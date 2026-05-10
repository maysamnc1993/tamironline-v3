<?php

namespace Tamir\Rest\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\RequestValidator;
use Rehub\Rest\Response;
use WP_REST_Request;
use Tamir\Rest\Traits\JwtAuthTrait; // 🆕

/**
 * ProfileController — هماهنگ با AuthController v2.2
 *
 * 🆕 تغییرات این نسخه:
 *  - استفاده از JwtAuthTrait
 *  - حذف const JWT_META_KEY
 *  - حذف private currentUserId()
 *
 * بقیه‌ی منطق (stats query بهینه، cache invalidation و ...) دست‌نخورده.
 */
final class ProfileController extends Controller
{
    use JwtAuthTrait; // 🆕

    const CACHE_STATS_TTL          = 60;
    const CACHE_CUSTOMER_IDS_TTL   = 300;
    const CACHE_STATS_PREFIX       = 'tamir_profile_stats_';
    const CACHE_CUSTOMER_IDS_PREFIX = 'tamir_profile_cids_';

    const STATUS_PENDING     = 0;
    const STATUS_ASSIGNED    = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_SUSPENDED   = 3;
    const STATUS_CANCELLED   = 4;
    const STATUS_COMPLETED   = 5;

    public function authorize(WP_REST_Request $r): bool
    {
        return $this->currentUserId($r) > 0;
    }

    public function rules(): array
    {
        return [];
    }
    public function messages(): array
    {
        return [];
    }

    public function show(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        $user   = get_userdata($userId);

        if (!$user) {
            return Response::error('کاربر یافت نشد.', 404);
        }

        return Response::ok([
            'success' => true,
            'data'    => $this->formatProfile($user),
        ]);
    }

    public function update(WP_REST_Request $r)
    {
        $validator = new RequestValidator(
            [
                'name'  => ['sometimes', 'string', 'min:2', 'max:60'],
                'email' => ['sometimes', 'string'],
            ],
            [
                'name.min' => 'نام باید حداقل ۲ کاراکتر باشد.',
                'name.max' => 'نام نباید بیشتر از ۶۰ کاراکتر باشد.',
            ]
        );

        if (!$validator->passes($r)) {
            return Response::error('اطلاعات نامعتبر است.', 422, [
                'errors' => $validator->toErrorResponse(),
            ]);
        }

        $userId = $this->currentUserId($r);
        $data   = ['ID' => $userId];

        $name  = mb_substr(sanitize_text_field((string) ($r->get_param('name') ?: '')), 0, 60);
        $email = sanitize_email((string) ($r->get_param('email') ?: ''));

        if ($name !== '') {
            $data['display_name'] = $name;
            $data['first_name']   = $name;
            update_user_meta($userId, 'first_name', $name);
        }

        if ($email !== '') {
            if (!is_email($email)) {
                return Response::error('آدرس ایمیل معتبر نیست.', 422, [
                    'errors' => ['email' => ['فرمت ایمیل صحیح نیست.']],
                ]);
            }

            $existing = get_user_by('email', $email);
            if ($existing && (int) $existing->ID !== (int) $userId) {
                return Response::error('این آدرس ایمیل قبلاً استفاده شده است.', 409, [
                    'errors' => ['email' => ['این ایمیل قبلاً ثبت شده است.']],
                ]);
            }
            $data['user_email'] = $email;
        }

        $result = wp_update_user($data);

        if (is_wp_error($result)) {
            error_log('[Tamir Profile] wp_update_user: ' . $result->get_error_message());
            return Response::error('خطا در بروزرسانی پروفایل.', 500);
        }

        return Response::ok([
            'success' => true,
            'message' => 'پروفایل با موفقیت بروزرسانی شد.',
            'data'    => $this->formatProfile(get_userdata($userId)),
        ]);
    }

    public static function invalidateStatsCache(int $userId): void
    {
        if ($userId <= 0) return;
        delete_transient(self::CACHE_STATS_PREFIX . $userId);
    }

    public static function invalidateCustomerIdsCache(string $mobile, int $userId): void
    {
        if ($mobile === '' || $userId <= 0) return;
        $key = self::CACHE_CUSTOMER_IDS_PREFIX . md5($mobile . '|' . $userId);
        delete_transient($key);
    }

    // 🗑️ private function currentUserId() حذف شد — از JwtAuthTrait میاد

    // ══════════════════════════════════════════════════════════
    //  Helpers (دست‌نخورده)
    // ══════════════════════════════════════════════════════════

    private function formatProfile(\WP_User $user): array
    {
        $userId = (int) $user->ID;

        $name = $this->resolveUserName($user);

        $phone = (string) (
            get_user_meta($userId, 'tamir_phone', true)
            ?: get_user_meta($userId, 'mobile', true)
            ?: ''
        );

        $email = null;
        if ($user->user_email && strpos($user->user_email, '@tamir.local') === false) {
            $email = $user->user_email;
        }

        $stats = $this->getOrderStats($userId, $phone);

        $subscription = (string) (
            get_user_meta($userId, 'subscription', true)
            ?: ($userId + 10000)
        );

        return [
            'id'                 => $userId,
            'name'               => $name,
            'phone'              => $phone,
            'email'              => $email,
            'avatar'             => get_avatar_url($userId, ['size' => 192]),
            'subscription'       => $subscription,

            'orders_count'       => $stats['total'],
            'completed_count'    => $stats['completed'],
            'pending_count'      => $stats['pending'],
            'in_progress_count'  => $stats['in_progress'],
            'cancelled_count'    => $stats['cancelled'],

            'registered_at'      => $user->user_registered,
        ];
    }

