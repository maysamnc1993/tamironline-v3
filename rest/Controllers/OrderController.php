<?php

namespace Tamir\Rest\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\RequestValidator;
use Rehub\Rest\Response;
use WP_REST_Request;
use Tamir\Notification\NotificationService;
use Tamir\Notification\NotificationTemplates;
use Tamir\Holidays\ScheduleValidator;
use Tamir\Holidays\TimeSlotsConfig;
use Tamir\Rest\Services\IdempotencyHandler;
use Tamir\Rest\Traits\JwtAuthTrait; // 🆕

use Tamir\Admin\ListManager;

/**
 * OrderController — هماهنگ با AuthController v2.2
 *
 * 🆕 تغییرات این نسخه:
 *  - استفاده از JwtAuthTrait برای احراز هویت
 *  - حذف const JWT_META_KEY
 *  - حذف private currentUserId() (از trait میاد)
 *
 * بقیه‌ی منطق (workflow، effective status، bulk insert و ...) دست‌نخورده.
 */
class OrderController extends Controller
{
    use JwtAuthTrait; // 🆕

    const POST_TYPE = 'orders';

    const STATUS_MAP = array(
        0   => 'pending',
        1   => 'scheduled',
        2   => 'in_progress',
        3   => 'suspended',
        4   => 'cancelled',
        5   => 'completed',
        100 => 'declined',
    );

    const STATUS_MAP_REVERSE = array(
        'pending'     => 0,
        'assigned'    => 0,
        'scheduled'   => 1,
        'in_progress' => 2,
        'suspended'   => 3,
        'cancelled'   => 4,
        'completed'   => 5,
        'declined'    => 100,
    );

    const ORDER_TYPES = array('repair', 'service');

    public function authorize(WP_REST_Request $r)
    {
        return $this->currentUserId($r) > 0;
    }

    public function rules(): array
    {
        return array();
    }
    public function messages(): array
    {
        return array();
    }

    // ══════════════════════════════════════════════════════════
    //  GET /tamir/v1/orders
    // ══════════════════════════════════════════════════════════
    public function index(WP_REST_Request $r)
    {
        $userId  = $this->currentUserId($r);
        $page    = max(1, absint($r->get_param('page')     ?: 1));
        $perPage = min(50, max(1, absint($r->get_param('per_page') ?: 10)));

        $statusFilter  = $r->get_param('status') ?: '';
        $numericStatus = $this->resolveStatusToInt($statusFilter);

        $userMobile = $this->resolveUserMobile($userId);
        $customerIds = $this->findAllCustomerIdsByMobile($userMobile, $userId);

        error_log(sprintf(
            '[TamirOnline DEBUG] userId=%d, mobile=%s, customerIds=%s',
            $userId,
            $userMobile ?: 'EMPTY',
            implode(',', $customerIds)
        ));

        $orMetaQuery = array('relation' => 'OR');

        if (!empty($customerIds)) {
            $orMetaQuery[] = array(
                'key'     => 'customer',
                'value'   => $customerIds,
                'compare' => 'IN',
                'type'    => 'NUMERIC',
            );
        }

        if (!empty($userMobile)) {
            $orMetaQuery[] = array(
                'key'     => 'mobile',
                'value'   => $userMobile,
                'compare' => '=',
            );
        }

        if (count($orMetaQuery) < 2) {
            $orMetaQuery[] = array(
                'key'     => 'customer',
                'value'   => $userId,
                'compare' => '=',
                'type'    => 'NUMERIC',
            );
        }

        $metaQuery = array('relation' => 'AND', $orMetaQuery);

        if ($numericStatus !== null) {
            $metaQuery[] = array(
                'key'     => 'status',
                'value'   => $numericStatus,
                'compare' => '=',
                'type'    => 'NUMERIC',
            );
        }

        $query = new \WP_Query(array(
            'post_type'      => self::POST_TYPE,
            'posts_per_page' => $perPage,
            'paged'          => $page,
            'post_status'    => array('publish', 'pending', 'draft', 'private', 'future'),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => $metaQuery,
        ));

        $orders = array_map(array($this, 'formatOrder'), $query->posts);
        return Response::paginate($orders, $query->found_posts, $page, $perPage);
    }

