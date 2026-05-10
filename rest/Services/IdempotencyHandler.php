<?php

namespace Tamir\Rest\Services;

use WP_REST_Request;
use WP_REST_Response;

/**
 * IdempotencyHandler — جلوگیری از پردازش تکراری درخواست‌ها
 *
 * مسیر: wp-content/themes/weblino/rest/Services/IdempotencyHandler.php
 *
 * کاربرد:
 *   روی operations حساس مثل ثبت سفارش، در صورت ناپایداری شبکه،
 *   کلاینت ممکنه یک درخواست رو دو بار بفرسته (یکی timeout شده،
 *   یکی واقعاً به سرور رسیده). این کلاس جلوی duplicate رو می‌گیره.
 *
 * نحوه کار:
 *   1. کلاینت در هر POST یک هدر "Idempotency-Key" با UUID می‌فرسته
 *   2. سرور قبل از پردازش، چک می‌کنه آیا این key قبلاً پردازش شده
 *   3. اگه شده → همون response قبلی رو برمی‌گردونه (بدون اجرای دوباره)
 *   4. اگه نشده → پردازش می‌کنه + نتیجه رو ذخیره می‌کنه (24 ساعت)
 *
 * استفاده:
 *   use Tamir\Rest\Services\IdempotencyHandler;
 *
 *   $key = IdempotencyHandler::getKey($request);
 *   if ($key) {
 *       $cached = IdempotencyHandler::getCachedResponse($key, $userId);
 *       if ($cached !== null) {
 *           return IdempotencyHandler::buildResponseFromCache($cached);
 *       }
 *       IdempotencyHandler::acquireLock($key, $userId);
 *   }
 *   // ... پردازش ...
 *   if ($key) {
 *       IdempotencyHandler::storeResponse($key, $userId, 201, $body);
 *   }
 */
final class IdempotencyHandler
{
    /** Header name */
    const HEADER = 'Idempotency-Key';

    /** TTL: 24 ساعت — کلید بعد از این زمان expire می‌شه */
    const CACHE_TTL = 86400;

    /** Prefix برای transient keys */
    const PREFIX = 'tamir_idem_';

    /** Lock TTL — برای جلوگیری از race condition */
    const LOCK_TTL = 60;

    /**
     * استخراج Idempotency Key از header.
     * Format: 8-128 کاراکتر alphanumeric با - / _
     */
    public static function getKey(WP_REST_Request $request)
    {
        $key = $request->get_header(self::HEADER);
        if (!$key) {
            $key = $request->get_header('idempotency_key');
        }

        if (!$key) return null;

        $key = trim($key);

        if (!preg_match('/^[a-zA-Z0-9_\-]{8,128}$/', $key)) {
            return null;
        }

        return $key;
    }

    /**
     * ساخت transient key — userId اضافه می‌کنه برای جلوگیری از مشترک شدن
     * cache بین کاربران مختلف.
     */
    private static function buildCacheKey($idemKey, $userId)
    {
        return self::PREFIX . $userId . '_' . md5($idemKey);
    }

    /**
     * چک کن آیا این کلید قبلاً پردازش شده.
     *
     * @return array|null  ['status' => int, 'body' => mixed]  یا null اگر تازه‌ست
     */
    public static function getCachedResponse($idemKey, $userId)
    {
        $cacheKey = self::buildCacheKey($idemKey, $userId);
        $cached = get_transient($cacheKey);

        if (!is_array($cached)) {
            return null;
        }

        // اگه در حالت "lock" هست، یعنی هنوز در حال پردازشه
        if (isset($cached['_lock']) && $cached['_lock'] === true) {
            return array(
                'status' => 425, // Too Early
                'body'   => array(
                    'success' => false,
                    'message' => 'این درخواست در حال پردازش است. لطفاً چند لحظه صبر کنید.',
                    'code'    => 'idempotency_in_progress',
                ),
            );
        }

        return $cached;
    }

    /**
     * Lock زدن روی یک key. اگه قبلاً lock یا response ذخیره شده باشه،
     * false برمی‌گردونه.
     */
    public static function acquireLock($idemKey, $userId)
    {
        $cacheKey = self::buildCacheKey($idemKey, $userId);

        if (get_transient($cacheKey) !== false) {
            return false;
        }

        return set_transient(
            $cacheKey,
            array('_lock' => true, '_locked_at' => time()),
            self::LOCK_TTL
        );
    }

    /**
     * ذخیره‌ی response نهایی — جایگزین lock می‌شه.
     */
    public static function storeResponse($idemKey, $userId, $status, $body)
    {
        $cacheKey = self::buildCacheKey($idemKey, $userId);
        set_transient(
            $cacheKey,
            array(
                'status'    => $status,
                'body'      => $body,
                'stored_at' => time(),
            ),
            self::CACHE_TTL
        );
    }

    /**
     * در صورت بروز خطا، lock رو آزاد کن تا کاربر بتونه retry کنه.
     */
    public static function releaseLock($idemKey, $userId)
    {
        $cacheKey = self::buildCacheKey($idemKey, $userId);
        $cached = get_transient($cacheKey);

        if (is_array($cached) && isset($cached['_lock']) && $cached['_lock'] === true) {
            delete_transient($cacheKey);
        }
    }

    /**
     * تبدیل cached response به WP_REST_Response.
     */
    public static function buildResponseFromCache(array $cached)
    {
        return new WP_REST_Response($cached['body'], $cached['status']);
    }
}
