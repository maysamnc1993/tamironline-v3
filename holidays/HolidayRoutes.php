<?php

/**
 * ثبت route تعطیلات در Rehub REST Framework
 *
 * دو روش پشتیبانی:
 *  1) اگر در bootstrap framework شما فیلتر `rehub_rest_routes` وجود دارد،
 *     این کلاس آن را populate می‌کند (بدون دست زدن به routes.php).
 *  2) اگر ندارد، route را دستی در `core/rest/routes.php` اضافه کنید
 *     (نمونه در README ماژول آمده).
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class HolidayRoutes
{
    /**
     * @param array $registry   ساختار [groups, routes]
     * @return array
     */
    public static function register(array $registry): array
    {
        if (! isset($registry['routes']) || ! is_array($registry['routes'])) {
            $registry['routes'] = [];
        }

        $registry['routes'][] = [
            'namespace'  => 'rehub/v1',
            'route'      => '/tamir/holidays',
            'methods'    => 'GET',
            'controller' => \Tamir\Holidays\Controllers\HolidaysController::class,
            'action'     => 'index',
            'group'      => 'public',
            // Cache طولانی‌تر برای تعطیلات (تغییر کم)
            'middleware' => ['cache:3600'],
            'args'       => [
                'from' => ['sanitize_callback' => 'sanitize_text_field'],
                'to'   => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ];

        return $registry;
    }
}
