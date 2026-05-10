<?php

namespace Tamir\Notification;

/**
 * SmsService — ارسال پیامک از طریق پنل‌های ایرانی
 *
 * پشتیبانی از: ملی‌پیامک، کاوه‌نگار، SMS.ir، فراپیامک
 * برای تغییر provider فقط PROVIDER_KEY را عوض کن
 *
 * تنظیمات در wp-admin → تنظیمات → تعمیرآنلاین
 */
class SmsService
{
    // ── Provider های پشتیبانی‌شده ─────────────────────────────
    const PROVIDER_MELIPAYAMAK = 'melipayamak';   // ملی‌پیامک
    const PROVIDER_KAVENEGAR   = 'kavenegar';     // کاوه‌نگار
    const PROVIDER_SMSIR       = 'smsir';         // SMS.ir
    const PROVIDER_FARAPAYAMAK = 'farapayamak';   // فراپیامک

    private $provider;
    private $apiKey;
    private $lineNumber;
    private $username;
    private $password;

    public function __construct()
    {
        $this->provider   = get_option('tamir_sms_provider',     self::PROVIDER_MELIPAYAMAK);
        $this->apiKey     = get_option('tamir_sms_api_key',      '');
        $this->lineNumber = get_option('tamir_sms_line_number',  '');
        $this->username   = get_option('tamir_sms_username',     '');
        $this->password   = get_option('tamir_sms_password',     '');
    }

    /**
     * ارسال پیامک
     *
     * @param  string $to      شماره گیرنده (با 09 شروع می‌شود)
     * @param  string $message متن پیامک
     * @return array ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function send(string $to, string $message): array
    {
        // اگر پنل SMS تنظیم نشده، فقط log کن
        if (empty($this->apiKey) && empty($this->username)) {
            error_log('[TamirSMS] Not configured. Would send to ' . $to . ': ' . $message);
            return array('success' => false, 'message_id' => null, 'error' => 'SMS not configured');
        }

        $to = $this->normalizeNumber($to);
        if (!$to) {
            return array('success' => false, 'message_id' => null, 'error' => 'Invalid phone number');
        }

        switch ($this->provider) {
            case self::PROVIDER_MELIPAYAMAK:
                return $this->sendMelipayamak($to, $message);
            case self::PROVIDER_KAVENEGAR:
                return $this->sendKavenegar($to, $message);
            case self::PROVIDER_SMSIR:
                return $this->sendSmsIr($to, $message);
            case self::PROVIDER_FARAPAYAMAK:
                return $this->sendFarapayamak($to, $message);
            default:
                return array('success' => false, 'message_id' => null, 'error' => 'Unknown provider');
        }
    }

    // ── ملی‌پیامک ─────────────────────────────────────────────
    private function sendMelipayamak(string $to, string $text): array
    {
        $response = wp_remote_post('https://rest.payamak-panel.com/api/SendSMS/SendSMS', array(
            'timeout' => 10,
            'headers' => array('Content-Type' => 'application/json'),
            'body'    => wp_json_encode(array(
                'username' => $this->username,
                'password' => $this->password,
                'to'       => $to,
                'from'     => $this->lineNumber,
                'text'     => $text,
                'isFlash'  => false,
            )),
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message_id' => null, 'error' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $ok   = isset($body['Value']) && (int) $body['Value'] > 0;

        return array(
            'success'    => $ok,
            'message_id' => $ok ? (string) $body['Value'] : null,
            'error'      => $ok ? null : ($body['RetStatus'] ?? 'Unknown error'),
        );
    }

    // ── کاوه‌نگار ──────────────────────────────────────────────
    private function sendKavenegar(string $to, string $text): array
    {
        $url = 'https://api.kavenegar.com/v1/' . $this->apiKey . '/sms/send.json';

        $response = wp_remote_post($url, array(
            'timeout' => 10,
            'body'    => array(
                'receptor' => $to,
                'sender'   => $this->lineNumber,
                'message'  => $text,
            ),
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message_id' => null, 'error' => $response->get_error_message());
        }

        $body   = json_decode(wp_remote_retrieve_body($response), true);
        $status = $body['return']['status'] ?? 0;
        $ok     = $status === 200;

        $msgId = null;
        if ($ok && !empty($body['entries'][0]['messageid'])) {
            $msgId = (string) $body['entries'][0]['messageid'];
        }

        return array(
            'success'    => $ok,
            'message_id' => $msgId,
            'error'      => $ok ? null : ($body['return']['message'] ?? 'Unknown error'),
        );
    }

    // ── SMS.ir ─────────────────────────────────────────────────
    private function sendSmsIr(string $to, string $text): array
    {
        $response = wp_remote_post('https://api.sms.ir/v1/send/bulk', array(
            'timeout' => 10,
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key'    => $this->apiKey,
            ),
            'body'    => wp_json_encode(array(
                'lineNumber' => $this->lineNumber,
                'mobiles'    => array($to),
                'messageText'=> $text,
            )),
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message_id' => null, 'error' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $ok   = isset($body['status']) && $body['status'] === 1;

        return array(
            'success'    => $ok,
            'message_id' => $ok && !empty($body['data'][0]) ? (string) $body['data'][0] : null,
            'error'      => $ok ? null : ($body['message'] ?? 'Unknown error'),
        );
    }

    // ── فراپیامک ───────────────────────────────────────────────
    private function sendFarapayamak(string $to, string $text): array
    {
        $response = wp_remote_post('https://api.payam-resan.com/api/MessageSend', array(
            'timeout' => 10,
            'headers' => array('Content-Type' => 'application/json'),
            'body'    => wp_json_encode(array(
                'UserName'    => $this->username,
                'Password'    => $this->password,
                'PhoneNumber' => $this->lineNumber,
                'Message'     => $text,
                'RecNumber'   => $to,
                'Smsclass'    => 1,
            )),
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message_id' => null, 'error' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $ok   = isset($body['IsSuccessful']) && $body['IsSuccessful'] === true;

        return array(
            'success'    => $ok,
            'message_id' => $ok ? ($body['RetStatus'] ?? null) : null,
            'error'      => $ok ? null : ($body['ErrorMessage'] ?? 'Unknown error'),
        );
    }

    // ── normalize شماره ────────────────────────────────────────
    private function normalizeNumber(string $num): string
    {
        $num = preg_replace('/[^0-9]/', '', $num);
        if (strlen($num) === 10 && substr($num, 0, 1) === '9') {
            $num = '0' . $num;
        }
        if (strlen($num) === 12 && substr($num, 0, 2) === '98') {
            $num = '0' . substr($num, 2);
        }
        if (strlen($num) !== 11 || substr($num, 0, 2) !== '09') {
            return '';
        }
        return $num;
    }
}
