# Tamir Notification System

## نصب

در پلاگین اصلی یا functions.php:

```php
// بارگذاری سرویس
require_once __DIR__ . '/notification/NotificationServiceProvider.php';
require_once __DIR__ . '/notification/NotificationTemplates.php';
require_once __DIR__ . '/notification/NotificationService.php';
require_once __DIR__ . '/notification/SmsService.php';
require_once __DIR__ . '/notification/NotificationController.php';
require_once __DIR__ . '/notification/NotificationAdmin.php';

use Tamir\Notification\NotificationServiceProvider;
use Tamir\Notification\NotificationAdmin;

// فعال‌سازی
NotificationServiceProvider::boot();
NotificationAdmin::boot();
```

## تغییر در OrderController

```php
// به جای private function createNotification:
use Tamir\Notification\NotificationService;
use Tamir\Notification\NotificationTemplates;

// در store():
$svc = new NotificationService();
$svc->send(NotificationTemplates::ORDER_CREATED, $userId, $postId, [
    'order_code' => $trackingCode,
    'device'     => $deviceTerm->name,
    'brand'      => $brandTerm->name,
]);

// در cancel():
$svc->send(NotificationTemplates::ORDER_CANCELLED, $userId, $postId, [
    'order_code' => $trackingCode,
]);
```

## افزودن تمپلیت جدید

فقط در NotificationTemplates.php:

```php
const MY_EVENT = 'my_event';

const TEMPLATES = [
    self::MY_EVENT => [
        'title'    => 'عنوان اعلان',
        'message'  => 'متن با {order_code} و {device}',
        'sms_text' => 'متن کوتاه SMS',
    ],
    // ...
];
```

## Routes در routes.php

```php
register_rest_route('tamir/v1', '/notifications', [
    ['methods' => 'GET', 'callback' => [$notifCtrl, 'index'], 'permission_callback' => [$notifCtrl, 'authorize']],
]);
register_rest_route('tamir/v1', '/notifications/read-all', [
    ['methods' => 'POST', 'callback' => [$notifCtrl, 'markAllRead'], 'permission_callback' => [$notifCtrl, 'authorize']],
]);
register_rest_route('tamir/v1', '/notifications/(?P<id>\d+)/read', [
    ['methods' => 'POST', 'callback' => [$notifCtrl, 'markRead'], 'permission_callback' => [$notifCtrl, 'authorize']],
]);
```
