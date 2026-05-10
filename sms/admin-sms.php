<?php

/**
 * SMS Admin — Settings & Test pages
 *
 * Supports both Kavenegar and MrOTP configuration.
 *
 * @package Rehub
 * @since   2.0.0
 */

defined('ABSPATH') || exit;

// ─── Menu registration ────────────────────────────────────────────────────────

add_action('admin_menu', function () {

    add_options_page(
        __('مدیریت SMS', 'rehub'),
        __('مدیریت SMS', 'rehub'),
        'manage_options',
        'rehub-sms-settings',
        'rehub_sms_settings_page'
    );

    add_management_page(
        __('تست SMS', 'rehub'),
        __('تست SMS', 'rehub'),
        'manage_options',
        'rehub-sms-test',
        'rehub_sms_test_page'
    );
});

// ─── Settings page ────────────────────────────────────────────────────────────

function rehub_sms_settings_page(): void
{
    if (! current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    // ── Save handler ──
    if (
        isset($_POST['rehub_sms_settings_nonce']) &&
        wp_verify_nonce($_POST['rehub_sms_settings_nonce'], 'rehub_sms_settings')
    ) {
        $fields = [
            // General
            'rehub_sms_active_provider'  => 'sanitize_text_field',
            // Kavenegar
            'rehub_sms_api_key'          => 'sanitize_text_field',
            'rehub_sms_default_sender'   => 'sanitize_text_field',
            'rehub_sms_otp_template'     => 'sanitize_text_field',
            // MrOTP
            'rehub_mrotp_api_key'        => 'sanitize_text_field',
            'rehub_mrotp_valid_time'     => 'absint',
            'rehub_mrotp_otp_length'     => 'absint',
            'rehub_mrotp_pattern_id'     => 'absint',
            'rehub_mrotp_default_type'   => 'sanitize_text_field',
        ];

        foreach ($fields as $key => $sanitizer) {
            if (isset($_POST[$key])) {
                update_option($key, $sanitizer($_POST[$key]));
            }
        }

        // Reset singleton so new keys take effect immediately
        \Rehub\Core\Services\Sms\Sms::resetInstance();

        echo '<div class="updated"><p>' . esc_html__('تنظیمات با موفقیت ذخیره شد.', 'rehub') . '</p></div>';
    }

    // ── Read current values ──
    $activeProvider   = get_option('rehub_sms_active_provider', 'kavenegar');
    $kavApiKey        = get_option('rehub_sms_api_key', '');
    $kavSender        = get_option('rehub_sms_default_sender', '');
    $kavTemplate      = get_option('rehub_sms_otp_template', '');
    $mrApiKey         = get_option('rehub_mrotp_api_key', '');
    $mrValidTime      = (int) get_option('rehub_mrotp_valid_time', 5);
    $mrOtpLength      = (int) get_option('rehub_mrotp_otp_length', 6);
    $mrPatternId      = (int) get_option('rehub_mrotp_pattern_id', 0);
    $mrDefaultType    = get_option('rehub_mrotp_default_type', 'USSD');

    // ── Check DB tables ──
    global $wpdb;
    $otpTable    = $wpdb->prefix . 'hdm_otp_codes';
    $tablesExist = $wpdb->get_var("SHOW TABLES LIKE '{$otpTable}'") === $otpTable;

    // ── Credit check ──
    $kavCredit = $mrCredit = null;
    try {
        $sms = \Rehub\Core\Services\Sms\Sms::getInstance();
        if ($kavApiKey) {
            $kavCredit = number_format($sms->provider('kavenegar')->getCredit());
        }
        if ($mrApiKey) {
            $mrCredit  = number_format($sms->provider('mrotp')->getCredit());
        }
    } catch (\Exception $e) {
        // silently skip credit display on error
    }

    ?>
    <div class="wrap" style="direction:rtl">
        <h1><?php esc_html_e('مدیریت سرویس SMS', 'rehub'); ?></h1>

        <?php if (! $tablesExist) : ?>
            <div class="notice notice-error">
                <p><strong><?php esc_html_e('⚠️ جداول OTP ایجاد نشده‌اند!', 'rehub'); ?></strong></p>
                <form method="post">
                    <?php wp_nonce_field('create_otp_tables', 'create_tables_nonce'); ?>
                    <button type="submit" class="button button-primary"><?php esc_html_e('ایجاد جداول دیتابیس', 'rehub'); ?></button>
                </form>
            </div>
        <?php else : ?>
            <div class="notice notice-success inline"><p>✅ <?php esc_html_e('جداول OTP آماده است.', 'rehub'); ?></p></div>
        <?php endif; ?>

        <?php if (isset($_POST['create_tables_nonce']) && wp_verify_nonce($_POST['create_tables_nonce'], 'create_otp_tables')) :
            require_once get_template_directory() . '/sms/OtpSchema.php';
            \Rehub\Core\Services\Sms\OtpSchema::install();
            \Rehub\Core\Services\Sms\OtpSchema::scheduleCleanup();
            echo '<div class="updated"><p>' . esc_html__('جداول ایجاد شدند.', 'rehub') . '</p></div>';
        endif; ?>

        <form method="post">
            <?php wp_nonce_field('rehub_sms_settings', 'rehub_sms_settings_nonce'); ?>

            <?php /* ─── General ─── */ ?>
            <h2><?php esc_html_e('تنظیمات عمومی', 'rehub'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="rehub_sms_active_provider"><?php esc_html_e('پروایدر پیش‌فرض', 'rehub'); ?></label></th>
                    <td>
                        <select name="rehub_sms_active_provider" id="rehub_sms_active_provider">
                            <option value="kavenegar" <?php selected($activeProvider, 'kavenegar'); ?>>Kavenegar</option>
                            <option value="mrotp"     <?php selected($activeProvider, 'mrotp'); ?>>MrOTP (USSD/SMS/Voice)</option>
                        </select>
                        <p class="description"><?php esc_html_e('پروایدر پیش‌فرض برای ارسال SMS ساده. سیستم OTP به‌طور خودکار MrOTP را اگر کانفیگ شده باشد ترجیح می‌دهد.', 'rehub'); ?></p>
                    </td>
                </tr>
            </table>

            <hr>

            <?php /* ─── Kavenegar ─── */ ?>
            <h2>☎️ <?php esc_html_e('تنظیمات Kavenegar', 'rehub'); ?>
                <?php if ($kavCredit !== null) : ?>
                    <span style="font-size:13px; color:#2271b1; font-weight:normal; margin-right:12px">
                        💳 <?php echo esc_html($kavCredit); ?> <?php esc_html_e('ریال', 'rehub'); ?>
                    </span>
                <?php endif; ?>
            </h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="rehub_sms_api_key"><?php esc_html_e('API Key', 'rehub'); ?></label></th>
                    <td>
                        <input name="rehub_sms_api_key" type="text" id="rehub_sms_api_key" class="regular-text"
                               value="<?php echo esc_attr($kavApiKey); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label for="rehub_sms_default_sender"><?php esc_html_e('شماره فرستنده', 'rehub'); ?></label></th>
                    <td>
                        <input name="rehub_sms_default_sender" type="text" id="rehub_sms_default_sender" class="regular-text"
                               value="<?php echo esc_attr($kavSender); ?>" placeholder="2000660110" />
                    </td>
                </tr>
                <tr>
                    <th><label for="rehub_sms_otp_template"><?php esc_html_e('نام قالب OTP', 'rehub'); ?></label></th>
                    <td>
                        <input name="rehub_sms_otp_template" type="text" id="rehub_sms_otp_template" class="regular-text"
                               value="<?php echo esc_attr($kavTemplate); ?>" placeholder="otp_verify" />
                        <p class="description"><?php esc_html_e('نام قالب تأیید‌شده در پنل Kavenegar.', 'rehub'); ?></p>
                    </td>
                </tr>
            </table>

            <hr>

            <?php /* ─── MrOTP ─── */ ?>
            <h2>📱 <?php esc_html_e('تنظیمات MrOTP (USSD / SMS / Voice)', 'rehub'); ?>
                <?php if ($mrCredit !== null) : ?>
                    <span style="font-size:13px; color:#2271b1; font-weight:normal; margin-right:12px">
                        💳 <?php echo esc_html($mrCredit); ?> <?php esc_html_e('ریال', 'rehub'); ?>
                    </span>
                <?php endif; ?>
            </h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="rehub_mrotp_api_key"><?php esc_html_e('API Key مروتی‌پی', 'rehub'); ?></label></th>
                    <td>
                        <input name="rehub_mrotp_api_key" type="text" id="rehub_mrotp_api_key" class="regular-text"
                               value="<?php echo esc_attr($mrApiKey); ?>" />
                        <p class="description">
                            <?php esc_html_e('از پنل ', 'rehub'); ?>
                            <a href="https://my.mrotp.ir" target="_blank">my.mrotp.ir</a>
                            <?php esc_html_e(' دریافت کنید.', 'rehub'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th><label for="rehub_mrotp_default_type"><?php esc_html_e('روش پیش‌فرض ارسال OTP', 'rehub'); ?></label></th>
                    <td>
                        <select name="rehub_mrotp_default_type" id="rehub_mrotp_default_type">
                            <option value="USSD"  <?php selected($mrDefaultType, 'USSD'); ?>>USSD (کاربر کد می‌شماره‌گیری)</option>
                            <option value="SMS"   <?php selected($mrDefaultType, 'SMS'); ?>>SMS</option>
                            <option value="VOICE" <?php selected($mrDefaultType, 'VOICE'); ?>>Voice (پیامک صوتی)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="rehub_mrotp_valid_time"><?php esc_html_e('مدت اعتبار OTP (دقیقه)', 'rehub'); ?></label></th>
                    <td>
                        <input name="rehub_mrotp_valid_time" type="number" id="rehub_mrotp_valid_time"
                               min="1" max="1440" value="<?php echo esc_attr($mrValidTime); ?>" style="width:80px" />
                        <p class="description"><?php esc_html_e('بین ۱ تا ۱۴۴۰ دقیقه (۲۴ ساعت).', 'rehub'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="rehub_mrotp_otp_length"><?php esc_html_e('تعداد رقم OTP تصادفی', 'rehub'); ?></label></th>
                    <td>
                        <input name="rehub_mrotp_otp_length" type="number" id="rehub_mrotp_otp_length"
                               min="3" max="8" value="<?php echo esc_attr($mrOtpLength); ?>" style="width:80px" />
                        <p class="description"><?php esc_html_e('بین ۳ تا ۸ رقم.', 'rehub'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="rehub_mrotp_pattern_id"><?php esc_html_e('شناسه قالب SMS پترن', 'rehub'); ?></label></th>
                    <td>
                        <input name="rehub_mrotp_pattern_id" type="number" id="rehub_mrotp_pattern_id"
                               min="0" value="<?php echo esc_attr($mrPatternId); ?>" style="width:120px" />
                        <p class="description"><?php esc_html_e('شناسه عددی قالب تأیید‌شده در پنل MrOTP (برای ارسال SMS پترن).', 'rehub'); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('ذخیره تنظیمات', 'rehub')); ?>
        </form>

        <?php /* ─── USSD info ─── */ ?>
        <?php if ($mrApiKey) :
            try {
                $ussd = \Rehub\Core\Services\Sms\Sms::getInstance()->getUssdCode();
                if ($ussd) : ?>
                    <div class="card" style="max-width:600px; margin-top:20px; padding:16px; background:#f0f9ff; border-right:4px solid #2271b1;">
                        <h3 style="margin-top:0">📞 <?php esc_html_e('کد USSD فعال', 'rehub'); ?></h3>
                        <p style="font-size:22px; font-weight:bold; direction:ltr; text-align:center; letter-spacing:2px; color:#1d4ed8">
                            <?php echo esc_html($ussd); ?>
                        </p>
                        <p class="description"><?php esc_html_e('کاربران با شماره‌گیری این کد رمز یکبارمصرف خود را مشاهده می‌کنند.', 'rehub'); ?></p>
                    </div>
                <?php endif;
            } catch (\Exception $e) { /* skip */ }
        endif; ?>
    </div>
    <?php
}

// ─── Test page ────────────────────────────────────────────────────────────────

function rehub_sms_test_page(): void
{
    if (! current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    $result  = '';
    $testOtp = '';

    if (
        isset($_POST['rehub_sms_test_nonce']) &&
        wp_verify_nonce($_POST['rehub_sms_test_nonce'], 'rehub_sms_test')
    ) {
        $action   = sanitize_text_field($_POST['test_action'] ?? 'sms');
        $receptor = sanitize_text_field($_POST['receptor']    ?? '');
        $message  = sanitize_textarea_field($_POST['message'] ?? '');
        $sender   = sanitize_text_field($_POST['sender']      ?? '');

        try {
            $sms = \Rehub\Core\Services\Sms\Sms::getInstance();

            if ($action === 'sms') {
                // ── Plain SMS ──
                $results = $sms->send($receptor, $message, $sender ?: null);
                $first   = $results[0] ?? null;
                if ($first && $first->success) {
                    $result = '<div class="updated"><p>✅ ' . esc_html__('پیامک ارسال شد.', 'rehub')
                        . ' ID: ' . esc_html($first->messageId) . '</p></div>';
                } else {
                    $result = '<div class="error"><p>❌ ' . esc_html($first->statusMessage ?? 'خطا') . '</p></div>';
                }

            } elseif ($action === 'otp_send') {
                // ── Send OTP ──
                $type    = sanitize_text_field($_POST['otp_type'] ?? 'USSD');
                $otpResult = $sms->sendOtp($receptor, '', '', $type);
                if ($otpResult->success) {
                    $testOtp = $otpResult->otp;
                    $result  = '<div class="updated"><p>✅ '
                        . esc_html__('OTP ارسال شد.', 'rehub')
                        . ($otpResult->otp     ? ' کد: <strong>' . esc_html($otpResult->otp) . '</strong>' : '')
                        . ($otpResult->ussdCode ? ' | USSD: <strong>' . esc_html($otpResult->ussdCode) . '</strong>' : '')
                        . '</p></div>';
                } else {
                    $result = '<div class="error"><p>❌ [' . esc_html($otpResult->errorCode) . '] '
                        . esc_html($otpResult->message) . '</p></div>';
                }

            } elseif ($action === 'otp_verify') {
                // ── Verify OTP ──
                $otp       = sanitize_text_field($_POST['otp_code'] ?? '');
                $verResult = $sms->verifyOtp($receptor, $otp);
                if ($verResult->accepted) {
                    $result = '<div class="updated"><p>✅ ' . esc_html__('کد OTP معتبر است!', 'rehub') . '</p></div>';
                } else {
                    $result = '<div class="error"><p>❌ [' . esc_html($verResult->errorCode) . '] '
                        . esc_html($verResult->message) . '</p></div>';
                }

            } elseif ($action === 'credit') {
                // ── Credit check ──
                $kavCredit = $mrCredit = null;
                try { $kavCredit = $sms->provider('kavenegar')->getCredit(); } catch (\Exception $e) {}
                try { $mrCredit  = $sms->provider('mrotp')->getCredit();     } catch (\Exception $e) {}

                $result = '<div class="updated"><p>';
                if ($kavCredit !== null) $result .= 'Kavenegar: <strong>' . number_format($kavCredit) . ' ریال</strong>  ';
                if ($mrCredit  !== null) $result .= 'MrOTP: <strong>'     . number_format($mrCredit)  . ' ریال</strong>';
                $result .= '</p></div>';
            }

        } catch (\Exception $e) {
            $result = '<div class="error"><p>❌ ' . esc_html($e->getMessage()) . '</p></div>';
        }
    }

    $defaultSender = get_option('rehub_sms_default_sender', '');
    ?>
    <div class="wrap" style="direction:rtl">
        <h1><?php esc_html_e('تست سرویس SMS', 'rehub'); ?></h1>
        <?php echo $result; // phpcs:ignore ?>

        <form method="post" style="max-width:700px">
            <?php wp_nonce_field('rehub_sms_test', 'rehub_sms_test_nonce'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="test_action"><?php esc_html_e('نوع تست', 'rehub'); ?></label></th>
                    <td>
                        <select name="test_action" id="test_action">
                            <option value="sms">ارسال SMS ساده</option>
                            <option value="otp_send">ارسال OTP</option>
                            <option value="otp_verify">تأیید OTP</option>
                            <option value="credit">بررسی موجودی</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="receptor"><?php esc_html_e('شماره گیرنده', 'rehub'); ?></label></th>
                    <td><input name="receptor" type="text" id="receptor" class="regular-text" placeholder="09121112233" /></td>
                </tr>
                <tr>
                    <th><label for="otp_type"><?php esc_html_e('روش OTP', 'rehub'); ?></label></th>
                    <td>
                        <select name="otp_type" id="otp_type">
                            <option value="USSD">USSD</option>
                            <option value="SMS">SMS</option>
                            <option value="VOICE">Voice</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="otp_code"><?php esc_html_e('کد OTP (برای تأیید)', 'rehub'); ?></label></th>
                    <td>
                        <input name="otp_code" type="text" id="otp_code" class="small-text"
                               placeholder="123456" value="<?php echo esc_attr($testOtp); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label for="sender"><?php esc_html_e('شماره فرستنده', 'rehub'); ?></label></th>
                    <td><input name="sender" type="text" id="sender" class="regular-text"
                               value="<?php echo esc_attr($defaultSender); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="message"><?php esc_html_e('متن پیام (SMS ساده)', 'rehub'); ?></label></th>
                    <td><textarea name="message" id="message" rows="4" class="large-text"></textarea></td>
                </tr>
            </table>

            <?php submit_button(__('اجرای تست', 'rehub')); ?>
        </form>
    </div>
    <?php
}