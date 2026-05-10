<?php

/**
 * TimeSlotsConfig — تعریف مرکزی بازه‌های زمانی مراجعه
 *
 * Single Source of Truth برای بازه‌ها در PHP.
 * همتای TypeScript: src/lib/time-slots.ts (TIME_SLOTS)
 *
 * 🆕 تغییرات این نسخه:
 *   - بازه‌های جدید: 09-12, 12-15, 15-18, 18-21
 *   - هر بازه ۳ ساعت (قبلاً ۲ ساعت بود)
 *
 * ⚠️ نکته: این تغییر روی TypeScript type ها هم تأثیر دارد.
 *    باید src/types/index.ts و src/lib/time-slots.ts هم آپدیت شوند.
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class TimeSlotsConfig
{
    /**
     * لیست بازه‌های معتبر.
     * format: 'HH-HH' (ساعت ۲۴)
     *
     * @return array<int, array{value:string,label:string,start_hour:int}>
     */
    public static function all(): array
    {
        return [
            ['value' => '09-12', 'label' => '۹ تا ۱۲ صبح',  'start_hour' => 9],
            ['value' => '12-15', 'label' => '۱۲ تا ۱۵ ظهر', 'start_hour' => 12],
            ['value' => '15-18', 'label' => '۱۵ تا ۱۸ عصر', 'start_hour' => 15],
            ['value' => '18-21', 'label' => '۱۸ تا ۲۱ شب',  'start_hour' => 18],
        ];
    }

    /**
     * فقط مقادیر (slug) بازه‌ها — برای validation.
     *
     * @return string[]
     */
    public static function validValues(): array
    {
        return array_column(self::all(), 'value');
    }

    /**
     * آیا یک slug معتبر است؟
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::validValues(), true);
    }

    /**
     * فیلتر و sanitize لیست slot ها — فقط معتبرها باقی می‌مانند، بدون تکرار، در ترتیب استاندارد.
     *
     * @param mixed $input
     * @return string[]
     */
    public static function sanitizeList($input): array
    {
        if (! is_array($input)) return [];
        $valid = self::validValues();
        $clean = [];
        foreach ($input as $v) {
            if (! is_string($v)) continue;
            if (in_array($v, $valid, true) && ! in_array($v, $clean, true)) {
                $clean[] = $v;
            }
        }
        return array_values(array_intersect($valid, $clean));
    }
}
