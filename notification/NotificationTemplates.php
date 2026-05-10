<?php

namespace Tamir\Notification;

/**
 * NotificationTemplates
 *
 * تمپلیت‌های اعلان — برای اضافه کردن تمپلیت جدید:
 *  1. یک const جدید در EVENT_TYPES اضافه کن
 *  2. یک آرایه در TEMPLATES اضافه کن
 *  3. تمام — سرویس خودکار از آن استفاده می‌کند
 *
 * متغیرهای قابل استفاده: {name} {order_code} {device} {brand} {time} {date} {technician}
 */
class NotificationTemplates
{
    // ── نوع رویدادها ─────────────────────────────────────────
    const ORDER_CREATED      = 'order_created';
    const ORDER_ASSIGNED     = 'order_assigned';
    const ORDER_IN_PROGRESS  = 'order_in_progress';
    const ORDER_COMPLETED    = 'order_completed';
    const ORDER_CANCELLED    = 'order_cancelled';
    const ORDER_SUSPENDED    = 'order_suspended';
    const ORDER_DECLINED     = 'order_declined';
    const MANUAL             = 'manual';
    const SYSTEM             = 'system';

    // ── کانال‌های ارسال ───────────────────────────────────────
    const CHANNEL_IN_APP  = 'in_app';
    const CHANNEL_SMS     = 'sms';
    const CHANNEL_PUSH    = 'push';
    const CHANNEL_ALL     = 'all';

    // ── تمپلیت‌ها ─────────────────────────────────────────────
    // هر تمپلیت: title, message, sms_text (کوتاه‌تر برای SMS)
    const TEMPLATES = array(
        self::ORDER_CREATED => array(
            'title'    => 'سفارش شما ثبت شد',
            'message'  => 'سفارش تعمیر {device} با کد #{order_code} ثبت شد. به زودی با شما تماس می‌گیریم.',
            'sms_text' => 'تعمیرآنلاین: سفارش {device} شما (#{order_code}) ثبت شد.',
        ),
        self::ORDER_ASSIGNED => array(
            'title'    => 'تعمیرکار اختصاص داده شد',
            'message'  => '{technician} برای سرویس {device} شما تعیین شد. بازه زمانی: {time}.',
            'sms_text' => 'تعمیرآنلاین: {technician} برای تعمیر {device} شما در {time} مراجعه می‌کند.',
        ),
        self::ORDER_IN_PROGRESS => array(
            'title'    => 'تعمیرکار در راه است',
            'message'  => 'تعمیرکار سفارش #{order_code} به زودی به آدرس شما می‌رسد.',
            'sms_text' => 'تعمیرآنلاین: تعمیرکار سفارش #{order_code} در راه است.',
        ),
        self::ORDER_COMPLETED => array(
            'title'    => 'سفارش با موفقیت انجام شد',
            'message'  => 'تعمیر {device} با کد #{order_code} با موفقیت کامل شد. ممنون از اعتماد شما!',
            'sms_text' => 'تعمیرآنلاین: تعمیر {device} شما (#{order_code}) انجام شد. ممنون!',
        ),
        self::ORDER_CANCELLED => array(
            'title'    => 'سفارش لغو شد',
            'message'  => 'سفارش #{order_code} لغو شد. در صورت نیاز مجدداً ثبت سفارش کنید.',
            'sms_text' => 'تعمیرآنلاین: سفارش #{order_code} لغو شد.',
        ),
        self::ORDER_SUSPENDED => array(
            'title'    => 'سفارش به تعلیق درآمد',
            'message'  => 'سفارش #{order_code} موقتاً متوقف شد. کارشناسان در حال بررسی هستند.',
            'sms_text' => 'تعمیرآنلاین: سفارش #{order_code} موقتاً متوقف شد.',
        ),
        self::ORDER_DECLINED => array(
            'title'    => 'سفارش تأیید نشد',
            'message'  => 'متأسفانه سفارش #{order_code} تأیید نشد. برای اطلاعات بیشتر با پشتیبانی تماس بگیرید.',
            'sms_text' => 'تعمیرآنلاین: سفارش #{order_code} تأیید نشد. تماس: 021XXXXXXXX',
        ),
        self::MANUAL => array(
            'title'    => '',    // ادمین مستقیم وارد می‌کند
            'message'  => '',
            'sms_text' => '',
        ),
        self::SYSTEM => array(
            'title'    => 'پیام سیستم',
            'message'  => '',
            'sms_text' => '',
        ),
    );

    // ── نگاشت وضعیت سفارش به نوع اعلان ───────────────────────
    const STATUS_TO_EVENT = array(
        0   => self::ORDER_CREATED,
        1   => self::ORDER_ASSIGNED,
        2   => self::ORDER_IN_PROGRESS,
        3   => self::ORDER_SUSPENDED,
        4   => self::ORDER_CANCELLED,
        5   => self::ORDER_COMPLETED,
        100 => self::ORDER_DECLINED,
    );

    /**
     * دریافت تمپلیت
     */
    public static function get(string $event): array
    {
        return isset(self::TEMPLATES[$event])
            ? self::TEMPLATES[$event]
            : self::TEMPLATES[self::SYSTEM];
    }

    /**
     * جایگزینی متغیرها در متن
     */
    public static function render(string $text, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $text = str_replace('{' . $key . '}', (string) $value, $text);
        }
        return $text;
    }

    /**
     * لیست همه نوع رویدادها برای انتخاب در ادمین
     */
    public static function eventLabels(): array
    {
        return array(
            self::ORDER_CREATED     => 'ثبت سفارش',
            self::ORDER_ASSIGNED    => 'اختصاص تعمیرکار',
            self::ORDER_IN_PROGRESS => 'در حال انجام',
            self::ORDER_COMPLETED   => 'تکمیل سفارش',
            self::ORDER_CANCELLED   => 'لغو سفارش',
            self::ORDER_SUSPENDED   => 'تعلیق سفارش',
            self::ORDER_DECLINED    => 'رد سفارش',
            self::MANUAL            => 'دستی',
            self::SYSTEM            => 'سیستمی',
        );
    }

    /**
     * رویداد متناسب با وضعیت عددی سفارش
     */
    public static function fromStatus(int $status): string
    {
        return isset(self::STATUS_TO_EVENT[$status])
            ? self::STATUS_TO_EVENT[$status]
            : self::SYSTEM;
    }
}
