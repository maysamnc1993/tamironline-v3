<?php

/**
 * Custom Post Type: tamir_holiday
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class HolidayPostType
{
    public const POST_TYPE  = 'tamir_holiday';
    public const META_DATE  = 'tamir_holiday_date';
    public const META_SLOTS = 'tamir_holiday_open_slots';

    public static function register(): void
    {
        $labels = [
            'name'               => 'تعطیلات',
            'singular_name'      => 'تعطیلی',
            'menu_name'          => 'تعطیلات',
            'add_new'            => 'افزودن تعطیلی',
            'add_new_item'       => 'افزودن تعطیلی جدید',
            'edit_item'          => 'ویرایش تعطیلی',
            'new_item'           => 'تعطیلی جدید',
            'view_item'          => 'نمایش تعطیلی',
            'search_items'       => 'جستجوی تعطیلی',
            'not_found'          => 'تعطیلی‌ای یافت نشد.',
            'not_found_in_trash' => 'در سطل زباله چیزی نیست.',
            'all_items'          => 'همه تعطیلات',
        ];

        register_post_type(self::POST_TYPE, [
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'show_in_rest'        => false,
            'menu_icon'           => 'dashicons-calendar-alt',
            'menu_position'       => 26,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => ['title'],
            'has_archive'         => false,
            'rewrite'             => false,
            'query_var'           => false,
            'exclude_from_search' => true,
        ]);
    }
}
