<?php

require_once get_template_directory() . '/notification/NotificationServiceProvider.php';
require_once get_template_directory() . '/notification/NotificationTemplates.php';
require_once get_template_directory() . '/notification/NotificationService.php';
require_once get_template_directory() . '/notification/SmsService.php';
// require_once get_template_directory() . '/notification/NotificationController.php';
require_once get_template_directory() . '/notification/NotificationAdmin.php';


use Tamir\Notification\NotificationServiceProvider;
use Tamir\Notification\NotificationAdmin;

NotificationServiceProvider::boot();
NotificationAdmin::boot();
