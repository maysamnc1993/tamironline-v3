<?php

/**
 * Cache busting: وقتی تعطیلی‌ای ویرایش/حذف شد، transientهای مربوطه پاک می‌شوند.
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class HolidayCacheBuster
{
    /**
     * هرگونه cache مربوط به route تعطیلات را invalidate کن.
     * چون Rehub Cache middleware از hash(route+params) استفاده می‌کند،
     * ما یک نسخه (version) در option می‌گذاریم و آن را افزایش می‌دهیم.
     * در کنترلر این نسخه را در response بر‌می‌گردانیم و cache بر اساس آن invalidate می‌شود.
     *
     * روش تکمیلی: پاک کردن مستقیم transientهای مشکوک.
     */
    public const VERSION_OPTION = 'tamir_holidays_version';

    public static function flush(int $post_id = 0): void
    {
        // نسخه را افزایش بده
        $v = (int) get_option(self::VERSION_OPTION, 0);
        update_option(self::VERSION_OPTION, $v + 1, false);

        // پاک کردن transientهایی که framework شما با پیشوند مشخصی ذخیره می‌کند.
        // اگر prefix transient در Cache middleware چیز دیگری است، اینجا هماهنگ کنید.
        self::purgeByPattern('rehub_rest_cache_');
        self::purgeByPattern('rest_cache_');
    }

    public static function onDelete(int $post_id): void
    {
        $p = get_post($post_id);
        if (! $p || $p->post_type !== HolidayPostType::POST_TYPE) return;
        self::flush($post_id);
    }

    public static function version(): int
    {
        return (int) get_option(self::VERSION_OPTION, 0);
    }

    /**
     * پاک کردن transientها بر اساس pattern (فقط در صورت dev/non-persistent object cache).
     */
    private static function purgeByPattern(string $prefix): void
    {
        global $wpdb;
        if (wp_using_ext_object_cache()) {
            // در محیط object-cache نمی‌توان به راحتی pattern purge کرد
            // Kernel می‌تواند cache خود را بر اساس `version` که در params ارسال می‌کنیم invalidate کند.
            return;
        }
        $like = $wpdb->esc_like('_transient_' . $prefix) . '%';
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            $like
        ));
        $like2 = $wpdb->esc_like('_transient_timeout_' . $prefix) . '%';
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            $like2
        ));
    }
}
