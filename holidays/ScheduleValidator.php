<?php

/**
 * ScheduleValidator
 * ───────────────────────────────────────────────────────────
 * اعتبارسنجی ترکیبی (تاریخ + بازه زمانی) برای رزرو سرویس.
 *
 * قوانین:
 *   1) تاریخ باید جلالی معتبر باشد
 *   2) تاریخ نباید گذشته باشد
 *   3) تاریخ نباید بیش از MAX_BOOKING_OFFSET روز در آینده باشد (پیش‌فرض ۴)
 *   4) بازه باید در لیست TimeSlotsConfig::validValues() باشد
 *   5) اگر امروز انتخاب شده: بازه باید هنوز شروع نشده باشد
 *      (تا لحظه‌ی start_hour، رزرو امکان‌پذیر است)
 *   6) اگر این تاریخ در CPT تعطیلات باشد: بازه باید در open_slots باشد
 *
 * این کلاس stateless است و از هر controller قابل استفاده است.
 *
 * 🆕 تغییرات این نسخه:
 *   - MAX_BOOKING_OFFSET از 5 به 4 تغییر کرد
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class ScheduleValidator
{
    /**
     * حداکثر روزهای قابل رزرو از امروز (inclusive).
     *
     * 🆕 از 5 به 4 تغییر کرد: مشتری از روز جاری تا 4 روز بعد
     *    می‌تواند نوبت رزرو کند.
     */
    public const MAX_BOOKING_OFFSET = 4;

    /**
     * Buffer (ساعت) — حداقل فاصله‌ی زمانی بین الان و شروع بازه برای امروز.
     *
     * 0 = تا لحظه‌ی شروع بازه، رزرو امکان‌پذیر است.
     */
    public const BUFFER_HOURS = 0;

    /** Timezone محاسبات */
    public const TZ = 'Asia/Tehran';

    // ═══════════════════════════════════════════════════════════

    private $errorCode = '';
    private $errorMessage = '';
    private $normalizedJalali = null;
    private $normalizedGregorian = null;

    private function reset(): void
    {
        $this->errorCode = '';
        $this->errorMessage = '';
        $this->normalizedJalali = null;
        $this->normalizedGregorian = null;
    }

    /**
     * اجرای کامل validation.
     *
     * @param string $dateRaw تاریخ ورودی (YYYY-MM-DD شمسی یا میلادی)
     * @param string $slot    بازه (مثلاً "09-12")
     * @return bool  true اگر معتبر، false در غیر این صورت
     */
    public function validate(string $dateRaw, string $slot): bool
    {
        $this->reset();

        // 1) نرمال‌سازی تاریخ
        $normalized = $this->normalizeDate($dateRaw);
        if (! $normalized) {
            return false;
        }
        [$jalali, $gregorian] = $normalized;
        $this->normalizedJalali = $jalali;
        $this->normalizedGregorian = $gregorian;

        // 2) چک محدوده (نه گذشته، نه خیلی دور)
        if (! $this->checkDateRange($gregorian)) {
            return false;
        }

        // 3) چک معتبر بودن slot
        if (! TimeSlotsConfig::isValid($slot)) {
            $this->setError('slot_invalid', 'بازه زمانی معتبر نیست.');
            return false;
        }

        // 4) چک buffer برای امروز
        if ($this->isToday($gregorian)) {
            if (! $this->checkBufferForToday($slot)) {
                return false;
            }
        }

        // 5) چک تعطیلات
        if (! $this->checkHolidayConstraint($jalali, $slot)) {
            return false;
        }

        return true;
    }

    // ── Getters ─────────────────────────────────────────────

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getNormalizedJalali(): ?string
    {
        return $this->normalizedJalali;
    }

    public function getNormalizedGregorian(): ?string
    {
        return $this->normalizedGregorian;
    }

    /**
     * @return array{jalali:string,gregorian:string}|null
     */
    public function getNormalizedDates(): ?array
    {
        if (! $this->normalizedJalali || ! $this->normalizedGregorian) return null;
        return [
            'jalali'    => $this->normalizedJalali,
            'gregorian' => $this->normalizedGregorian,
        ];
    }

    // ═══════════════════════════════════════════════════════════
    //  Internal checks
    // ═══════════════════════════════════════════════════════════

    /**
     * تشخیص جلالی/میلادی و تبدیل به هر دو فرمت.
     *
     * @return array{0:string,1:string}|null  [jalali, gregorian]
     */
    private function normalizeDate(string $raw): ?array
    {
        $raw = trim($this->toEnDigits($raw));
        if (! preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $raw, $m)) {
            $this->setError('date_format', 'فرمت تاریخ صحیح نیست.');
            return null;
        }
        $y = (int) $m[1];
        $mo = (int) $m[2];
        $d = (int) $m[3];

        if ($mo < 1 || $mo > 12 || $d < 1 || $d > 31) {
            $this->setError('date_invalid', 'ماه یا روز نامعتبر است.');
            return null;
        }

        if ($y >= 1300 && $y <= 1500) {
            // جلالی
            if (! HolidayRepository::isValidJalaliDate(sprintf('%04d-%02d-%02d', $y, $mo, $d))) {
                $this->setError('date_invalid', 'تاریخ جلالی نامعتبر است.');
                return null;
            }
            [$gy, $gm, $gd] = HolidayRepository::jalaliToGregorian($y, $mo, $d);
            return [
                sprintf('%04d-%02d-%02d', $y, $mo, $d),
                sprintf('%04d-%02d-%02d', $gy, $gm, $gd),
            ];
        } elseif ($y >= 1900 && $y <= 2100) {
            // میلادی
            if (! checkdate($mo, $d, $y)) {
                $this->setError('date_invalid', 'تاریخ میلادی نامعتبر است.');
                return null;
            }
            [$jy, $jm, $jd] = HolidayRepository::gregorianToJalali($y, $mo, $d);
            return [
                sprintf('%04d-%02d-%02d', $jy, $jm, $jd),
                sprintf('%04d-%02d-%02d', $y, $mo, $d),
            ];
        }

        $this->setError('date_invalid', 'سال تاریخ در محدوده مجاز نیست.');
        return null;
    }

    /**
     * چک بازه قابل قبول (نه گذشته، نه بیش از MAX_BOOKING_OFFSET)
     */
    private function checkDateRange(string $gregorian): bool
    {
        try {
            $tz = new \DateTimeZone(self::TZ);
            $req = \DateTime::createFromFormat('Y-m-d', $gregorian, $tz);
            if (! $req) {
                $this->setError('date_invalid', 'تاریخ قابل پردازش نیست.');
                return false;
            }
            $req->setTime(0, 0, 0);

            $today = new \DateTime('now', $tz);
            $today->setTime(0, 0, 0);

            if ($req < $today) {
                $this->setError('date_past', 'تاریخ مراجعه نمی‌تواند در گذشته باشد.');
                return false;
            }

            $maxDate = clone $today;
            $maxDate->modify('+' . self::MAX_BOOKING_OFFSET . ' days');
            if ($req > $maxDate) {
                $this->setError(
                    'date_too_far',
                    sprintf('تاریخ مراجعه نمی‌تواند بیش از %d روز آینده باشد.', self::MAX_BOOKING_OFFSET)
                );
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            $this->setError('date_invalid', 'خطا در پردازش تاریخ.');
            return false;
        }
    }

    /**
     * آیا این تاریخ میلادی "امروز" در تهران است؟
     */
    private function isToday(string $gregorian): bool
    {
        try {
            $tz = new \DateTimeZone(self::TZ);
            $today = (new \DateTime('now', $tz))->format('Y-m-d');
            return $gregorian === $today;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * اگر انتخاب کاربر "امروز" است، بازه باید هنوز شروع نشده باشد.
     *
     * با BUFFER_HOURS=0:
     *   - cutoff = ساعت دقیق الان (مثلاً 17.5 = 17:30)
     *   - بازه‌ای که start_hour <= cutoff باشد → بسته
     *
     * مثال‌ها (با بازه‌های جدید 09-12, 12-15, 15-18, 18-21):
     *   - الان 17:30 → cutoff=17.5 → بازه 18-21 (start=18) → 18 <= 17.5 → false → باز ✓
     *   - الان 18:00 → cutoff=18.0 → بازه 18-21 (start=18) → 18 <= 18 → true → بسته ✓
     *   - الان 18:00 → بازه 15-18 (start=15) → 15 <= 18 → true → بسته ✓
     *   - الان 11:30 → cutoff=11.5 → بازه 12-15 (start=12) → 12 <= 11.5 → false → باز ✓
     */
    private function checkBufferForToday(string $slot): bool
    {
        $slotMeta = null;
        foreach (TimeSlotsConfig::all() as $s) {
            if ($s['value'] === $slot) {
                $slotMeta = $s;
                break;
            }
        }
        if (! $slotMeta) return true;

        try {
            $tz = new \DateTimeZone(self::TZ);
            $now = new \DateTime('now', $tz);
            $hour = (int) $now->format('G');
            $minute = (int) $now->format('i');
            $cutoff = $hour + self::BUFFER_HOURS + ($minute > 0 ? $minute / 60 : 0);

            if ($slotMeta['start_hour'] <= $cutoff) {
                $this->setError(
                    'slot_past',
                    'این بازه دیگر در دسترس نیست. لطفاً بازه یا روز دیگری انتخاب کنید.'
                );
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * اگر این تاریخ در CPT تعطیلات باشد، بازه باید در open_slots باشد.
     */
    private function checkHolidayConstraint(string $jalali, string $slot): bool
    {
        $holiday = HolidayRepository::findByDate($jalali);
        if (! $holiday) {
            return true;
        }

        $openSlots = isset($holiday['open_slots']) && is_array($holiday['open_slots'])
            ? $holiday['open_slots']
            : [];

        if (empty($openSlots)) {
            $this->setError(
                'holiday_fully_closed',
                sprintf('روز %s تعطیل است و قابل رزرو نیست.', $holiday['title'] ?: $jalali)
            );
            return false;
        }

        if (! in_array($slot, $openSlots, true)) {
            $this->setError(
                'holiday_slot_closed',
                sprintf('در روز %s این بازه قابل رزرو نیست.', $holiday['title'] ?: $jalali)
            );
            return false;
        }
        return true;
    }

    // ── Utils ───────────────────────────────────────────────

    private function setError(string $code, string $message): void
    {
        $this->errorCode = $code;
        $this->errorMessage = $message;
    }

    private function toEnDigits(string $s): string
    {
        $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace(array_merge($fa, $ar), array_merge($en, $en), $s);
    }
}
