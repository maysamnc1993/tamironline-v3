<?php

/**
 * صفحه‌ی "ابزار زمانبندی سریع" در admin
 *
 *  - تولید خودکار جمعه‌ها در یک بازه
 *  - افزودن گروهی تعطیلات رسمی (از روی textarea)
 *
 * مسیر: مدیریت → تعطیلات → ابزار زمانبندی سریع
 *
 * @package Tamir\Holidays
 */

namespace Tamir\Holidays;

defined('ABSPATH') || exit;

final class HolidayBulkTools
{
    public const PAGE_SLUG    = 'tamir-holiday-bulk';
    public const NONCE_ACTION = 'tamir_holiday_bulk';

    public static function register(): void
    {
        add_submenu_page(
            'edit.php?post_type=' . HolidayPostType::POST_TYPE,
            'ابزار زمانبندی سریع',
            'ابزار زمانبندی سریع',
            'edit_posts',
            self::PAGE_SLUG,
            [self::class, 'render']
        );
    }

    public static function render(): void
    {
        if (! current_user_can('edit_posts')) {
            wp_die('دسترسی ندارید.');
        }

        // Handle form submission
        $notice = self::handleSubmit();
        $slots = TimeSlotsConfig::all();
?>
        <div class="wrap">
            <h1>ابزار زمانبندی سریع</h1>

            <?php if ($notice): ?>
                <div class="notice notice-<?php echo esc_attr($notice['type']); ?> is-dismissible">
                    <p><?php echo wp_kses_post($notice['message']); ?></p>
                </div>
            <?php endif; ?>

            <style>
                .tamir-bulk-card {
                    background: #fff;
                    border: 1px solid #ccd0d4;
                    border-radius: 6px;
                    padding: 20px;
                    margin-top: 16px;
                    max-width: 760px;
                    box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                }

                .tamir-bulk-card h2 {
                    margin-top: 0;
                    font-size: 16px;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 10px;
                }

                .tamir-bulk-card .row {
                    margin-bottom: 14px;
                }

                .tamir-bulk-card label {
                    font-weight: 600;
                    display: block;
                    margin-bottom: 4px;
                }

                .tamir-bulk-card input[type=text],
                .tamir-bulk-card textarea,
                .tamir-bulk-card select {
                    font-size: 14px;
                    padding: 6px 10px;
                    direction: ltr;
                    text-align: center;
                }

                .tamir-bulk-card textarea {
                    width: 100%;
                    min-height: 160px;
                    font-family: ui-monospace, monospace;
                    direction: ltr;
                    text-align: left;
                }

                .tamir-hint {
                    color: #666;
                    font-size: 12px;
                    margin-top: 4px;
                }

                .tamir-slots-inline {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-top: 6px;
                }

                .tamir-slots-inline label {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-weight: normal;
                    cursor: pointer;
                    padding: 6px 12px;
                    background: #f6f7f7;
                    border: 1px solid #dcdcde;
                    border-radius: 6px;
                }

                .tamir-slots-inline input[type=checkbox]:checked+span {
                    color: #2271b1;
                    font-weight: 600;
                }

                .tamir-slots-inline label:has(input:checked) {
                    background: #f0f6fc;
                    border-color: #2271b1;
                }
            </style>

            <!-- ═════════════════════════════════════════════════ -->
            <!--  ۱. تولید جمعه‌ها                                -->
            <!-- ═════════════════════════════════════════════════ -->
            <div class="tamir-bulk-card">
                <h2>📅 تولید خودکار جمعه‌ها</h2>
                <p class="tamir-hint">تمام جمعه‌های بازه‌ی وارد شده را یکجا ایجاد می‌کند (از جمعه‌های موجود رد می‌شود).</p>

                <form method="post">
                    <?php wp_nonce_field(self::NONCE_ACTION); ?>
                    <input type="hidden" name="tamir_bulk_action" value="generate_fridays">

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="row">
                            <label>از تاریخ (شمسی)</label>
                            <input type="text" name="from_date" placeholder="1405-01-01" value="<?php echo esc_attr(self::defaultFromDate()); ?>" required dir="ltr" style="width:100%">
                        </div>
                        <div class="row">
                            <label>تا تاریخ (شمسی)</label>
                            <input type="text" name="to_date" placeholder="1405-12-29" value="<?php echo esc_attr(self::defaultToDate()); ?>" required dir="ltr" style="width:100%">
                        </div>
                    </div>

                    <div class="row">
                        <label>عنوان تعطیلی برای هر جمعه</label>
                        <input type="text" name="friday_title" value="جمعه" style="width:100%" required>
                    </div>

                    <div class="row">
                        <label>بازه‌هایی که در هر جمعه <strong>باز</strong> باشند</label>
                        <div class="tamir-slots-inline">
                            <?php foreach ($slots as $s): ?>
                                <label>
                                    <input type="checkbox" name="open_slots[]" value="<?php echo esc_attr($s['value']); ?>">
                                    <span><?php echo esc_html($s['label']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="tamir-hint">اگر هیچ‌کدام تیک نخورد، همه‌ی جمعه‌ها کاملاً تعطیل خواهند بود.</p>
                    </div>

                    <p>
                        <button type="submit" class="button button-primary">تولید جمعه‌ها</button>
                    </p>
                </form>
            </div>

            <!-- ═════════════════════════════════════════════════ -->
            <!--  ۲. افزودن گروهی تعطیلات رسمی                    -->
            <!-- ═════════════════════════════════════════════════ -->
            <div class="tamir-bulk-card">
                <h2>📋 افزودن گروهی تعطیلات رسمی</h2>
                <p class="tamir-hint">
                    هر خط یک تعطیلی. فرمت: <code>1405-01-13 | عنوان تعطیلی</code>
                </p>

                <form method="post">
                    <?php wp_nonce_field(self::NONCE_ACTION); ?>
                    <input type="hidden" name="tamir_bulk_action" value="bulk_holidays">

                    <div class="row">
                        <label>لیست تعطیلات</label>
                        <textarea name="holidays_list" placeholder="1405-01-01 | عید نوروز
1405-01-02 | عید نوروز
1405-01-13 | روز طبیعت
1405-02-03 | شهادت امام علی (ع)" required></textarea>
                        <p class="tamir-hint">تاریخ‌های تکراری با رکوردهای موجود نادیده گرفته می‌شوند.</p>
                    </div>

                    <div class="row">
                        <label>پیش‌فرض بازه‌های باز برای همه (قابل ویرایش بعد از ایجاد)</label>
                        <div class="tamir-slots-inline">
                            <?php foreach ($slots as $s): ?>
                                <label>
                                    <input type="checkbox" name="open_slots[]" value="<?php echo esc_attr($s['value']); ?>">
                                    <span><?php echo esc_html($s['label']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="tamir-hint">اگر هیچ‌کدام تیک نخورد، همه‌ی تعطیلات به صورت کاملاً تعطیل ایجاد می‌شوند.</p>
                    </div>

                    <p>
                        <button type="submit" class="button button-primary">افزودن همه</button>
                    </p>
                </form>
            </div>
        </div>
<?php
    }

    /**
     * @return array{type:string,message:string}|null
     */
    private static function handleSubmit(): ?array
    {
        if (! isset($_POST['tamir_bulk_action'])) return null;
        if (! check_admin_referer(self::NONCE_ACTION)) return null;
        if (! current_user_can('edit_posts')) return null;

        $action = sanitize_text_field(wp_unslash($_POST['tamir_bulk_action']));

        $openSlots = $_POST['open_slots'] ?? [];
        if (! is_array($openSlots)) $openSlots = [];
        $openSlots = array_map(function ($s) {
            return sanitize_text_field((string) $s);
        }, $openSlots);
        $openSlots = TimeSlotsConfig::sanitizeList($openSlots);

        if ($action === 'generate_fridays') {
            $from = sanitize_text_field(wp_unslash($_POST['from_date'] ?? ''));
            $to   = sanitize_text_field(wp_unslash($_POST['to_date']   ?? ''));
            $title = sanitize_text_field(wp_unslash($_POST['friday_title'] ?? 'جمعه'));

            $from = self::toEn($from);
            $to   = self::toEn($to);

            if (
                ! HolidayRepository::isValidJalaliDate($from) ||
                ! HolidayRepository::isValidJalaliDate($to)
            ) {
                return ['type' => 'error', 'message' => 'تاریخ‌های وارد شده نامعتبر هستند.'];
            }
            if (strcmp($from, $to) > 0) {
                return ['type' => 'error', 'message' => 'تاریخ شروع باید قبل از تاریخ پایان باشد.'];
            }

            $fridays = HolidayRepository::allFridaysInRange($from, $to);
            $created = 0;
            $skipped = 0;
            foreach ($fridays as $f) {
                $id = HolidayRepository::create($f, $title, $openSlots);
                if ($id) $created++;
                else $skipped++;
            }
            HolidayCacheBuster::flush();
            return [
                'type'    => $created > 0 ? 'success' : 'warning',
                'message' => sprintf(
                    '<strong>%d</strong> جمعه ایجاد شد. %d جمعه از قبل وجود داشت و نادیده گرفته شد.',
                    $created,
                    $skipped
                ),
            ];
        }

        if ($action === 'bulk_holidays') {
            $raw = (string) wp_unslash($_POST['holidays_list'] ?? '');
            $lines = preg_split('/\r\n|\r|\n/', $raw);
            if (! $lines) $lines = [];

            $created = 0;
            $skipped = 0;
            $invalid = 0;
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;

                // تبدیل ارقام فارسی
                $line = self::toEn($line);
                $parts = array_map('trim', explode('|', $line, 2));
                $date  = $parts[0] ?? '';
                $title = $parts[1] ?? $date;

                if (! HolidayRepository::isValidJalaliDate($date)) {
                    $invalid++;
                    continue;
                }
                $id = HolidayRepository::create($date, $title, $openSlots);
                if ($id) $created++;
                else $skipped++;
            }
            HolidayCacheBuster::flush();
            return [
                'type' => $created > 0 ? 'success' : 'warning',
                'message' => sprintf(
                    '<strong>%d</strong> تعطیلی ایجاد شد. %d تکراری نادیده گرفته شد. %d خط نامعتبر بود.',
                    $created,
                    $skipped,
                    $invalid
                ),
            ];
        }

        return null;
    }

    private static function toEn(string $s): string
    {
        $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace(array_merge($fa, $ar), array_merge($en, $en), $s);
    }

    private static function defaultFromDate(): string
    {
        $g = getdate();
        [$jy, $jm, $jd] = HolidayRepository::gregorianToJalali($g['year'], $g['mon'], $g['mday']);
        return sprintf('%04d-%02d-%02d', $jy, $jm, $jd);
    }

    private static function defaultToDate(): string
    {
        $from = self::defaultFromDate();
        [$jy, $jm, $jd] = array_map('intval', explode('-', $from));
        // پایان سال شمسی (۲۹ اسفند — اگر ۳۰ اسفند معتبر نبود، ۲۹)
        return sprintf('%04d-12-29', $jy);
    }
}
