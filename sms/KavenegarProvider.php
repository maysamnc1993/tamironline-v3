<?php

namespace Rehub\Core\Services\Sms;

use Rehub\Core\Services\Sms\AbstractSmsProvider;
use Rehub\Core\Services\Sms\OtpResult;
use Rehub\Core\Services\Sms\SendResult;
use Rehub\Core\Services\Sms\VerifyResult;
use Rehub\Core\Services\Sms\OtpException;
use Rehub\Core\Services\Sms\OtpSchema;
use Rehub\Core\Services\Sms\ProviderRequestException;

/**
 * Kavenegar SMS Provider — با پشتیبانی پراکسی
 *
 * ──────────────────────────────────────────────────────────────────
 *  چرا پراکسی؟
 * ──────────────────────────────────────────────────────────────────
 *
 *  سرور تامیر به دلیل محدودیت شبکه نمی‌تواند مستقیماً به
 *  api.kavenegar.com وصل شود (cURL timeout). راه حل: استفاده از
 *  یک پراکسی واسط (api.ganjemarket.com/sms-proxy.php) که درخواست
 *  را دریافت و به Kavenegar forward می‌کند.
 *
 * ──────────────────────────────────────────────────────────────────
 *  پیکربندی پراکسی
 * ──────────────────────────────────────────────────────────────────
 *
 *  این provider از دو روش پشتیبانی می‌کند:
 *
 *  حالت 1 — Proxy mode (پیش‌فرض اگر URL پراکسی پیکربندی شده باشد):
 *      درخواست‌ها به URL پراکسی POST می‌شوند با payload JSON.
 *      Header امنیتی X-Proxy-Secret اضافه می‌شود.
 *
 *  حالت 2 — Direct mode (اگر پراکسی پیکربندی نشده باشد):
 *      درخواست‌ها مستقیماً به api.kavenegar.com می‌روند.
 *
 *  پیکربندی از طریق WordPress options:
 *   - rehub_sms_proxy_url     ← URL پراکسی (مثل https://api.ganjemarket.com/sms-proxy.php)
 *   - rehub_sms_proxy_secret  ← Secret header
 *
 *  اگر هیچ‌کدام تنظیم نشده باشد، fallback به direct mode.
 *
 * ──────────────────────────────────────────────────────────────────
 *  OTP Workflow (server-side model)
 * ──────────────────────────────────────────────────────────────────
 *
 *  ۱. Backend خودش OTP می‌سازد (5 رقمی random)
 *  ۲. در hdm_otp_codes ذخیره می‌کند
 *  ۳. از طریق پراکسی → Kavenegar template ارسال می‌کند
 *  ۴. هنگام verify، از hdm_otp_codes چک می‌کند
 * ──────────────────────────────────────────────────────────────────
 */
final class KavenegarProvider extends AbstractSmsProvider
{
    /** Direct base URL (در حالت بدون پراکسی) */
    const BASE_URL = 'https://api.kavenegar.com/v1/';

    /** Status code که Kavenegar در صورت موفقیت برمی‌گرداند */
    const STATUS_OK = 200;

    /** WP option keys */
    const OPT_PROXY_URL    = 'rehub_sms_proxy_url';
    const OPT_PROXY_SECRET = 'rehub_sms_proxy_secret';

    public function getName(): string
    {
        return 'kavenegar';
    }

    public function supportsOtp(): bool
    {
        return true;
    }

    // ══════════════════════════════════════════════════════════
    //  Proxy helpers
    // ══════════════════════════════════════════════════════════

    /**
     * آیا پراکسی پیکربندی شده؟
     */
    private function isProxyEnabled(): bool
    {
        $url = trim((string) get_option(self::OPT_PROXY_URL, ''));
        return $url !== '';
    }

    /**
     * URL پراکسی
     */
    private function getProxyUrl(): string
    {
        return trim((string) get_option(self::OPT_PROXY_URL, ''));
    }

    /**
     * Secret پراکسی
     */
    private function getProxySecret(): string
    {
        return trim((string) get_option(self::OPT_PROXY_SECRET, ''));
    }

