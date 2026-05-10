

<?php
/**
 * Tamir App Control Module — Bootstrap
 *
 * Load from functions.php:
 *   require_once get_template_directory() . '/core/app-control/bootstrap.php';
 *
 * @package Tamir\AppControl
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/AcfFields.php';
require_once __DIR__ . '/AppStatusService.php';

// راه‌اندازی ACF
\Tamir\AppControl\AcfFields::register();
