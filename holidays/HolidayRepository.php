<?php

/**
 * Repository: منطق داده‌ای تعطیلات
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class HolidayRepository
{
    /**
     * لیست تعطیلات در یک بازه (inclusive).
     *
     * @param string $from  'YYYY-MM-DD' (جلالی)
     * @param string $to    'YYYY-MM-DD' (جلالی)
     * @return array<int, array{date:string,title:string,open_slots:string[]}>
     */
    public static function inRange(string $from, string $to): array
    {
        if (strcmp($from, $to) > 0) {
            [$from, $to] = [$to, $from];
        }

        $posts = get_posts([
            'post_type'        => HolidayPostType::POST_TYPE,
            'post_status'      => 'publish',
            'posts_per_page'   => 1000,
            'no_found_rows'    => true,
            'suppress_filters' => true,
            'orderby'          => 'meta_value',
            'meta_key'         => HolidayPostType::META_DATE,
            'order'            => 'ASC',
            'meta_query'       => [
                [
                    'key'     => HolidayPostType::META_DATE,
                    'value'   => [$from, $to],
                    'compare' => 'BETWEEN',
                    'type'    => 'CHAR',
                ],
            ],
        ]);

        $out = [];
        foreach ($posts as $p) {
            $date = (string) get_post_meta($p->ID, HolidayPostType::META_DATE, true);
            if (! self::isValidJalaliDate($date)) continue;

            $slots = get_post_meta($p->ID, HolidayPostType::META_SLOTS, true);
            $openSlots = TimeSlotsConfig::sanitizeList(
                is_array($slots) ? $slots : []
            );

            $out[] = [
                'date'       => $date,
                'title'      => (string) $p->post_title,
                'open_slots' => $openSlots,
            ];
        }

        return $out;
    }

    /**
     * آیا یک تاریخ خاص تعطیل است؟ (یعنی در CPT رکورد دارد)
     */
    public static function isHoliday(string $date): bool
    {
        if (! self::isValidJalaliDate($date)) return false;
        $list = self::inRange($date, $date);
        return ! empty($list);
    }

    /**
     * دریافت رکورد تعطیلی برای تاریخ خاص (یا null)
     *
     * @return array{date:string,title:string,open_slots:string[]}|null
     */
    public static function findByDate(string $date): ?array
    {
        if (! self::isValidJalaliDate($date)) return null;
        $list = self::inRange($date, $date);
        return $list[0] ?? null;
    }

    /**
     * چک وجود قبلی برای جلوگیری از duplicate — برای bulk create
     */
    public static function existsForDate(string $date): bool
    {
        if (! self::isValidJalaliDate($date)) return false;

        $q = new \WP_Query([
            'post_type'      => HolidayPostType::POST_TYPE,
            'post_status'    => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'meta_query'     => [
                [
                    'key'     => HolidayPostType::META_DATE,
                    'value'   => $date,
                    'compare' => '=',
                ],
            ],
        ]);
        $exists = $q->have_posts();
        wp_reset_postdata();
        return $exists;
    }

    /**
     * ایجاد یک تعطیلی — برای bulk tools
     *
     * @param string $date
     * @param string $title
     * @param string[] $openSlots
     * @return int|null post_id یا null اگر duplicate بود
     */
    public static function create(string $date, string $title, array $openSlots): ?int
    {
        if (! self::isValidJalaliDate($date)) return null;
        if (self::existsForDate($date)) return null;

        $post_id = wp_insert_post([
            'post_type'   => HolidayPostType::POST_TYPE,
            'post_status' => 'publish',
            'post_title'  => $title ?: $date,
        ]);

        if (is_wp_error($post_id) || ! $post_id) return null;

        update_post_meta($post_id, HolidayPostType::META_DATE, $date);
        update_post_meta(
            $post_id,
            HolidayPostType::META_SLOTS,
            TimeSlotsConfig::sanitizeList($openSlots)
        );

        return (int) $post_id;
    }

    // ═══════════════════════════════════════════════════════════
    //  Jalali Utilities
    // ═══════════════════════════════════════════════════════════

    /**
     * اعتبارسنجی فرمت تاریخ جلالی: YYYY-MM-DD
     */
    public static function isValidJalaliDate(string $date): bool
    {
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) return false;
        $y = (int) $m[1];
        $mo = (int) $m[2];
        $d = (int) $m[3];

        if ($y < 1300 || $y > 1500) return false;
        if ($mo < 1 || $mo > 12)    return false;
        if ($d < 1 || $d > 31)      return false;
        if ($mo >= 7 && $mo <= 11 && $d > 30) return false;
        if ($mo === 12 && $d > 30) return false;
        return true;
    }

    /**
     * تبدیل تاریخ جلالی به gregorian (roozbeh pournader)
     *
     * @return array{0:int,1:int,2:int} [gy, gm, gd]
     */
    public static function jalaliToGregorian(int $jy, int $jm, int $jd): array
    {
        $jy += 1595;
        $days = -355668
            + (365 * $jy)
            + (intdiv($jy, 33) * 8)
            + intdiv((($jy % 33) + 3), 4)
            + $jd
            + ($jm < 7 ? ($jm - 1) * 31 : (($jm - 7) * 30 + 186));

        $gy = 400 * intdiv($days, 146097);
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100 * intdiv(--$days, 36524);
            $days %= 36524;
            if ($days >= 365) $days++;
        }
        $gy += 4 * intdiv($days, 1461);
        $days %= 1461;
        if ($days > 365) {
            $gy += intdiv(($days - 1), 365);
            $days = ($days - 1) % 365;
        }
        $gd = $days + 1;

        $isLeap = (($gy % 4 === 0 && $gy % 100 !== 0) || $gy % 400 === 0);
        $sal_a = [0, 31, $isLeap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $gm = 0;
        while ($gm < 13 && $gd > $sal_a[$gm]) {
            $gd -= $sal_a[$gm];
            $gm++;
        }
        return [$gy, $gm, $gd];
    }

    /**
     * تبدیل gregorian به جلالی.
     *
     * @return array{0:int,1:int,2:int} [jy, jm, jd]
     */
    public static function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666
            + (365 * $gy)
            + intdiv(($gy2 + 3), 4)
            - intdiv(($gy2 + 99), 100)
            + intdiv(($gy2 + 399), 400)
            + $gd + $g_d_m[$gm - 1];

        $jy = -1595 + 33 * intdiv($days, 12053);
        $days %= 12053;
        $jy += 4 * intdiv($days, 1461);
        $days %= 1461;
        if ($days > 365) {
            $jy += intdiv(($days - 1), 365);
            $days = ($days - 1) % 365;
        }
        if ($days < 186) {
            $jm = 1 + intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intdiv(($days - 186), 30);
            $jd = 1 + (($days - 186) % 30);
        }
        return [$jy, $jm, $jd];
    }

    /**
     * روز هفته در تقویم شمسی از روی تاریخ جلالی.
     * شنبه=0 ... جمعه=6
     */
    public static function jalaliWeekday(string $date): int
    {
        if (! self::isValidJalaliDate($date)) return -1;
        [$jy, $jm, $jd] = array_map('intval', explode('-', $date));
        [$gy, $gm, $gd] = self::jalaliToGregorian($jy, $jm, $jd);
        $w = (int) jdaytoweekday(gregoriantojd($gm, $gd, $gy));
        // jdaytoweekday در PHP: Sunday=0..Saturday=6 — همان convention JS
        return ($w + 1) % 7;
    }

    /**
     * اولین جمعه در یا بعد از یک تاریخ شمسی.
     */
    public static function firstFridayOnOrAfter(string $date): ?string
    {
        if (! self::isValidJalaliDate($date)) return null;
        [$jy, $jm, $jd] = array_map('intval', explode('-', $date));
        [$gy, $gm, $gd] = self::jalaliToGregorian($jy, $jm, $jd);
        $ts = mktime(12, 0, 0, $gm, $gd, $gy);
        // روز هفته در PHP: Sun=0..Sat=6, جمعه=5
        for ($i = 0; $i < 7; $i++) {
            $w = (int) date('w', $ts);
            if ($w === 5) { // Friday
                $gyn = (int) date('Y', $ts);
                $gmn = (int) date('n', $ts);
                $gdn = (int) date('j', $ts);
                [$jny, $jnm, $jnd] = self::gregorianToJalali($gyn, $gmn, $gdn);
                return sprintf('%04d-%02d-%02d', $jny, $jnm, $jnd);
            }
            $ts += 86400;
        }
        return null;
    }

    /**
     * اضافه کردن N روز به تاریخ جلالی.
     */
    public static function addDays(string $date, int $days): ?string
    {
        if (! self::isValidJalaliDate($date)) return null;
        [$jy, $jm, $jd] = array_map('intval', explode('-', $date));
        [$gy, $gm, $gd] = self::jalaliToGregorian($jy, $jm, $jd);
        $ts = mktime(12, 0, 0, $gm, $gd, $gy);
        $ts += $days * 86400;
        [$jyn, $jmn, $jdn] = self::gregorianToJalali(
            (int) date('Y', $ts),
            (int) date('n', $ts),
            (int) date('j', $ts)
        );
        return sprintf('%04d-%02d-%02d', $jyn, $jmn, $jdn);
    }

    /**
     * تولید همه جمعه‌ها در یک بازه.
     *
     * @return string[]  لیست تاریخ‌های جمعه (شمسی YYYY-MM-DD)
     */
    public static function allFridaysInRange(string $from, string $to): array
    {
        $firstFriday = self::firstFridayOnOrAfter($from);
        if (! $firstFriday) return [];
        if (strcmp($firstFriday, $to) > 0) return [];

        $fridays = [];
        $current = $firstFriday;
        $safety = 0;
        while (strcmp($current, $to) <= 0 && $safety < 500) {
            $fridays[] = $current;
            $next = self::addDays($current, 7);
            if (! $next) break;
            $current = $next;
            $safety++;
        }
        return $fridays;
    }
}