    /**
     * فرستادن درخواست به پراکسی.
     *
     * Payload format:
     *   {
     *     "action": "verify" | "send" | "account_info" | ...,
     *     "params": {api_key, ...کاوه‌نگار params}
     *   }
     *
     * @throws ProviderRequestException
     * @return array  پاسخ پراکسی (به‌صورت JSON decoded)
     */
    private function callProxy(string $action, array $params, int $timeout = 30): array
    {
        $url    = $this->getProxyUrl();
        $secret = $this->getProxySecret();

        $payload = [
            'action' => $action,
            'params' => array_merge(
                ['api_key' => $this->apiKey],
                $params
            ),
        ];

        $args = [
            'timeout'     => $timeout,
            'redirection' => 5,
            'httpversion' => '1.1',
            'headers'     => [
                'Content-Type'   => 'application/json',
                'Accept'         => 'application/json',
                'X-Proxy-Secret' => $secret,
            ],
            'body'        => wp_json_encode($payload),
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            throw new ProviderRequestException(
                sprintf('[%s-proxy] HTTP error: %s', $this->getName(), $response->get_error_message()),
                0,
                $this->getName()
            );
        }

        $statusCode = (int) wp_remote_retrieve_response_code($response);
        $rawBody    = wp_remote_retrieve_body($response);

        $body = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ProviderRequestException(
                sprintf(
                    '[%s-proxy] Non-JSON response (HTTP %d): %s',
                    $this->getName(),
                    $statusCode,
                    substr($rawBody, 0, 200)
                ),
                $statusCode,
                $this->getName()
            );
        }

        // لاگ پاسخ پراکسی برای دیباگ (در error_log)
        error_log(sprintf(
            '[Kavenegar-proxy] action=%s, http=%d, body=%s',
            $action,
            $statusCode,
            substr($rawBody, 0, 500)
        ));

