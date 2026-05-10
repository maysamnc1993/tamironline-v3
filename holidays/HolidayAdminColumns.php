<?php

/**
 * ستون‌های لیست admin برای CPT تعطیلات.
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class HolidayAdminColumns
{
    public static function columns(array $cols): array
    {
        unset($cols['date']);
        $cols['tamir_holiday_date']  = 'تاریخ شمسی';
        $cols['tamir_holiday_slots'] = 'بازه‌های باز';
        $cols['date']                = 'ایجاد';
        return $cols;
    }

    public static function render(string $col, int $post_id): void
    {
        if ($col === 'tamir_holiday_date') {
            $val = (string) get_post_meta($post_id, HolidayPostType::META_DATE, true);
            if (! $val) {
                echo '<span style="color:#b32d2e">—</span>';
                return;
            }
            echo '<code style="direction:ltr;display:inline-block">' . esc_html($val) . '</code>';
            return;
        }

        if ($col === 'tamir_holiday_slots') {
            $slots = get_post_meta($post_id, HolidayPostType::META_SLOTS, true);
            $slots = is_array($slots) ? $slots : [];
            $total = count(TimeSlotsConfig::all());
            $open  = count($slots);

            if ($open === 0) {
                echo '<span style="display:inline-block;padding:2px 8px;border-radius:10px;background:#fcecea;color:#b32d2e;font-size:11px;font-weight:600">کاملاً تعطیل</span>';
            } elseif ($open === $total) {
                echo '<span style="display:inline-block;padding:2px 8px;border-radius:10px;background:#e6f4ea;color:#00632f;font-size:11px;font-weight:600">همه بازه‌ها باز</span>';
            } else {
                echo '<span style="display:inline-block;padding:2px 8px;border-radius:10px;background:#fcf9e8;color:#8a6d00;font-size:11px;font-weight:600">';
                echo esc_html($open . ' از ' . $total . ' باز');
                echo '</span>';
                echo '<br><small style="color:#666;direction:ltr;display:inline-block;margin-top:3px">' . esc_html(implode(', ', $slots)) . '</small>';
            }
        }
    }

    public static function sortable(array $cols): array
    {
        $cols['tamir_holiday_date'] = 'tamir_holiday_date';
        return $cols;
    }

    public static function sortQuery(\WP_Query $q): void
    {
        if (! is_admin() || ! $q->is_main_query()) return;
        if ($q->get('post_type') !== HolidayPostType::POST_TYPE) return;
        if ($q->get('orderby') === 'tamir_holiday_date') {
            $q->set('meta_key', HolidayPostType::META_DATE);
            $q->set('orderby', 'meta_value');
        }
    }
}
