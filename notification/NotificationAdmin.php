<?php

namespace Tamir\Notification;

/**
 * NotificationAdmin — متا باکس ادمین برای ارسال دستی
 * افزودن در پنل orders برای ارسال اعلان دستی به کاربر
 */
class NotificationAdmin
{
    public static function boot(): void
    {
        add_action('add_meta_boxes',    array(self::class, 'addMetaBoxes'));
        add_action('admin_post_tamir_send_notif', array(self::class, 'handleSend'));
    }

    public static function addMetaBoxes(): void
    {
        add_meta_box(
            'tamir_notif_send',
            'ارسال اعلان به کاربر',
            array(self::class, 'renderSendBox'),
            'orders',
            'side',
            'default'
        );

        add_meta_box(
            'tamir_notif_history',
            'تاریخچه اعلان‌ها',
            array(self::class, 'renderHistoryBox'),
            'orders',
            'normal',
            'default'
        );
    }

    /**
     * باکس ارسال اعلان
     */
    public static function renderSendBox(\WP_Post $post): void
    {
        $events = NotificationTemplates::eventLabels();
        ?>
        <div dir="rtl">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('tamir_send_notif', 'tamir_notif_nonce'); ?>
                <input type="hidden" name="action"   value="tamir_send_notif">
                <input type="hidden" name="order_id" value="<?php echo (int) $post->ID; ?>">

                <p>
                    <label style="font-weight:bold">نوع اعلان:</label><br>
                    <select name="event" style="width:100%">
                        <?php foreach ($events as $val => $label): ?>
                        <option value="<?php echo esc_attr($val); ?>">
                            <?php echo esc_html($label); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label style="font-weight:bold">کانال:</label><br>
                    <select name="channel" style="width:100%">
                        <option value="all">همه (اپ + SMS)</option>
                        <option value="in_app">فقط داخل اپ</option>
                        <option value="sms">فقط SMS</option>
                    </select>
                </p>

                <p>
                    <label style="font-weight:bold">عنوان سفارشی (اختیاری):</label><br>
                    <input type="text" name="custom_title" style="width:100%" placeholder="خالی = از تمپلیت">
                </p>

                <p>
                    <label style="font-weight:bold">متن سفارشی (اختیاری):</label><br>
                    <textarea name="custom_message" rows="3" style="width:100%"
                        placeholder="خالی = از تمپلیت"></textarea>
                </p>

                <p>
                    <?php submit_button('ارسال اعلان', 'primary', 'submit', false, array('style' => 'width:100%')); ?>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * باکس تاریخچه اعلان‌ها
     */
    public static function renderHistoryBox(\WP_Post $post): void
    {
        $notifs = get_posts(array(
            'post_type'      => NotificationService::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 20,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'     => 'order_id',
                    'value'   => $post->ID,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            ),
        ));

        if (empty($notifs)) {
            echo '<p dir="rtl">هیچ اعلانی ارسال نشده.</p>';
            return;
        }

        echo '<table class="widefat striped" dir="rtl">';
        echo '<thead><tr><th>نوع</th><th>کانال</th><th>وضعیت SMS</th><th>تاریخ</th><th>خوانده شد</th></tr></thead>';
        echo '<tbody>';

        foreach ($notifs as $n) {
            $event      = get_post_meta($n->ID, 'event',      true);
            $channel    = get_post_meta($n->ID, 'channel',    true);
            $smsStatus  = get_post_meta($n->ID, 'sms_status', true);
            $isRead     = get_post_meta($n->ID, 'is_read',    true);
            $labels     = NotificationTemplates::eventLabels();

            $smsColor = array(
                'sent'    => 'green',
                'failed'  => 'red',
                'pending' => 'orange',
                'skipped' => 'gray',
            );

            printf(
                '<tr>
                    <td>%s</td>
                    <td>%s</td>
                    <td style="color:%s">%s</td>
                    <td>%s</td>
                    <td>%s</td>
                </tr>',
                esc_html($labels[$event] ?? $event),
                esc_html($channel),
                esc_attr($smsColor[$smsStatus] ?? 'gray'),
                esc_html($smsStatus),
                esc_html($n->post_date),
                $isRead ? '✓' : '—'
            );
        }

        echo '</tbody></table>';
    }

    /**
     * هندل ارسال دستی
     */
    public static function handleSend(): void
    {
        if (!isset($_POST['tamir_notif_nonce']) ||
            !wp_verify_nonce($_POST['tamir_notif_nonce'], 'tamir_send_notif')) {
            wp_die('خطای امنیتی');
        }

        if (!current_user_can('manage_options')) {
            wp_die('دسترسی ممنوع');
        }

        $orderId  = (int)    ($_POST['order_id']       ?? 0);
        $event    = sanitize_text_field($_POST['event']          ?? NotificationTemplates::MANUAL);
        $channel  = sanitize_text_field($_POST['channel']        ?? 'all');
        $cusTitle = sanitize_text_field($_POST['custom_title']   ?? '');
        $cusMsg   = sanitize_textarea_field($_POST['custom_message'] ?? '');

        $userId = (int) get_post_meta($orderId, 'customer', true);
        if (!$userId) {
            wp_safe_redirect(admin_url("post.php?post={$orderId}&action=edit&notif_error=no_user"));
            exit;
        }

        $svc = new NotificationService();

        if ($cusTitle || $cusMsg) {
            // متن سفارشی
            $svc->sendManual($userId, $cusTitle, $cusMsg, '', $orderId, $channel);
        } else {
            // از تمپلیت
            $svc->send($event, $userId, $orderId, array(
                'order_code' => get_post_meta($orderId, 'tracking_code', true),
                'device'     => get_post_meta($orderId, 'device_name', true),
                'brand'      => get_post_meta($orderId, 'brand_name', true),
            ), $channel);
        }

        wp_safe_redirect(admin_url("post.php?post={$orderId}&action=edit&notif_sent=1"));
        exit;
    }
}
