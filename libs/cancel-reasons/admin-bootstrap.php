<?php
/**
 * ═══════════════════════════════════════════════════════════════
 *  admin-bootstrap.php — ثبت admin pages
 *
 *  Path: wp-content/themes/weblino/admin/admin-bootstrap.php
 *
 *  Include در functions.php:
 *    require_once get_template_directory() . '/admin/admin-bootstrap.php';
 *
 *  وظایف:
 *   - load کلاس‌ها
 *   - instantiate پنل‌های admin
 * ═══════════════════════════════════════════════════════════════
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/ListManager.php';
require_once __DIR__ . '/ListAdminPage.php';

// ────────────────────────────────────────────────────────
// ثبت پنل دلایل لغو سفارش (در اپ موبایل)
// ────────────────────────────────────────────────────────
new \Tamir\Admin\ListAdminPage(
    'cancel_reasons',
    'دلایل لغو سفارش',
    'tamir-cancel-reasons',
    'manage_options'
);

// ────────────────────────────────────────────────────────
// در آینده می‌توانید لیست‌های دیگر اضافه کنید:
//
// new \Tamir\Admin\ListAdminPage(
//     'objections',
//     'ایرادهای دستگاه',
//     'tamir-objections'
// );
//
// new \Tamir\Admin\ListAdminPage(
//     'introductions',
//     'زمینه‌های آشنایی',
//     'tamir-introductions'
// );
// ────────────────────────────────────────────────────────