    private function findAllCustomerIdsByMobile($mobile, $fallbackId)
    {
        $ids = array((int) $fallbackId);
        if (empty($mobile)) return $ids;

        $metaKeys = array('mobile', 'tamir_phone', 'phone', 'billing_phone', 'user_phone');

        foreach ($metaKeys as $key) {
            $users = get_users(array(
                'meta_key'   => $key,
                'meta_value' => $mobile,
                'fields'     => 'ID',
            ));
            foreach ($users as $uid) {
                $ids[] = (int) $uid;
            }
        }

        $loginUser = get_user_by('login', $mobile);
        if ($loginUser) {
            $ids[] = (int) $loginUser->ID;
        }

        return array_values(array_unique(array_filter($ids)));
    }

    // ══════════════════════════════════════════════════════════
    //  POST /tamir/v1/orders
    // ══════════════════════════════════════════════════════════
    public function store(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        if (!$userId) {
            return Response::error('احراز هویت ناموفق.', 401);
        }

        $idemKey = IdempotencyHandler::getKey($r);

        if ($idemKey) {
            $cached = IdempotencyHandler::getCachedResponse($idemKey, $userId);
            if ($cached !== null) {
                return IdempotencyHandler::buildResponseFromCache($cached);
            }
            if (!IdempotencyHandler::acquireLock($idemKey, $userId)) {
                return Response::error(
                    'این درخواست در حال پردازش است.',
                    425,
                    array('code' => 'idempotency_in_progress')
                );
            }
        }

        $releaseAndError = function ($message, $status = 422, $extra = array()) use ($idemKey, $userId) {
            if ($idemKey) {
                IdempotencyHandler::releaseLock($idemKey, $userId);
            }
            return Response::error($message, $status, $extra);
        };

        try {
            $rateLimitKey = 'tamir_order_rate_' . $userId;
            $rateLimitWindow = 5 * MINUTE_IN_SECONDS;
            $rateLimitMax = 10;

            $bucket = get_transient($rateLimitKey);
            $now = time();

            if (!is_array($bucket) || ($now - $bucket['start']) >= $rateLimitWindow) {
                $bucket = array('start' => $now, 'count' => 0);
            }

            if ($bucket['count'] >= $rateLimitMax) {
                $remaining = $rateLimitWindow - ($now - $bucket['start']);
                return $releaseAndError(
                    sprintf('شما به سقف سفارش‌های ۵ دقیقه‌ای رسیده‌اید. لطفاً %d ثانیه دیگر تلاش کنید.', $remaining),
                    429,
                    array('retry_after' => $remaining)
                );
            }

            $validSlots = TimeSlotsConfig::validValues();
            $validator = new RequestValidator(
                array(
                    'order_type'            => array('required', 'string', 'in:' . implode(',', self::ORDER_TYPES)),
                    'device_id'             => array('required', 'int', 'min:1'),
                    'brand_id'              => array('required', 'int', 'min:1'),
                    'objection_description' => array('string', 'max:1000'),
                    'scheduled_date'        => array('required', 'string', 'regex:/^\d{4}-\d{2}-\d{2}$/'),
                    'scheduled_time'        => array('required', 'string', 'in:' . implode(',', $validSlots)),
                ),
                array(
                    'order_type.required'     => 'نوع سفارش الزامی است.',
                    'order_type.in'           => 'نوع سفارش باید repair یا service باشد.',
                    'device_id.required'      => 'دستگاه الزامی است.',
                    'brand_id.required'       => 'برند الزامی است.',
                    'scheduled_date.required' => 'تاریخ مراجعه الزامی است.',
                    'scheduled_date.regex'    => 'فرمت تاریخ صحیح نیست.',
                    'scheduled_time.required' => 'بازه زمانی الزامی است.',
                    'scheduled_time.in'       => 'بازه زمانی معتبر نیست.',
                )
            );

            if (!$validator->passes($r)) {
                return $releaseAndError(
                    'اطلاعات ورودی نامعتبر است.',
                    422,
                    array('errors' => $validator->toErrorResponse())
                );
            }

            $orderType = sanitize_text_field($r->get_param('order_type'));
            $deviceId  = absint($r->get_param('device_id'));
            $brandId   = absint($r->get_param('brand_id'));
            $objDesc   = mb_substr(sanitize_textarea_field($r->get_param('objection_description') ?: ''), 0, 1000);
            $dateRaw   = sanitize_text_field($r->get_param('scheduled_date'));
            $time      = sanitize_text_field($r->get_param('scheduled_time'));
            $objection = $this->sanitizeArray($r->get_param('objection') ?: array());

            if ($orderType === 'repair') {
                $trimmed = trim($objDesc);
                if (mb_strlen($trimmed) < 2) {
                    return $releaseAndError(
                        'توضیح مشکل برای سفارش تعمیر الزامی است.',
                        422,
                        array('errors' => array('objection_description' => array('توضیح مشکل باید حداقل ۲ کاراکتر باشد.')))
                    );
                }
            }

            if (empty($objection) && !empty($objDesc)) {
                $objection = array($objDesc);
            }

            if ($orderType === 'service') {
                $objection = $this->appendServiceAutoObjections($objection);
            }

            $scheduleValidator = new ScheduleValidator();
            if (!$scheduleValidator->validate($dateRaw, $time)) {
                $errorCode = $scheduleValidator->getErrorCode();
                $errorMsg  = $scheduleValidator->getErrorMessage();

                $errorField = in_array($errorCode, ['slot_invalid', 'slot_past', 'holiday_slot_closed'], true)
                    ? 'scheduled_time'
                    : 'scheduled_date';

                return $releaseAndError($errorMsg, 422, array(
                    'errors'     => array($errorField => array($errorMsg)),
                    'error_code' => $errorCode,
                ));
            }

            $dates = $scheduleValidator->getNormalizedDates();
            $dateJalali    = $dates['jalali'];
            $dateGregorian = $dates['gregorian'];

            $intro = mb_substr(sanitize_text_field($r->get_param('introduction') ?: 'اپلیکیشن'), 0, 100);

            $deviceTerm = $this->findTaxonomyTerm($deviceId, 'device');
            if (!$deviceTerm) {
                return $releaseAndError('دستگاه انتخاب‌شده معتبر نیست.', 422, array('device_id' => $deviceId));
            }

            $brandTerm = $this->findTaxonomyTerm($brandId, 'brand');
            if (!$brandTerm) {
                return $releaseAndError('برند انتخاب‌شده معتبر نیست.', 422, array('brand_id' => $brandId));
            }

            $addressData = $this->resolveAddress($r, $userId);
            if (is_wp_error($addressData)) {
                return $releaseAndError($addressData->get_error_message(), 422);
            }

            $mobile = $this->resolveUserMobile($userId);
            $phone = isset($addressData['phone']) ? sanitize_text_field($addressData['phone']) : '';

            $subscription = $userId + 10000;

            // --- محاسبه‌ی تاریخ ثبت سفارش به وقت تهران (میلادی برای post_date و شمسی برای متا) ---
            if (!function_exists('jdate')) {
                require_once __DIR__ . '/libs/jdf.php';
            }

            $tehranTz   = new \DateTimeZone('Asia/Tehran');
            $utcTz      = new \DateTimeZone('UTC');
            $nowTehran  = new \DateTime('now', $tehranTz);
            $nowUtc     = clone $nowTehran;
            $nowUtc->setTimezone($utcTz);

            $mysqlNow    = $nowTehran->format('Y-m-d H:i:s'); // میلادی به وقت تهران
            $mysqlNowGmt = $nowUtc->format('Y-m-d H:i:s');    // میلادی UTC

            $createdJalali     = jdate('Y/m/d H:i:s', $nowTehran->getTimestamp(), '', 'Asia/Tehran', 'en');
            $createdJalaliDate = jdate('Y/m/d',       $nowTehran->getTimestamp(), '', 'Asia/Tehran', 'en');
            $createdJalaliTime = jdate('H:i:s',       $nowTehran->getTimestamp(), '', 'Asia/Tehran', 'en');

            $postId = wp_insert_post(array(
                'post_title'    => 'سفارش جدید',
                'post_type'     => self::POST_TYPE,
                'post_status'   => 'draft',
                'post_author'   => $userId,
                'post_date'     => $mysqlNow,
                'post_date_gmt' => $mysqlNowGmt,
            ), true);

            if (is_wp_error($postId)) {
                error_log('[TamirOnline] store error: ' . $postId->get_error_message());
                return $releaseAndError('خطا در ثبت سفارش.', 500);
            }

            $trackingCode = (string) $postId;

            wp_set_post_terms($postId, array($deviceTerm->term_id), 'device');
            wp_set_post_terms($postId, array($brandTerm->term_id),  'brand');
            $this->assignLocationTerm($postId, isset($addressData['city'])     ? $addressData['city']     : '', 'city');
            $this->assignLocationTerm($postId, isset($addressData['province']) ? $addressData['province'] : '', 'state');

            $statusLog = array(array(
                'subject' => 'ثبت سفارش آنلاین',
                'content' => 'سفارش ' . $postId . ' از اپلیکیشن ثبت شد. کد پیگیری: #' . $trackingCode,
                'author'  => $userId,
                'date'    => jdate('Y/m/d H:i', $nowTehran->getTimestamp(), '', 'Asia/Tehran', 'en'),
                'status'  => 'ثبت سفارش',
            ));

            $metas = array(
                'customer'              => $userId,
                'subscription'          => $subscription,
                'mobile'                => $mobile,
                'phone'                 => $phone,
                'address'               => isset($addressData['full_address']) ? $addressData['full_address'] : '',
                'city_name'             => isset($addressData['city'])         ? $addressData['city']         : '',
                'province_name'         => isset($addressData['province'])     ? $addressData['province']     : '',
                'order_type'            => $orderType,
                'introduction'          => $intro,
                'objection'             => $objection,
                'objection_description' => $objDesc,
                'technician'            => '',
                'status'                => 0,
                'tracking_code'         => $trackingCode,
                'scheduled_date'           => $dateJalali,
                'scheduled_date_jalali'    => $dateJalali,
                'scheduled_date_gregorian' => $dateGregorian,
                'scheduled_time'           => $time,
                'device_id'             => $deviceTerm->term_id,
                'device_name'           => $deviceTerm->name,
                'device_slug'           => $deviceTerm->slug,
                'brand_id'              => $brandTerm->term_id,
                'brand_name'            => $brandTerm->name,
                'brand_slug'            => $brandTerm->slug,
                'send_sms_customer'     => 0,
                'send_sms_tec'          => 0,
                'order_description_content' => $statusLog,

                // تاریخ ثبت سفارش
                'created_at'             => $mysqlNow,          // میلادی به وقت تهران
                'created_at_gmt'         => $mysqlNowGmt,       // میلادی UTC
                'created_at_jalali'      => $createdJalali,     // شمسی کامل: 1404/02/16 14:23:05
                'created_at_jalali_date' => $createdJalaliDate, // فقط تاریخ شمسی
                'created_at_jalali_time' => $createdJalaliTime, // فقط ساعت
            );

            $this->bulkInsertPostMeta($postId, $metas);

            if (class_exists('\Tamir\Rest\Controllers\ProfileController')) {
                \Tamir\Rest\Controllers\ProfileController::invalidateStatsCache($userId);
            }

            if (method_exists(__CLASS__, 'invalidateOrderIdsCache')) {
                self::invalidateOrderIdsCache();
            }

            $bucket['count']++;
            $ttl = $rateLimitWindow - ($now - $bucket['start']);
            set_transient($rateLimitKey, $bucket, max(60, $ttl));

            wp_schedule_single_event(time(), 'tamir_order_post_create_async', array(
                'post_id'      => $postId,
                'user_id'      => $userId,
                'tracking'     => $trackingCode,
                'device_name'  => $deviceTerm->name,
                'brand_name'   => $brandTerm->name,
            ));

            $responseBody = array(
                'success' => true,
                'message' => 'سفارش با موفقیت ثبت شد.',
                'data'    => $this->formatOrder(get_post($postId)),
            );

            if ($idemKey) {
                IdempotencyHandler::storeResponse($idemKey, $userId, 201, $responseBody);
            }

            return Response::ok($responseBody, 201);
        } catch (\Throwable $e) {
            if ($idemKey) {
                IdempotencyHandler::releaseLock($idemKey, $userId);
            }

            error_log('[TamirOnline] store exception: ' . $e->getMessage());

            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                throw $e;
            }

            return Response::error(
                'خطای داخلی سرور. لطفاً لحظاتی دیگر تلاش کنید.',
                500
            );
        }
    }

    private function bulkInsertPostMeta($postId, array $metas)
    {
        if (empty($metas)) return;

        global $wpdb;

        $values = array();
        $params = array();

        foreach ($metas as $key => $value) {
            $key = wp_unslash($key);

            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            } else {
                $value = (string) $value;
            }

            $values[] = '(%d, %s, %s)';
            $params[] = (int) $postId;
            $params[] = $key;
            $params[] = $value;
        }

        $sql = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES "
            . implode(', ', $values);

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $wpdb->query($wpdb->prepare($sql, $params));

        wp_cache_delete($postId, 'post_meta');
    }

    public function show(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        $postId = absint($r->get_param('id'));

        $post = get_post($postId);
        if (!$this->isValidOrder($post)) return Response::error('سفارش یافت نشد.', 404);
        if (!$this->userOwnsOrder($post, $userId)) return Response::error('دسترسی ممنوع.', 403);

        return Response::ok(array('success' => true, 'data' => $this->formatOrder($post)));
    }

    public function cancel(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        $postId = absint($r->get_param('id'));

        $post = get_post($postId);
        if (!$this->isValidOrder($post)) return Response::error('سفارش یافت نشد.', 404);
        if (!$this->userOwnsOrder($post, $userId)) return Response::error('دسترسی ممنوع.', 403);

        $currentStatus = (int) get_post_meta($postId, 'status', true);
        if ($currentStatus !== 0) {
            $statusLabel = isset(self::STATUS_MAP[$currentStatus]) ? self::STATUS_MAP[$currentStatus] : 'unknown';
            return Response::error(
                'فقط سفارش‌های در انتظار قابل لغو هستند.',
                422,
                array('current_status' => $statusLabel)
            );
        }

        $reasonId    = sanitize_text_field((string) ($r->get_param('reason_id') ?: ''));
        $reasonOther = sanitize_textarea_field((string) ($r->get_param('reason_other') ?: ''));
        $reasonOther = mb_substr(trim($reasonOther), 0, 500);

        if ($reasonId === '' && $reasonOther === '') {
            return Response::error(
                'دلیل لغو سفارش الزامی است.',
                422,
                array('errors' => array('reason' => array('لطفاً دلیل لغو را انتخاب کنید.')))
            );
        }

        $reasonSnapshot = null;
        if ($reasonId !== '') {
            $reasonSnapshot = ListManager::snapshot('cancel_reasons', $reasonId);
            if (!$reasonSnapshot) {
                return Response::error(
                    'دلیل انتخاب‌شده معتبر نیست.',
                    422,
                    array('errors' => array('reason_id' => array('این دلیل دیگر در دسترس نیست.')))
                );
            }
        }

        if ($reasonId === '' && mb_strlen($reasonOther) < 3) {
            return Response::error(
                'توضیح دلیل لغو باید حداقل ۳ کاراکتر باشد.',
                422,
                array('errors' => array('reason_other' => array('توضیح را کامل‌تر بنویسید.')))
            );
        }

        update_post_meta($postId, 'status', 4);

        if ($reasonSnapshot) {
            update_post_meta($postId, 'cancel_reason', $reasonSnapshot);
        } else {
            delete_post_meta($postId, 'cancel_reason');
        }

        if ($reasonOther !== '') {
            update_post_meta($postId, 'cancel_reason_other', $reasonOther);
        } else {
            delete_post_meta($postId, 'cancel_reason_other');
        }

        if (class_exists('\Tamir\Rest\Controllers\ProfileController')) {
            \Tamir\Rest\Controllers\ProfileController::invalidateStatsCache($userId);
        }

        $trackingCode = get_post_meta($postId, 'tracking_code', true) ?: ((string) $postId);

        $logContent = 'سفارش #' . $trackingCode . ' توسط کاربر لغو شد.';
        $logReasonParts = array();
        if ($reasonSnapshot) $logReasonParts[] = $reasonSnapshot['label'];
        if ($reasonOther !== '') $logReasonParts[] = $reasonOther;
        $logContent .= ' دلیل: ' . (empty($logReasonParts) ? 'سایر' : implode(' — ', $logReasonParts));

        $this->addStatusLog($postId, 'لغو سفارش', $logContent, $userId, 'کنسل شده');

        try {
            $svc = new NotificationService();
            $svc->send(NotificationTemplates::ORDER_CANCELLED, $userId, $postId, array(
                'order_code' => $trackingCode,
                'device'     => get_post_meta($postId, 'device_name', true) ?: '',
            ));
        } catch (\Throwable $e) {
            error_log('[TamirOnline] cancel notification error: ' . $e->getMessage());
        }

        return Response::ok(array(
            'success' => true,
            'message' => 'سفارش با موفقیت لغو شد.',
            'data'    => $this->formatOrder(get_post($postId)),
        ));
    }

    // 🗑️ private function currentUserId() حذف شد — از JwtAuthTrait میاد

    // ══════════════════════════════════════════════════════════
    //  Helpers
    // ══════════════════════════════════════════════════════════

    private function resolveUserMobile($userId)
    {
        $keys = array('tamir_phone', 'mobile', 'phone', 'billing_phone', 'mobile_number');
        foreach ($keys as $k) {
            $v = get_user_meta($userId, $k, true);
            if ($v) return $v;
        }
        $udata = get_userdata($userId);
        if ($udata && preg_match('/^09[0-9]{9}$/', $udata->user_login)) {
            return $udata->user_login;
        }
        return '';
    }

    private function assignLocationTerm($postId, $name, $taxonomy)
    {
        $name = mb_substr(trim($name), 0, 60);
        if (!$name) return;

        $slug = sanitize_title($name);
        $term = get_term_by('slug', $slug, $taxonomy);
        if (!$term) $term = get_term_by('name', $name, $taxonomy);
        if (!$term || is_wp_error($term)) return;

        wp_set_post_terms($postId, array($term->term_id), $taxonomy);
    }

    private function termWithMeta($termId, $name, $slug)
    {
        $termId = (int) $termId;
        return array(
            'id'    => $termId,
            'name'  => $name ?: '—',
            'slug'  => $slug ?: '',
            'icon'  => $termId > 0 ? (get_term_meta($termId, 'tamir_icon',  true) ?: '') : '',
            'image' => $termId > 0 ? (get_term_meta($termId, 'tamir_image', true) ?: '') : '',
        );
    }

    private function computeEffectiveStatusKey($rawStatus, $techId)
    {
        $rawStatus = (int) $rawStatus;
        $techId    = (int) $techId;

        if ($rawStatus === 0 && $techId > 0) {
            return 'assigned';
        }

        return isset(self::STATUS_MAP[$rawStatus])
            ? self::STATUS_MAP[$rawStatus]
            : 'pending';
    }

    private function computeEffectiveStatusInt($rawStatus, $techId)
    {
        $key = $this->computeEffectiveStatusKey($rawStatus, $techId);
        if ($key === 'assigned') {
            return 10001;
        }
        return (int) $rawStatus;
    }

    private function formatOrder($post)
    {
        $id = $post->ID;

        $rawStatus = (int) get_post_meta($id, 'status', true);
        $techId    = (int) get_post_meta($id, 'technician', true);
        $statusStr = $this->computeEffectiveStatusKey($rawStatus, $techId);
        $statusInt = $this->computeEffectiveStatusInt($rawStatus, $techId);

        $deviceId   = (int) get_post_meta($id, 'device_id',   true);
        $deviceName =       get_post_meta($id, 'device_name', true);
        $deviceSlug =       get_post_meta($id, 'device_slug', true);
        if (!$deviceName) {
            $t = wp_get_post_terms($id, 'device');
            if (!empty($t) && !is_wp_error($t)) {
                $deviceId   = $t[0]->term_id;
                $deviceName = $t[0]->name;
                $deviceSlug = $t[0]->slug;
            }
        }

        $brandId   = (int) get_post_meta($id, 'brand_id',   true);
        $brandName =       get_post_meta($id, 'brand_name', true);
        $brandSlug =       get_post_meta($id, 'brand_slug', true);
        if (!$brandName) {
            $t = wp_get_post_terms($id, 'brand');
            if (!empty($t) && !is_wp_error($t)) {
                $brandId   = $t[0]->term_id;
                $brandName = $t[0]->name;
                $brandSlug = $t[0]->slug;
            }
        }

        $cityName     = get_post_meta($id, 'city_name',     true) ?: '';
        $provinceName = get_post_meta($id, 'province_name', true) ?: '';
        $cityT  = wp_get_post_terms($id, 'city');
        $stateT = wp_get_post_terms($id, 'state');
        if (!empty($cityT)  && !is_wp_error($cityT))  $cityName     = $cityT[0]->name;
        if (!empty($stateT) && !is_wp_error($stateT)) $provinceName = $stateT[0]->name;

        $technician = null;
        if ($techId > 0) {
            $tu = get_userdata($techId);
            if ($tu) {
                $technician = array(
                    'id'     => $techId,
                    'name'   => get_user_meta($techId, 'first_name', true) ?: $tu->display_name,
                    'rating' => (float)(get_user_meta($techId, 'tamir_rating', true) ?: 4.5),
                );
            }
        }

        $price     = get_post_meta($id, 'price_customer', true);
        $objection = get_post_meta($id, 'objection', true);

        $scheduledDate = get_post_meta($id, 'scheduled_date', true) ?: '';
        $scheduledGreg = get_post_meta($id, 'scheduled_date_gregorian', true) ?: '';

        $cancelReason = $this->getCancelReasonForOrder($id);
        $cancelOther  = get_post_meta($id, 'cancel_reason_other', true);
        if (!$cancelOther) {
            $cancelOther = get_post_meta($id, 'cancel_desc_other', true) ?: '';
        }

        return array(
            'id'                  => $id,
            'tracking_code'       => get_post_meta($id, 'tracking_code', true) ?: (string) $id,
            'status'              => $statusStr,
            'status_int'          => $statusInt,
            'order_type'          => get_post_meta($id, 'order_type', true) ?: 'repair',
            'service_category'    => $this->termWithMeta($deviceId, $deviceName, $deviceSlug),
            'brand'               => $this->termWithMeta($brandId,  $brandName,  $brandSlug),
            'problem_description' => get_post_meta($id, 'objection_description', true) ?: '',
            'objection'           => is_array($objection) ? $objection : array(),
            'address'             => array(
                'full_address' => get_post_meta($id, 'address', true) ?: '',
                'city'         => $cityName,
                'province'     => $provinceName,
                'phone'        => get_post_meta($id, 'phone', true) ?: '',
            ),
            'scheduled_date'           => $scheduledDate,
            'scheduled_date_gregorian' => $scheduledGreg,
            'scheduled_time'           => get_post_meta($id, 'scheduled_time', true) ?: '',
            'technician'          => $technician,
            'price'               => $price ? (int) $price : null,
            'subscription'        => get_post_meta($id, 'subscription', true) ?: '',
            'created_at'          => $post->post_date,
            'updated_at'          => $post->post_modified,
            'cancel_reason'       => $cancelReason,
            'cancel_reason_other' => $cancelOther,
        );
    }

    private function resolveAddress(WP_REST_Request $r, $userId)
    {
        $addressId = sanitize_text_field((string) ($r->get_param('address_id') ?: ''));

        if ($addressId !== '') {
            $optKey = 'tamir_addr_' . $userId;
            $json   = get_option($optKey, '[]');
            $saved  = is_string($json) ? (json_decode($json, true) ?: array()) : (is_array($json) ? $json : array());

            if (empty($saved)) {
                $saved = (array)(get_user_meta($userId, 'tamir_addresses', true) ?: array());
            }

            foreach ($saved as $addr) {
                if (!is_array($addr) || !isset($addr['id'])) continue;
                if ((string)$addr['id'] === $addressId) {
                    return $addr;
                }
            }
            return new \WP_Error('address_not_found', 'آدرس ذخیره‌شده یافت نشد.');
        }

        $inline = $r->get_param('address');
        if (is_array($inline) && !empty($inline['full_address'])) {
            return array(
                'full_address' => mb_substr(sanitize_textarea_field($inline['full_address']), 0, 300),
                'city'         => mb_substr(sanitize_text_field(isset($inline['city'])        ? $inline['city']        : ''), 0, 60),
                'province'     => mb_substr(sanitize_text_field(isset($inline['province'])    ? $inline['province']    : ''), 0, 60),
                'postal_code'  => preg_replace('/[^0-9]/', '', isset($inline['postal_code']) ? $inline['postal_code'] : ''),
                'phone'        => sanitize_text_field(isset($inline['phone']) ? $inline['phone'] : ''),
            );
        }

        return new \WP_Error('address_required', 'آدرس الزامی است.');
    }

    private function isValidOrder($post)
    {
        return $post instanceof \WP_Post
            && $post->post_type === self::POST_TYPE
            && in_array($post->post_status, array('publish', 'pending', 'draft', 'private', 'future'), true);
    }

    private function userOwnsOrder($post, $userId)
    {
        if (current_user_can('administrator')) return true;

        $customer = (int) get_post_meta($post->ID, 'customer', true);
        if ($customer === $userId) return true;

        $userMobile = $this->resolveUserMobile($userId);
        if (!empty($userMobile)) {
            $orderMobile = get_post_meta($post->ID, 'mobile', true);
            if ($orderMobile === $userMobile) return true;
        }

        if ((int) $post->post_author === $userId) return true;

        $customerIds = $this->findAllCustomerIdsByMobile($userMobile, $userId);
        if (in_array($customer, $customerIds, true)) return true;

        return false;
    }

    private function resolveStatusToInt($status)
    {
        if ($status === '' || $status === null) return null;
        if (is_string($status) && isset(self::STATUS_MAP_REVERSE[$status])) return self::STATUS_MAP_REVERSE[$status];
        if (is_numeric($status) && isset(self::STATUS_MAP[(int)$status])) return (int)$status;
        return null;
    }

    private function sanitizeArray($input, $maxItems = 20, $maxLength = 100)
    {
        if (!is_array($input)) return array();
        $result = array();
        $count  = 0;
        foreach ($input as $item) {
            if (++$count > $maxItems) break;
            $clean = mb_substr(sanitize_text_field((string)$item), 0, $maxLength);
            if ($clean !== '') $result[] = $clean;
        }
        return $result;
    }

    const SERVICE_AUTO_OBJECTION_IDS = array(217, 219);

    private function appendServiceAutoObjections(array $existing): array
    {
        $list = get_option('objectionsList', array());
        if (!is_array($list)) $list = array();

        $fallback = array(
            217 => 'سرویس کلی دستگاه',
            219 => 'نصب غیرگارانتی',
        );

        foreach (self::SERVICE_AUTO_OBJECTION_IDS as $id) {
            $raw = isset($list[$id]) ? (string) $list[$id] : '';
            $name = trim($raw);
            if ($name === '') {
                $name = isset($fallback[$id]) ? $fallback[$id] : '';
            }
            if ($name === '') continue;

            if (!in_array($name, $existing, true)) {
                $existing[] = $name;
            }
        }

        return $existing;
    }

    private function findTaxonomyTerm($id, $taxonomy)
    {
        $id = (int)$id;

        $term = get_term($id, $taxonomy);
        if ($term && !is_wp_error($term)) return $term;

        $byMeta = get_terms(array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'meta_query' => array(
                array('key' => 'tamir_legacy_id', 'value' => $id, 'compare' => '=', 'type' => 'NUMERIC'),
            ),
        ));
        if (!is_wp_error($byMeta) && !empty($byMeta)) return $byMeta[0];

        $optionKey = ($taxonomy === 'device') ? 'tamir_service_categories' : 'tamir_brands';
        $items     = (array) get_option($optionKey, array());

        foreach ($items as $item) {
            if (!isset($item['id']) || (int)$item['id'] !== $id) continue;
            $name = isset($item['name']) ? sanitize_text_field($item['name']) : '';
            $slug = isset($item['slug']) ? sanitize_title($item['slug'])      : sanitize_title($name);
            if (!$name) return null;

            $existing = get_term_by('name', $name, $taxonomy) ?: get_term_by('slug', $slug, $taxonomy);
            if ($existing && !is_wp_error($existing)) {
                update_term_meta($existing->term_id, 'tamir_legacy_id', $id);
                return $existing;
            }

            if (taxonomy_exists($taxonomy)) {
                $result = wp_insert_term($name, $taxonomy, array('slug' => $slug));
                if (!is_wp_error($result)) {
                    update_term_meta($result['term_id'], 'tamir_legacy_id', $id);
                    return get_term($result['term_id'], $taxonomy);
                }
            }
            return null;
        }

        error_log("[TamirOnline] Term not found: id={$id}, taxonomy={$taxonomy}.");
        return null;
    }

    private function addStatusLog($postId, $subject, $content, $authorId, $title)
    {
        $logs   = get_post_meta($postId, 'order_description_content', true);
        $logs   = is_array($logs) ? $logs : array();
        $logs[] = array(
            'subject' => $subject,
            'content' => $content,
            'author'  => $authorId,
            'date'    => current_time('Y/m/d H:i'),
            'status'  => $title,
        );
        update_post_meta($postId, 'order_description_content', $logs);
    }

    public function version(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        if (!$userId) {
            return Response::error('احراز هویت ناموفق.', 401);
        }

        $postId = absint($r->get_param('id'));
        $post = get_post($postId);

        if (!$this->isValidOrder($post)) {
            return Response::error('سفارش یافت نشد.', 404);
        }
        if (!$this->userOwnsOrder($post, $userId)) {
            return Response::error('دسترسی ممنوع.', 403);
        }

        $rawStatus = (int) get_post_meta($postId, 'status', true);
        $techId    = (int) get_post_meta($postId, 'technician', true);
        $statusStr = $this->computeEffectiveStatusKey($rawStatus, $techId);
        $statusInt = $this->computeEffectiveStatusInt($rawStatus, $techId);

        $priceCustomer = (string) get_post_meta($postId, 'price_customer', true);

        $hashSource = $statusInt . '|' . $techId . '|' . $priceCustomer . '|' . $post->post_modified;
        $hash = substr(md5($hashSource), 0, 12);

        return Response::ok(array(
            'success'    => true,
            'id'         => $postId,
            'status'     => $statusStr,
            'status_int' => $statusInt,
            'updated_at' => $post->post_modified,
            'hash'       => $hash,
        ));
    }

    private function getCancelReasonForOrder($postId)
    {
        $reason = get_post_meta($postId, 'cancel_reason', true);
        if (is_array($reason) && isset($reason['label'])) {
            return array(
                'id'    => isset($reason['id']) ? (string) $reason['id'] : '',
                'label' => (string) $reason['label'],
            );
        }

        $oldDesc = get_post_meta($postId, 'cancel_desc', true);
        if ($oldDesc) {
            return array('id' => '', 'label' => (string) $oldDesc);
        }
        return null;
    }
}
