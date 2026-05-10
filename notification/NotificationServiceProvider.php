<?php

namespace Tamir\Notification;

/**
 * NotificationServiceProvider
 *
 * ثبت post type + تنظیمات ادمین + هوک‌های خودکار
 * این فایل را در functions.php یا پلاگین اصلی require کن
 */
class NotificationServiceProvider
{
    public static function boot(): void
    {
        add_action('init',            array(self::class, 'registerPostType'));
        add_action('admin_menu',      array(self::class, 'addSettingsPage'));
        add_action('admin_init',      array(self::class, 'registerSettings'));
        add_action('save_post_orders', array(self::class, 'onOrderStatusChange'), 10, 3);
    }

    // ══════════════════════════════════════════════════════════
    //  Post Type
    // ══════════════════════════════════════════════════════════

    public static function registerPostType(): void
    {
        register_post_type('tamir_notif', array(
            'label'               => 'اعلان‌ها',
            'labels'              => array(
                'name'               => 'اعلان‌ها',
                'singular_name'      => 'اعلان',
                'add_new'            => 'اعلان جدید',
                'add_new_item'       => 'افزودن اعلان',
                'edit_item'          => 'ویرایش اعلان',
                'view_item'          => 'مشاهده اعلان',
                'search_items'       => 'جستجوی اعلان',
                'not_found'          => 'اعلانی یافت نشد',
                'menu_name'          => 'اعلان‌ها',
            ),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => 'dashicons-bell',
            'menu_position'       => 26,
            'capability_type'     => 'post',
            'capabilities'        => array(
                'edit_post'          => 'manage_options',
                'edit_posts'         => 'manage_options',
                'edit_others_posts'  => 'manage_options',
                'publish_posts'      => 'manage_options',
                'read_post'          => 'manage_options',
                'delete_post'        => 'manage_options',
                'delete_posts'       => 'manage_options',
            ),
            'supports'            => array('title', 'author'),
            'show_in_rest'        => false,
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  هوک خودکار — تغییر وضعیت سفارش
    // ══════════════════════════════════════════════════════════

    /**
     * وقتی پست orders ذخیره می‌شود و status تغییر کرده، اعلان ارسال کن
     */
    public static function onOrderStatusChange(int $postId, \WP_Post $post, bool $update): void
    {
        if (!$update) return;
        if (wp_is_post_revision($postId) || wp_is_post_autosave($postId)) return;

        $newStatus = (int) get_post_meta($postId, 'status', true);
        $prevKey   = '_tamir_prev_status_' . $postId;
        $prevStatus = (int) get_transient($prevKey);

        // اگر وضعیت تغییر نکرده
        if ($prevStatus === $newStatus && $update) {
            // اولین بار — ذخیره کن
            set_transient($prevKey, $newStatus, 60);
            return;
        }

        set_transient($prevKey, $newStatus, 60);

        $userId    = (int) get_post_meta($postId, 'customer',       true);
        $orderCode = (string) get_post_meta($postId, 'tracking_code', true);
        $device    = (string) get_post_meta($postId, 'device_name',   true);
        $brand     = (string) get_post_meta($postId, 'brand_name',    true);
        $techId    = (int) get_post_meta($postId, 'technician',    true);
        $techName  = '';

        if ($techId) {
            $techName = get_user_meta($techId, 'first_name', true)
                      ?: get_userdata($techId)->display_name ?? '';
        }

        $event = NotificationTemplates::fromStatus($newStatus);

        if ($event === NotificationTemplates::SYSTEM) return;
        if (!$userId) return;

        $svc = new NotificationService();
        $svc->send($event, $userId, $postId, array(
            'order_code'  => $orderCode,
            'device'      => $device,
            'brand'       => $brand,
            'technician'  => $techName ?: 'تعمیرکار',
            'time'        => get_post_meta($postId, 'scheduled_time', true) ?: '',
            'date'        => get_post_meta($postId, 'scheduled_date', true) ?: '',
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  تنظیمات ادمین
    // ══════════════════════════════════════════════════════════

    public static function addSettingsPage(): void
    {
        add_options_page(
            'تنظیمات تعمیرآنلاین',
            'تعمیرآنلاین',
            'manage_options',
            'tamir-settings',
            array(self::class, 'renderSettingsPage')
        );
    }

    public static function registerSettings(): void
    {
        $options = array(
            'tamir_sms_provider',
            'tamir_sms_api_key',
            'tamir_sms_username',
            'tamir_sms_password',
            'tamir_sms_line_number',
        );
        foreach ($options as $opt) {
            register_setting('tamir_settings', $opt);
        }
    }

    public static function renderSettingsPage(): void
    {
        $provider = get_option('tamir_sms_provider', SmsService::PROVIDER_MELIPAYAMAK);
        ?>
        <div class="wrap" dir="rtl">
            <h1>تنظیمات تعمیرآنلاین</h1>
            <form method="post" action="options.php">
                <?php settings_fields('tamir_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th>پنل SMS</th>
                        <td>
                            <select name="tamir_sms_provider">
                                <?php
                                $providers = array(
                                    SmsService::PROVIDER_MELIPAYAMAK => 'ملی‌پیامک',
                                    SmsService::PROVIDER_KAVENEGAR   => 'کاوه‌نگار',
                                    SmsService::PROVIDER_SMSIR       => 'SMS.ir',
                                    SmsService::PROVIDER_FARAPAYAMAK => 'فراپیامک',
                                );
                                foreach ($providers as $val => $label):
                                ?>
                                    <option value="<?php echo esc_attr($val); ?>"
                                        <?php selected($provider, $val); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>API Key / نام کاربری</th>
                        <td>
                            <input name="tamir_sms_username" type="text"
                                value="<?php echo esc_attr(get_option('tamir_sms_username')); ?>"
                                class="regular-text" placeholder="نام کاربری یا username">
                        </td>
                    </tr>
                    <tr>
                        <th>رمز عبور / API Key</th>
                        <td>
                            <input name="tamir_sms_api_key" type="text"
                                value="<?php echo esc_attr(get_option('tamir_sms_api_key')); ?>"
                                class="regular-text" placeholder="رمز عبور یا API Key">
                        </td>
                    </tr>
                    <tr>
                        <th>رمز عبور (فراپیامک)</th>
                        <td>
                            <input name="tamir_sms_password" type="text"
                                value="<?php echo esc_attr(get_option('tamir_sms_password')); ?>"
                                class="regular-text" placeholder="فقط برای فراپیامک">
                        </td>
                    </tr>
                    <tr>
                        <th>شماره خط</th>
                        <td>
                            <input name="tamir_sms_line_number" type="text"
                                value="<?php echo esc_attr(get_option('tamir_sms_line_number')); ?>"
                                class="regular-text" dir="ltr" placeholder="مثلاً 50002...">
                        </td>
                    </tr>
                </table>
                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
        <?php
    }
}