        return $body ? $body : [];
    }

    /**
     * نرمالایز پاسخ پراکسی به فرمت Kavenegar.
     *
     * چون فرمت دقیق پراکسی مشخص نیست، این متد هر دو حالت ممکن را
     * هندل می‌کند:
     *
     * حالت A — پراکسی عیناً پاسخ Kavenegar را برمی‌گرداند:
     *     {return: {status: 200, message: ...}, entries: [...]}
     *
     * حالت B — پراکسی فرمت ساده‌سازی شده دارد:
     *     {success: true, message: ..., data: {...}}
     *
     * @return array{status:int, message:string, entries:array}
     */
    private function normalizeProxyResponse(array $response): array
    {
        // حالت A: پاسخ Kavenegar عیناً
        if (isset($response['return'])) {
            return [
                'status'  => (int)    ($response['return']['status']  ?? 0),
                'message' => (string) ($response['return']['message'] ?? ''),
                'entries' => (array)  ($response['entries'] ?? $response['return']['entries'] ?? []),
            ];
        }

        // حالت B: پراکسی wrapper خودش دارد
        if (isset($response['success'])) {
            $success = (bool) $response['success'];
            $msg     = (string) ($response['message'] ?? '');

            // اگر داده‌ی Kavenegar در زیر کلید data هست
            if (isset($response['data']['return'])) {
                return [
                    'status'  => (int)    ($response['data']['return']['status']  ?? ($success ? 200 : 0)),
                    'message' => (string) ($response['data']['return']['message'] ?? $msg),
                    'entries' => (array)  ($response['data']['entries'] ?? $response['data']['return']['entries'] ?? []),
                ];
            }

            return [
                'status'  => $success ? 200 : 0,
                'message' => $msg,
                'entries' => (array) ($response['data']['entries'] ?? []),
            ];
        }

        // فرمت ناشناخته — log می‌کنیم
        error_log('[Kavenegar-proxy] Unknown response format: ' . substr(wp_json_encode($response), 0, 300));

        return [
            'status'  => 0,
            'message' => 'Unknown proxy response format',
            'entries' => [],
        ];
    }

    // ══════════════════════════════════════════════════════════
    //  Plain SMS
    // ══════════════════════════════════════════════════════════

    public function send($receptor, string $message, $sender = null): array
    {
        $sender = $sender ? $sender : get_option('rehub_sms_default_sender', '');
        $receptors = is_array($receptor) ? $receptor : [$receptor];

        foreach ($receptors as $m) {
            $this->assertValidMobile($m);
        }

        $params = [
            'receptor' => implode(',', $receptors),
            'message'  => $message,
            'sender'   => $sender,
        ];

        try {
            if ($this->isProxyEnabled()) {
                $raw = $this->callProxy('send', $params);
                $body = $this->normalizeProxyResponse($raw);
                $entries = $body['entries'];
                $overallStatus = $body['status'];
            } else {
                // Direct mode (fallback)
                $body = $this->httpPost(
                    self::BASE_URL . $this->apiKey . '/sms/send.json',
                    $params
                );
                $entries = isset($body['return']['entries']) ? $body['return']['entries'] : [];
                $overallStatus = (int) (isset($body['return']['status']) ? $body['return']['status'] : 0);
            }
        } catch (ProviderRequestException $e) {
            // در صورت خطای ارتباطی، fail برای همه receptorها برگردان
            $results = [];
            foreach ($receptors as $m) {
                $results[] = SendResult::fail($m, $e->getProviderCode(), $e->getMessage());
            }
            return $results;
        }

        $results = [];
        foreach ($entries as $entry) {
            $status = (int)    (isset($entry['status'])    ? $entry['status']    : 0);
            $mid    = (string) (isset($entry['messageid']) ? $entry['messageid'] : '');
            $rec    = (string) (isset($entry['receptor'])  ? $entry['receptor']  : '');

            $results[] = $status === self::STATUS_OK
                ? SendResult::ok($mid, $rec, $status, 'Sent')
                : SendResult::fail($rec, $status, isset($entry['statustext']) ? $entry['statustext'] : 'Failed');
        }

        if (empty($results) && $overallStatus === self::STATUS_OK) {
            foreach ($receptors as $m) {
                $results[] = SendResult::ok('', $m, self::STATUS_OK, 'Queued');
            }
        }

        return $results;
    }

    // ══════════════════════════════════════════════════════════
    //  OTP — sendOtp (low-level, از پراکسی استفاده می‌کند)
    // ══════════════════════════════════════════════════════════

    public function sendOtp(string $mobile, string $token, string $template, string $type = 'sms'): OtpResult
    {
        $this->assertValidMobile($mobile);

        if (empty($template)) {
            $template = (string) get_option('rehub_sms_otp_template', '');
        }

        if (empty($template)) {
            return OtpResult::fail(
                -1,
                'No OTP template configured. Set rehub_sms_otp_template in SMS settings.',
                $this->getName()
            );
        }

        $kavType = $this->normalizeKavenegarType($type);

        $params = [
            'receptor' => $mobile,
            'token'    => $token,
            'template' => $template,
            'type'     => $kavType,
        ];

        try {
            if ($this->isProxyEnabled()) {
                // ── از طریق پراکسی ──────────────────────────
                $raw = $this->callProxy('verify', $params);
                $body = $this->normalizeProxyResponse($raw);

                if ($body['status'] !== self::STATUS_OK) {
                    return OtpResult::fail(
                        $body['status'],
                        $body['message'] ?: 'Unknown error',
                        $this->getName()
                    );
                }

                return OtpResult::ok($token, '', $this->getName(), 'OTP sent via Kavenegar proxy');
            }

            // ── Direct mode ─────────────────────────────────
            $body = $this->httpPost(
                self::BASE_URL . $this->apiKey . '/verify/lookup.json',
                $params
            );

            $status = (int) (isset($body['return']['status']) ? $body['return']['status'] : 0);

            if ($status !== self::STATUS_OK) {
                $msg = isset($body['return']['message']) ? $body['return']['message'] : 'Unknown error';
                return OtpResult::fail($status, $msg, $this->getName());
            }

            return OtpResult::ok($token, '', $this->getName(), 'OTP sent via Kavenegar');
        } catch (ProviderRequestException $e) {
            return OtpResult::fail($e->getProviderCode(), $e->getMessage(), $this->getName());
        }
    }

    // ══════════════════════════════════════════════════════════
    //  setRandomOtp — interface implementation
    // ══════════════════════════════════════════════════════════

    /**
     * ساخت + ذخیره + ارسال OTP در یک عملیات.
     *
     * این متد به‌صورت end-to-end کار می‌کند:
     *   ۱. OTP تصادفی با طول مشخص می‌سازد
     *   ۲. در hdm_otp_codes ذخیره می‌کند (با expire time)
     *   ۳. از طریق پراکسی (یا direct) به Kavenegar می‌فرستد
     *   ۴. در hdm_sms_log لاگ می‌کند
     */
    public function setRandomOtp(string $mobile, int $length = 5, int $validMinutes = 5, string $type = 'SMS'): OtpResult
    {
        $this->assertValidMobile($mobile);

        // ── Sanitize inputs ────────────────────────────────────
        $length       = max(3, min(8, $length));
        $validMinutes = max(1, min(30, $validMinutes));
        $type         = strtoupper(trim($type));

        // Kavenegar فقط SMS و VOICE — USSD ندارد
        if (!in_array($type, ['SMS', 'VOICE'], true)) {
            $type = 'SMS';
        }

        // ── 1. ساخت OTP تصادفی ─────────────────────────────────
        $otp = $this->generateRandomOtp($length);

        // ── 2. ذخیره در DB (قبل از send) ───────────────────────
        $otpId = OtpSchema::insertOtp(
            $mobile,
            $otp,
            $validMinutes,
            $this->getName(),
            $type,
            '' // Kavenegar USSD code ندارد
        );

        if ($otpId === false) {
            $this->logFailure($mobile, $type, 0, 'Failed to insert OTP record', []);
            return OtpResult::fail(
                -1,
                'خطا در ذخیره OTP. لطفاً دوباره تلاش کنید.',
                $this->getName()
            );
        }

        // ── 3. ارسال (از طریق پراکسی یا مستقیم) ────────────────
        $kavType = $type === 'VOICE' ? 'call' : 'sms';
        $sendResult = $this->sendOtp($mobile, $otp, '', $kavType);

        // ── 4. لاگ نتیجه ───────────────────────────────────────
        $logCtx = [
            'length'        => $length,
            'valid_minutes' => $validMinutes,
            'type'          => $type,
            'otp_id'        => $otpId,
            'transport'     => $this->isProxyEnabled() ? 'proxy' : 'direct',
        ];

        if ($sendResult->success) {
            OtpSchema::log(
                $mobile,
                $this->getName(),
                'setRandomOTP',
                $type,
                OtpSchema::LOG_STATUS_SENT,
                0,
                'OTP sent successfully',
                $logCtx,
                ['otp_id' => $otpId]
            );
            return OtpResult::ok($otp, '', $this->getName(), 'OTP sent via Kavenegar');
        }

        OtpSchema::log(
            $mobile,
            $this->getName(),
            'setRandomOTP',
            $type,
            OtpSchema::LOG_STATUS_FAILED,
            $sendResult->errorCode,
            $sendResult->message,
            $logCtx,
            ['otp_id' => $otpId]
        );

        return $sendResult;
    }

    // ══════════════════════════════════════════════════════════
    //  OTP — verifyOtp (server-side via local DB)
    // ══════════════════════════════════════════════════════════

    /**
     * تأیید OTP از طریق دیتابیس محلی.
     * (بدون نیاز به پراکسی — همه‌چیز محلی است)
     */
    public function verifyOtp(string $mobile, string $otp): VerifyResult
    {
        $this->assertValidMobile($mobile);

        $otp = trim($otp);
        if ($otp === '') {
            $this->logVerifyAttempt($mobile, false, -1, 'Empty OTP');
            return VerifyResult::rejected(
                $mobile,
                -1,
                'کد تأیید الزامی است.',
                $this->getName()
            );
        }

        $row = OtpSchema::verifyOtpLocally($mobile, $otp);

        if ($row === null) {
            $this->logVerifyAttempt($mobile, false, 401, 'OTP mismatch or expired');
            return VerifyResult::rejected(
                $mobile,
                401,
                'کد تأیید اشتباه یا منقضی شده است.',
                $this->getName()
            );
        }

        $this->logVerifyAttempt($mobile, true, 0, 'OTP verified locally', [
            'otp_id' => (int) $row->id,
        ]);

        return VerifyResult::accepted(
            $mobile,
            $otp,
            $this->getName(),
            'OTP verified via local DB'
        );
    }

    // ══════════════════════════════════════════════════════════
    //  Account
    // ══════════════════════════════════════════════════════════

    /**
     * Get remaining credit.
     *
     * در حالت پراکسی، اگر پراکسی action 'account_info' را پشتیبانی
     * نکند، fallback به 0.0 + لاگ خطا. در ادمین می‌توانی credit را
     * مستقیم از پنل Kavenegar چک کنی.
     */
    public function getCredit(): float
    {
        try {
            if ($this->isProxyEnabled()) {
                $raw  = $this->callProxy('account_info', []);
                $body = $this->normalizeProxyResponse($raw);

                // Kavenegar credit در entries.remaincredit است
                $entries = $body['entries'];
                if (isset($entries['remaincredit'])) {
                    return (float) $entries['remaincredit'];
                }
                if (isset($entries[0]['remaincredit'])) {
                    return (float) $entries[0]['remaincredit'];
                }
                // اگر پراکسی این action را پشتیبانی نمی‌کند
                return 0.0;
            }

            // Direct mode
            $body = $this->httpPost(
                self::BASE_URL . $this->apiKey . '/account/info.json',
                []
            );
            return (float) (isset($body['entries']['remaincredit']) ? $body['entries']['remaincredit'] : 0.0);
        } catch (ProviderRequestException $e) {
            error_log('[Kavenegar] getCredit failed: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Kavenegar از USSD پشتیبانی نمی‌کند.
     */
    public function getUssdCode(): string
    {
        return '';
    }

    // ══════════════════════════════════════════════════════════
    //  Bulk send (Kavenegar-specific) — direct only
    //  (پراکسی ممکن است این action را پشتیبانی نکند)
    // ══════════════════════════════════════════════════════════

    public function sendArray(array $receptors, array $messages, array $senders): array
    {
        foreach ($receptors as $m) {
            $this->assertValidMobile($m);
        }

        $params = [
            'receptor' => json_encode($receptors),
            'message'  => json_encode($messages),
            'sender'   => json_encode($senders),
        ];

        try {
            if ($this->isProxyEnabled()) {
                $raw  = $this->callProxy('send_array', $params);
                $body = $this->normalizeProxyResponse($raw);
                $entries = $body['entries'];
            } else {
                $body = $this->httpPost(
                    self::BASE_URL . $this->apiKey . '/sms/sendarray.json',
                    $params
                );
                $entries = isset($body['return']['entries']) ? $body['return']['entries'] : [];
            }
        } catch (ProviderRequestException $e) {
            $results = [];
            foreach ($receptors as $m) {
                $results[] = SendResult::fail($m, $e->getProviderCode(), $e->getMessage());
            }
            return $results;
        }

        $results = [];
        foreach ($entries as $entry) {
            $status = (int)    (isset($entry['status'])    ? $entry['status']    : 0);
            $mid    = (string) (isset($entry['messageid']) ? $entry['messageid'] : '');
            $rec    = (string) (isset($entry['receptor'])  ? $entry['receptor']  : '');

            $results[] = $status === self::STATUS_OK
                ? SendResult::ok($mid, $rec, $status, 'Sent')
                : SendResult::fail($rec, $status, isset($entry['statustext']) ? $entry['statustext'] : 'Failed');
        }

        return $results;
    }

    public function cancel($messageId): array
    {
        $ids = is_array($messageId) ? implode(',', $messageId) : $messageId;

        try {
            if ($this->isProxyEnabled()) {
                $raw = $this->callProxy('cancel', ['messageid' => $ids]);
                return $this->normalizeProxyResponse($raw);
            }

            return $this->httpPost(
                self::BASE_URL . $this->apiKey . '/sms/cancel.json',
                ['messageid' => $ids]
            );
        } catch (ProviderRequestException $e) {
            error_log('[Kavenegar] cancel failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getStatus($messageId): array
    {
        $ids = is_array($messageId) ? implode(',', $messageId) : $messageId;

        try {
            if ($this->isProxyEnabled()) {
                $raw = $this->callProxy('status', ['messageid' => $ids]);
                $body = $this->normalizeProxyResponse($raw);
                return $body['entries'];
            }

            $body = $this->httpPost(
                self::BASE_URL . $this->apiKey . '/sms/status.json',
                ['messageid' => $ids]
            );
            return isset($body['return']['entries']) ? $body['return']['entries'] : [];
        } catch (ProviderRequestException $e) {
            error_log('[Kavenegar] getStatus failed: ' . $e->getMessage());
            return [];
        }
    }

    // ══════════════════════════════════════════════════════════
    //  Private helpers
    // ══════════════════════════════════════════════════════════

    /**
     * ساخت OTP تصادفی به طول مشخص با random_int (CSPRNG).
     */
    private function generateRandomOtp(int $length): string
    {
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_pad('', $length, '9');
        return (string) random_int($min, $max);
    }

    /**
     * Kavenegar فقط 'sms' و 'call' را قبول می‌کند.
     */
    private function normalizeKavenegarType(string $type): string
    {
        $type = strtolower(trim($type));
        if ($type === 'voice' || $type === 'call') {
            return 'call';
        }
        return 'sms';
    }

    private function logVerifyAttempt(
        string $mobile,
        bool $success,
        int $errorCode,
        string $message,
        array $extra = []
    ): void {
        OtpSchema::log(
            $mobile,
            $this->getName(),
            'verifyOTP',
            'plain',
            $success ? OtpSchema::LOG_STATUS_SENT : OtpSchema::LOG_STATUS_FAILED,
            $errorCode,
            $message,
            $extra,
            []
        );
    }

    private function logFailure(string $mobile, string $otpType, int $code, string $msg, array $req): void
    {
        OtpSchema::log(
            $mobile,
            $this->getName(),
            'setRandomOTP',
            $otpType,
            OtpSchema::LOG_STATUS_FAILED,
            $code,
            $msg,
            $req,
            []
        );
    }
}