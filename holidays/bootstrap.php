<?php

/**
 * Tamir Holidays Module — Bootstrap
 *
 * Load from functions.php:
 *   require_once get_template_directory() . '/core/holidays/bootstrap.php';
 *
 * @package Tamir\Holidays
 */

defined('ABSPATH') || exit;

// ── بارگذاری کلاس‌ها ──────────────────────────────────────────
require_once __DIR__ . '/TimeSlotsConfig.php';
require_once __DIR__ . '/HolidayPostType.php';
require_once __DIR__ . '/HolidayRepository.php';
require_once __DIR__ . '/ScheduleValidator.php';
require_once __DIR__ . '/HolidayMetaBox.php';
require_once __DIR__ . '/HolidayAdminColumns.php';
require_once __DIR__ . '/HolidayCacheBuster.php';
require_once __DIR__ . '/HolidayBulkTools.php';
// require_once __DIR__ . '/Controllers/HolidaysController.php';

// ── راه‌اندازی بخش‌های WP ─────────────────────────────────────
add_action('init',            [\Tamir\Holidays\HolidayPostType::class,  'register'], 5);
add_action('add_meta_boxes',  [\Tamir\Holidays\HolidayMetaBox::class,   'register']);
add_action('save_post_tamir_holiday',  [\Tamir\Holidays\HolidayMetaBox::class, 'save'], 10, 2);
add_action('admin_enqueue_scripts',    [\Tamir\Holidays\HolidayMetaBox::class, 'enqueueAssets']);

// ابزار زمانبندی سریع (submenu)
add_action('admin_menu',       [\Tamir\Holidays\HolidayBulkTools::class, 'register']);

// ستون‌های لیست
add_filter('manage_tamir_holiday_posts_columns',         [\Tamir\Holidays\HolidayAdminColumns::class, 'columns']);
add_action('manage_tamir_holiday_posts_custom_column',   [\Tamir\Holidays\HolidayAdminColumns::class, 'render'], 10, 2);
add_filter('manage_edit-tamir_holiday_sortable_columns', [\Tamir\Holidays\HolidayAdminColumns::class, 'sortable']);
add_action('pre_get_posts',                              [\Tamir\Holidays\HolidayAdminColumns::class, 'sortQuery']);

// Cache busting
add_action('save_post_tamir_holiday', [\Tamir\Holidays\HolidayCacheBuster::class, 'flush'], 20);
add_action('deleted_post',            [\Tamir\Holidays\HolidayCacheBuster::class, 'onDelete']);
add_action('trashed_post',            [\Tamir\Holidays\HolidayCacheBuster::class, 'onDelete']);
add_action('untrashed_post',          [\Tamir\Holidays\HolidayCacheBuster::class, 'onDelete']);