    private function getOrderStats(int $userId, string $phone): array
    {
        $zero = [
            'total'       => 0,
            'pending'     => 0,
            'completed'   => 0,
            'in_progress' => 0,
            'cancelled'   => 0,
        ];

        $cacheKey = self::CACHE_STATS_PREFIX . $userId;
        $cached   = get_transient($cacheKey);
        if (is_array($cached) && isset($cached['total'])) {
            return $cached;
        }

        $customerIds = $this->findAllCustomerIdsByMobile($phone, $userId);

        global $wpdb;

        $unionParts = [];
        $params = [];

        if (!empty($customerIds)) {
            $placeholders = implode(',', array_fill(0, count($customerIds), '%d'));
            $unionParts[] = "
                SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key = 'customer'
                  AND meta_value IN ($placeholders)
            ";
            foreach ($customerIds as $cid) {
                $params[] = (int) $cid;
            }
        }

        if ($phone !== '') {
            $unionParts[] = "
                SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key = 'mobile'
                  AND meta_value = %s
            ";
            $params[] = $phone;
        }

        if (empty($unionParts)) {
            $unionParts[] = "
                SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key = 'customer'
                  AND meta_value = %d
            ";
            $params[] = $userId;
        }

        $userPostsSubquery = '(' . implode(' UNION ', $unionParts) . ')';

        $sql = "
            SELECT pm_status.meta_value AS status, COUNT(DISTINCT p.ID) AS cnt
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm_status
                ON pm_status.post_id = p.ID
               AND pm_status.meta_key = 'status'
            WHERE p.ID IN $userPostsSubquery
              AND p.post_type = 'orders'
              AND p.post_status IN ('publish','pending','draft','private','future')
            GROUP BY pm_status.meta_value
        ";

        $prepared = $wpdb->prepare($sql, $params);
        $rows = $wpdb->get_results($prepared, ARRAY_A);

        if ($rows === null) {
            error_log('[Tamir Profile] DB error in getOrderStats: ' . $wpdb->last_error);
            return $zero;
        }

        $stats = $zero;
        foreach ($rows as $row) {
            $status = (int) $row['status'];
            $cnt    = (int) $row['cnt'];
            $stats['total'] += $cnt;

            switch ($status) {
                case self::STATUS_PENDING:
                    $stats['pending'] += $cnt;
                    break;
                case self::STATUS_COMPLETED:
                    $stats['completed'] += $cnt;
                    break;
                case self::STATUS_ASSIGNED:
                case self::STATUS_IN_PROGRESS:
                case self::STATUS_SUSPENDED:
                    $stats['in_progress'] += $cnt;
                    break;
                case self::STATUS_CANCELLED:
                    $stats['cancelled'] += $cnt;
                    break;
            }
        }

        set_transient($cacheKey, $stats, self::CACHE_STATS_TTL);

        return $stats;
    }

    private function findAllCustomerIdsByMobile(string $mobile, int $fallbackId): array
    {
        $ids = [$fallbackId];

        if ($mobile === '') return $ids;

        $cacheKey = self::CACHE_CUSTOMER_IDS_PREFIX . md5($mobile . '|' . $fallbackId);
        $cached   = get_transient($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        global $wpdb;

        $sql = "
            SELECT DISTINCT user_id AS id
            FROM {$wpdb->usermeta}
            WHERE meta_key IN ('mobile','tamir_phone','phone','billing_phone','user_phone')
              AND meta_value = %s

            UNION

            SELECT ID AS id
            FROM {$wpdb->users}
            WHERE user_login = %s
        ";

        $prepared = $wpdb->prepare($sql, $mobile, $mobile);
        $rows = $wpdb->get_col($prepared);

        if ($rows === null) {
            error_log('[Tamir Profile] DB error in findAllCustomerIdsByMobile: ' . $wpdb->last_error);
            return $ids;
        }

        foreach ($rows as $uid) {
            $ids[] = (int) $uid;
        }

        $result = array_values(array_unique(array_filter($ids)));

        set_transient($cacheKey, $result, self::CACHE_CUSTOMER_IDS_TTL);

        return $result;
    }

    private function resolveUserName(\WP_User $user): string
    {
        $firstName = trim((string) get_user_meta($user->ID, 'first_name', true));
        $lastName  = trim((string) get_user_meta($user->ID, 'last_name', true));

        if ($firstName !== '' || $lastName !== '') {
            $fullName = trim($firstName . ' ' . $lastName);
            if ($fullName !== '') return $fullName;
        }

        $displayName = trim((string) $user->display_name);
        $phone = (string) (
            get_user_meta($user->ID, 'tamir_phone', true)
            ?: get_user_meta($user->ID, 'mobile', true)
        );

        $isGeneric = $displayName === ''
            || $displayName === $user->user_login
            || $displayName === $phone
            || preg_match('/^09\d{9}$/', $displayName)
            || strpos($displayName, 'tamir_') === 0;

        if (!$isGeneric) return $displayName;

        return $displayName ?: $user->user_login;
    }
}
