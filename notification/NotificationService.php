<?php

namespace Tamir\Notification;

/**
 * NotificationService — سرویس مرکزی اعلان‌ها
 *
 * استفاده:
 *   $svc = new NotificationService();
 *   $svc->send(NotificationTemplates::ORDER_CREATED, $userId, $orderId, [
 *       'device' => 'لباسشویی', 'order_code' => 'TM9X3K'
 *   ]);
 *
 * ──────────────────────────────────────────────────────────────────
 *  🔒 SECURITY NOTE — order_description_content isolation
 *  از این نسخه، meta داخلی `order_description_content` (لاگ وضعیت
 *  داخلی سفارش که اپراتور می‌نویسد) دیگر به هیچ وجه در API
 *  notifications نمایش داده نمی‌شود. متد getOrderMessagesAsNotifications
 *  به‌صورت private باقی‌مانده و هیچ مسیر public به آن وصل نیست.
 *  کاربر فقط notification های رسمی (از NotificationTemplates) را می‌بیند.
 * ──────────────────────────────────────────────────────────────────
 */
class NotificationService
{
    const POST_TYPE = 'tamir_notif';

    private $sms;

    // ── تبدیل تاریخ میلادی به شمسی (تهران) با jdf.php ──────
    private function toJalali(string $datetime): string
    {
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
            return '';
        }
        try {
            $tz = new \DateTimeZone('Asia/Tehran');
            $dt = new \DateTime($datetime, new \DateTimeZone('UTC'));
            $dt->setTimezone($tz);

            $y = (int)$dt->format('Y');
            $m = (int)$dt->format('n');
            $d = (int)$dt->format('j');
            $time = $dt->format('H:i');

            if (function_exists('jdate')) {
                return jdate('Y/m/d H:i', $dt->getTimestamp());
            }

            if (function_exists('gregorian_to_jalali')) {
                list($jy, $jm, $jd) = gregorian_to_jalali($y, $m, $d);
                return sprintf('%04d/%02d/%02d %s', $jy, $jm, $jd, $time);
            }

            list($jy, $jm, $jd) = $this->g2j($y, $m, $d);
            return sprintf('%04d/%02d/%02d %s', $jy, $jm, $jd, $time);
        } catch (\Exception $e) {
            return $datetime;
        }
    }

    /**
     * تبدیل دستی میلادی به شمسی (fallback)
     */
    private function g2j(int $g_y, int $g_m, int $g_d): array
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $jy = 0;
        $jm = 0;
        $jd = 0;
        $g_y2 = $g_y - 1600;
        $g_m2 = $g_m - 1;
        $g_d2 = $g_d - 1;

        $g_day_no = 365 * $g_y2 + (int)(($g_y2 + 3) / 4) - (int)(($g_y2 + 99) / 100) + (int)(($g_y2 + 399) / 400);
        for ($i = 0; $i < $g_m2; $i++) $g_day_no += $g_days_in_month[$i];
        if ($g_m2 > 1 && (($g_y % 4 == 0 && $g_y % 100 != 0) || $g_y % 400 == 0)) $g_day_no++;
        $g_day_no += $g_d2;

        $j_day_no = $g_day_no - 79;
        $j_np     = (int)($j_day_no / 12053);
        $j_day_no %= 12053;
        $jy       = 979 + 33 * $j_np + 4 * (int)($j_day_no / 1461);
        $j_day_no %= 1461;
        if ($j_day_no >= 366) {
            $jy += (int)(($j_day_no - 1) / 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }
        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; $i++) $j_day_no -= $j_days_in_month[$i];
        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return array($jy, $jm, $jd);
    }

    public function __construct()
    {
        $this->sms = new SmsService();
    }

    // ══════════════════════════════════════════════════════════
    //  ارسال اعلان — نقطه ورودی اصلی
    // ══════════════════════════════════════════════════════════

    /**
     * @param  string $event    ثابت از NotificationTemplates (مثلاً ORDER_CREATED)
     * @param  int    $userId   شناسه کاربر
     * @param  int    $orderId  شناسه سفارش (0 اگر نداریم)
     * @param  array  $vars     متغیرهای تمپلیت
     * @param  string $channel  کانال: in_app | sms | push | all
     * @return int post_id اعلان ایجادشده
     */
    public function send(
        string $event,
        int    $userId,
        int    $orderId = 0,
        array  $vars    = array(),
        string $channel = NotificationTemplates::CHANNEL_ALL
    ): int {
        $tpl = NotificationTemplates::get($event);

        $title   = NotificationTemplates::render($tpl['title'],   $vars);
        $message = NotificationTemplates::render($tpl['message'],  $vars);
        $smsText = NotificationTemplates::render($tpl['sms_text'], $vars);

        $postId = $this->createRecord(array(
            'event'    => $event,
            'channel'  => $channel,
            'user_id'  => $userId,
            'order_id' => $orderId,
            'title'    => $title,
            'message'  => $message,
            'sms_text' => $smsText,
            'status'   => 'pending',
        ));

        if (!$postId) {
            return 0;
        }

        $this->dispatch($postId, $userId, $channel, $title, $message, $smsText);

        return $postId;
    }

    /**
     * ارسال دستی از ادمین با متن سفارشی
     */
    public function sendManual(
        int    $userId,
        string $title,
        string $message,
        string $smsText = '',
        int    $orderId = 0,
        string $channel = NotificationTemplates::CHANNEL_ALL
    ): int {
        $postId = $this->createRecord(array(
            'event'    => NotificationTemplates::MANUAL,
            'channel'  => $channel,
            'user_id'  => $userId,
            'order_id' => $orderId,
            'title'    => $title,
            'message'  => $message,
            'sms_text' => $smsText ?: $message,
            'status'   => 'pending',
        ));

        if (!$postId) {
            return 0;
        }

        $this->dispatch($postId, $userId, $channel, $title, $message, $smsText ?: $message);

        return $postId;
    }

    /**
     * ارسال گروهی به چند کاربر
     */
    public function sendBulk(
        array  $userIds,
        string $event,
        array  $vars    = array(),
        string $channel = NotificationTemplates::CHANNEL_ALL
    ): array {
        $results = array();
        foreach ($userIds as $uid) {
            $results[$uid] = $this->send($event, (int) $uid, 0, $vars, $channel);
        }
        return $results;
    }

    // ══════════════════════════════════════════════════════════
    //  خواندن اعلان‌ها
    //  نکته: این متدها قبلاً پشتیبانی از virtual IDs (ord_xxx_x)
    //  مربوط به order_description_content داشتند. این پشتیبانی
    //  به دلایل امنیتی حذف شد — کاربر نباید لاگ داخلی سفارش را ببیند.
    // ══════════════════════════════════════════════════════════

    /**
     * خوانده شدن یک اعلان
     * فقط برای اعلان‌های واقعی (post_type = tamir_notif) — virtual IDs دیگر پشتیبانی نمی‌شوند.
     *
     * @param int|string $notificationId
     * @param int $userId
     * @return bool
     */
    public function markRead($notificationId, int $userId): bool
    {
        // ❌ virtual IDs (ord_xxx_x) دیگر پشتیبانی نمی‌شوند — رد کن
        if (is_string($notificationId) && strpos($notificationId, 'ord_') === 0) {
            return false;
        }

        $id = (int) $notificationId;
        if ($id <= 0) return false;

        $post = get_post($id);
        if (! $post || $post->post_type !== self::POST_TYPE) {
            return false;
        }

        // چک ownership
        $owner = (int) get_post_meta($id, 'user_id', true);
        if ($owner !== $userId) {
            return false;
        }

        update_post_meta($id, 'is_read', 1);
        update_post_meta($id, 'read_at', current_time('mysql'));
        return true;
    }

    /**
     * خواندن همه اعلان‌های یک کاربر
     * فقط اعلان‌های post_type = tamir_notif — order messages دیگر touch نمی‌شوند.
     */
    public function markAllRead(int $userId): int
    {
        $posts = get_posts(array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array('key' => 'user_id', 'value' => $userId, 'compare' => '=', 'type' => 'NUMERIC'),
                array('key' => 'is_read', 'value' => 0, 'compare' => '=', 'type' => 'NUMERIC'),
            ),
        ));

        foreach ($posts as $id) {
            update_post_meta($id, 'is_read', 1);
            update_post_meta($id, 'read_at', current_time('mysql'));
        }

        return count($posts);
    }

    // ══════════════════════════════════════════════════════════
    //  Private
    // ══════════════════════════════════════════════════════════

    /**
     * ایجاد رکورد اعلان در post type
     */
    private function createRecord(array $data): int
    {
        $postId = wp_insert_post(array(
            'post_type'    => self::POST_TYPE,
            'post_status'  => 'publish',
            'post_title'   => sanitize_text_field($data['title'] ?? 'اعلان'),
            'post_content' => sanitize_textarea_field($data['message'] ?? ''),
            'post_author'  => $data['user_id'] ?? 0,
        ), true);

        if (is_wp_error($postId)) {
            error_log('[TamirNotif] createRecord error: ' . $postId->get_error_message());
            return 0;
        }

        $metas = array(
            'event'      => $data['event']    ?? NotificationTemplates::SYSTEM,
            'channel'    => $data['channel']  ?? NotificationTemplates::CHANNEL_ALL,
            'user_id'    => $data['user_id']  ?? 0,
            'order_id'   => $data['order_id'] ?? 0,
            'title'      => $data['title']    ?? '',
            'message'    => $data['message']  ?? '',
            'sms_text'   => $data['sms_text'] ?? '',
            'is_read'    => 0,
            'status'     => 'pending',
            'sms_status' => 'pending',
            'push_status' => 'pending',
            'sent_at'    => '',
            'read_at'    => '',
        );

        foreach ($metas as $k => $v) {
            update_post_meta($postId, $k, $v);
        }

        return $postId;
    }

    /**
     * ارسال واقعی از طریق کانال‌ها
     */
    private function dispatch(
        int    $postId,
        int    $userId,
        string $channel,
        string $title,
        string $message,
        string $smsText
    ): void {
        $inApp = in_array($channel, array(
            NotificationTemplates::CHANNEL_IN_APP,
            NotificationTemplates::CHANNEL_ALL,
        ), true);

        $doSms = in_array($channel, array(
            NotificationTemplates::CHANNEL_SMS,
            NotificationTemplates::CHANNEL_ALL,
        ), true);

        if ($inApp) {
            update_post_meta($postId, 'status', 'sent');
            update_post_meta($postId, 'sent_at', current_time('mysql'));
        }

        if ($doSms) {
            $mobile = get_user_meta($userId, 'mobile', true) ?: '';
            if ($mobile) {
                $result = $this->sms->send($mobile, $smsText ?: $message);
                update_post_meta($postId, 'sms_status', $result['success'] ? 'sent' : 'failed');
                if (!empty($result['message_id'])) {
                    update_post_meta($postId, 'sms_message_id', $result['message_id']);
                }
                if (!empty($result['error'])) {
                    update_post_meta($postId, 'sms_error', $result['error']);
                }
            } else {
                update_post_meta($postId, 'sms_status', 'skipped');
            }
        }

        if (in_array($channel, array(NotificationTemplates::CHANNEL_PUSH, NotificationTemplates::CHANNEL_ALL), true)) {
            update_post_meta($postId, 'push_status', 'skipped'); // TODO: Web Push
        }
    }

    /**
     * فرمت یک اعلان برای API
     */
    private function format(\WP_Post $post): array
    {
        $orderId = (int) get_post_meta($post->ID, 'order_id', true);
        return array(
            'id'         => $post->ID,
            'type'       => get_post_meta($post->ID, 'event',   true) ?: 'system',
            'channel'    => get_post_meta($post->ID, 'channel', true) ?: 'all',
            'title'      => get_post_meta($post->ID, 'title',   true) ?: $post->post_title,
            'message'    => get_post_meta($post->ID, 'message', true) ?: $post->post_content,
            'is_read'    => (bool) get_post_meta($post->ID, 'is_read', true),
            'order_id'   => $orderId ?: null,
            'status'     => get_post_meta($post->ID, 'status',     true) ?: 'sent',
            'sms_status' => get_post_meta($post->ID, 'sms_status', true) ?: 'skipped',
            'created_at' => $this->toJalali($post->post_date),
            'read_at'    => $this->toJalali(get_post_meta($post->ID, 'read_at', true) ?: ''),
            'created_at_raw' => $post->post_date,
        );
    }

    /**
     * دریافت اعلان‌های یک کاربر
     *
     * فقط اعلان‌های رسمی (tamir_notif post type) که از NotificationTemplates
     * ارسال شده‌اند برگردانده می‌شوند. لاگ‌های داخلی سفارش (order_description_content)
     * به هیچ وجه در این خروجی نیستند.
     */
    public function getUserNotifications(int $userId, int $page = 1, int $perPage = 20): array
    {
        $query = new \WP_Query(array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $perPage,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'     => 'user_id',
                    'value'   => $userId,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
                array(
                    'key'     => 'channel',
                    'value'   => array(
                        NotificationTemplates::CHANNEL_IN_APP,
                        NotificationTemplates::CHANNEL_ALL,
                    ),
                    'compare' => 'IN',
                ),
            ),
        ));

        $items = array();
        foreach ($query->posts as $post) {
            $items[] = $this->format($post);
        }

        $total = (int) $query->found_posts;

        return array(
            'data'  => $items,
            'total' => $total,
            'pages' => (int) ceil($total / max(1, $perPage)),
        );
    }

    /**
     * تعداد اعلان‌های خوانده‌نشده کاربر
     */
    public function countUnread(int $userId): int
    {
        $posts = get_posts(array(
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array('key' => 'user_id', 'value' => $userId, 'compare' => '=', 'type' => 'NUMERIC'),
                array('key' => 'is_read', 'value' => 0,       'compare' => '=', 'type' => 'NUMERIC'),
                array('key' => 'channel', 'value' => array('in_app', 'all'), 'compare' => 'IN'),
            ),
        ));

        return count($posts);
    }

    // ══════════════════════════════════════════════════════════
    //  🔒 INTERNAL METHODS — do not expose via public API
    //
    //  این متدها برای پنل داخلی / گزارش‌گیری / صفحه تاریخچه سفارش
    //  در آینده قابل استفاده هستند. هرگز مستقیماً در endpoint های
    //  notification ها نباید صدا زده شوند.
    //
    //  اگر می‌خواهید در آینده تاریخچه‌ی پیام‌های یک سفارش خاص را
    //  به کاربر نشان دهید (مثلاً در صفحه جزئیات همان سفارش)،
    //  یک endpoint جداگانه بسازید و از بیرون کلاس این متد را صدا بزنید،
    //  ولی سخت‌گیری کنید که:
    //    - فقط لاگ‌های «مناسب نمایش» فیلتر شوند
    //    - ownership سفارش چک شود
    //    - محتوای حساس (یادداشت اپراتور، ...) نمایش داده نشود
    // ══════════════════════════════════════════════════════════

    /**
     * 🔒 INTERNAL — استخراج order messages به فرمت notification
     * قبلاً از getUserNotifications صدا زده می‌شد. اکنون قطع شده.
     *
     * @internal هرگز مستقیماً در user-facing API صدا نزنید.
     */
    private function getOrderMessagesAsNotifications(int $userId): array
    {
        $userMobile = $this->resolveUserMobile($userId);
        $customerIds = $this->findAllCustomerIdsByMobile($userMobile, $userId);

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

        $orders = get_posts(array(
            'post_type'      => 'orders',
            'post_status'    => array('publish', 'pending', 'draft', 'private', 'future'),
            'posts_per_page' => -1,
            'meta_query'     => array('relation' => 'AND', $orMetaQuery),
            'fields'         => 'ids',
        ));

        if (empty($orders)) {
            return array();
        }

        $items = array();

        foreach ($orders as $orderId) {
            $messages = get_post_meta($orderId, 'order_description_content', true);
            if (!is_array($messages) || empty($messages)) continue;

            $trackingCode = get_post_meta($orderId, 'tracking_code', true) ?: ((string) $orderId);
            $priceCustomer = get_post_meta($orderId, 'price_customer', true);

            foreach ($messages as $idx => $item) {
                if (!is_array($item)) continue;

                $subject = isset($item['subject']) ? $item['subject'] : 'پیام سیستم';
                $rawContent = isset($item['content']) ? $item['content'] : '';
                $date = isset($item['date']) ? $item['date'] : '';
                $status = isset($item['status']) ? $item['status'] : '';
                $authorId = isset($item['author']) ? (int) $item['author'] : 0;
                $authorName = $authorId > 0
                    ? (get_user_meta($authorId, 'first_name', true) ?: 'اپراتور')
                    : 'سیستم';

                if ($subject === 'ایجاد فاکتور') {
                    $content = 'جمع کل صورت حساب: '
                        . number_format((int) $priceCustomer)
                        . ' تومان';
                } else {
                    $content = strip_tags($rawContent);
                }

                $virtualId = 'ord_' . $orderId . '_' . $idx;

                $items[] = array(
                    'id'             => $virtualId,
                    'type'           => 'order_message',
                    'channel'        => 'in_app',
                    'title'          => $subject . ' — سفارش #' . $trackingCode,
                    'message'        => $content,
                    'is_read'        => $this->isOrderMessageRead($userId, $virtualId),
                    'order_id'       => (int) $orderId,
                    'tracking_code'  => $trackingCode,
                    'status'         => 'sent',
                    'sms_status'     => 'skipped',
                    'author_name'    => $authorName,
                    'order_status'   => $status,
                    'created_at'     => $date,
                    'created_at_raw' => $this->panelinoDateToMysql($date),
                );
            }
        }

        usort($items, function ($a, $b) {
            return strcmp($b['created_at_raw'], $a['created_at_raw']);
        });

        return $items;
    }

    /**
     * 🔒 INTERNAL — بررسی read status یک order message
     * @internal
     */
    private function isOrderMessageRead(int $userId, string $virtualId): bool
    {
        $read = get_user_meta($userId, 'tamir_read_order_msgs', true);
        if (!is_array($read)) $read = array();
        return in_array($virtualId, $read, true);
    }

    /**
     * 🔒 INTERNAL — علامت‌گذاری یک order message به عنوان خوانده‌شده
     * @internal
     */
    private function markOrderMessageRead(int $userId, string $virtualId): bool
    {
        $read = get_user_meta($userId, 'tamir_read_order_msgs', true);
        if (!is_array($read)) $read = array();
        if (!in_array($virtualId, $read, true)) {
            $read[] = $virtualId;
            update_user_meta($userId, 'tamir_read_order_msgs', $read);
        }
        return true;
    }

    /**
     * 🔒 INTERNAL — تبدیل فرمت تاریخ Panelino به MySQL
     * @internal
     */
    private function panelinoDateToMysql(string $date): string
    {
        if (empty($date)) return '0000-00-00 00:00:00';

        $clean = str_replace('/', '-', trim($date));

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})(?:\s+(\d{1,2}):(\d{1,2}))?/', $clean, $m)) {
            $y = (int) $m[1];
            $mo = (int) $m[2];
            $d = (int) $m[3];
            $h = isset($m[4]) ? (int) $m[4] : 0;
            $mn = isset($m[5]) ? (int) $m[5] : 0;

            if ($y >= 1300 && $y <= 1500) {
                list($gy, $gm, $gd) = $this->j2g($y, $mo, $d);
                return sprintf('%04d-%02d-%02d %02d:%02d:00', $gy, $gm, $gd, $h, $mn);
            }

            return sprintf('%04d-%02d-%02d %02d:%02d:00', $y, $mo, $d, $h, $mn);
        }

        return '0000-00-00 00:00:00';
    }

    /**
     * 🔒 INTERNAL — تبدیل جلالی به میلادی
     * @internal
     */
    private function j2g(int $jy, int $jm, int $jd): array
    {
        $jy1 = $jy - 979;
        $jdn = 365 * $jy1 + (int)($jy1 / 33) * 8 + (int)((($jy1 % 33) + 3) / 4);
        for ($i = 0; $i < $jm - 1; ++$i) {
            $jdn += ($i < 6) ? 31 : 30;
        }
        $jdn += $jd - 1 + 79;

        $gy = 1600 + 400 * (int)($jdn / 146097);
        $jdn %= 146097;
        $leap = true;
        if ($jdn >= 36525) {
            $jdn--;
            $gy += 100 * (int)($jdn / 36524);
            $jdn %= 36524;
            if ($jdn >= 365) $jdn++;
            else $leap = false;
        }
        $gy += 4 * (int)($jdn / 1461);
        $jdn %= 1461;
        if ($jdn >= 366) {
            $leap = false;
            $jdn--;
            $gy += (int)($jdn / 365);
            $jdn %= 365;
        }

        $sal_a = array(
            0,
            31,
            (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)) ? 29 : 28,
            31,
            30,
            31,
            30,
            31,
            31,
            30,
            31,
            30,
            31
        );
        $gm = 0;
        for ($gm = 1; $gm <= 12 && $jdn >= $sal_a[$gm]; $gm++) {
            $jdn -= $sal_a[$gm];
        }
        $gd = $jdn + 1;

        return array($gy, $gm, $gd);
    }

    /**
     * 🔒 INTERNAL — پیدا کردن شماره موبایل کاربر
     * @internal استفاده توسط getOrderMessagesAsNotifications
     */
    private function resolveUserMobile(int $userId): string
    {
        $keys = array('tamir_phone', 'mobile', 'phone', 'billing_phone', 'mobile_number');
        foreach ($keys as $k) {
            $v = get_user_meta($userId, $k, true);
            if ($v) return $v;
        }
        return '';
    }

    /**
     * 🔒 INTERNAL — پیدا کردن همه ID های کاربری مرتبط با یک موبایل
     * @internal
     */
    private function findAllCustomerIdsByMobile(string $mobile, int $fallbackId): array
    {
        $ids = array($fallbackId);

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

        return array_values(array_unique(array_filter($ids)));
    }
}
