<?php

namespace Rehub\Core\Services\Sms;

use Rehub\Core\Services\Sms\OtpResult;
use Rehub\Core\Services\Sms\SendResult;
use Rehub\Core\Services\Sms\VerifyResult;
use Rehub\Core\Services\Sms\SmsProviderInterface;
use Rehub\Core\Services\Sms\ProviderNotFoundException;
use Rehub\Core\Services\Sms\SmsException;
use Rehub\Core\Services\Sms\KavenegarProvider;
use Rehub\Core\Services\Sms\MrOtpProvider;

/**
 * SMS Service Manager
 *
 * 🆕 Changes in this version:
 *  - resolveOtpProvider() respects the active default provider
 *  - setRandomOtp() unified entry-point for AuthController
 *  - getDefaultProviderName() helper
 */
final class Sms
{
    private static $instance = null;

    /** @var array<string, SmsProviderInterface> */
    private $providers = [];

    private $defaultProvider = 'kavenegar';

    private function __construct()
    {
        $this->defaultProvider = get_option('rehub_sms_active_provider', 'kavenegar');
        $this->bootProviders();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    private function bootProviders(): void
    {
        $kavApiKey = get_option('rehub_sms_api_key', '');
        if ($kavApiKey) {
            $this->register('kavenegar', new KavenegarProvider($kavApiKey));
        }

        $mrOtpApiKey = get_option('rehub_mrotp_api_key', '');
        if ($mrOtpApiKey) {
            $this->register('mrotp', new MrOtpProvider($mrOtpApiKey));
        }

        do_action('rehub_sms_register_providers', $this);
    }

    public function register(string $name, SmsProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }

    public function provider($name = null)
    {
        $name = $name ? $name : $this->defaultProvider;

        if (!isset($this->providers[$name])) {
            throw new ProviderNotFoundException(
                "SMS provider '{$name}' is not registered. " .
                    "Registered providers: " . implode(', ', array_keys($this->providers))
            );
        }

        return $this->providers[$name];
    }

    public function availableProviders(): array
    {
        return array_keys($this->providers);
    }

    public function getDefaultProviderName(): string
    {
        return $this->defaultProvider;
    }

    public function setDefaultProvider(string $name): void
    {
        if (!isset($this->providers[$name])) {
            throw new ProviderNotFoundException("Cannot set default: provider '{$name}' not registered.");
        }
        $this->defaultProvider = $name;
    }

    /**
     * 🆕 Resolve which provider to use for OTP operations.
     *
     * منطق:
     *  ۱. اگر default provider ثبت شده و OTP پشتیبانی می‌کند → آن
     *  ۲. در غیر این صورت، اولین provider ثبت‌شده‌ای که OTP دارد
     *  ۳. اگر هیچ‌کدام نبود → exception
     */
    private function resolveOtpProvider(): SmsProviderInterface
    {
        if (isset($this->providers[$this->defaultProvider])) {
            $p = $this->providers[$this->defaultProvider];
            if ($p->supportsOtp()) {
                return $p;
            }
        }

        foreach ($this->providers as $p) {
            if ($p->supportsOtp()) {
                return $p;
            }
        }

        throw new ProviderNotFoundException(
            'No SMS provider with OTP support is configured.'
        );
    }

    // ══════════════════════════════════════════════════════════
    //  Plain SMS
    // ══════════════════════════════════════════════════════════

    public function send($receptor, string $message, $sender = null): array
    {
        return $this->provider()->send($receptor, $message, $sender);
    }

    // ══════════════════════════════════════════════════════════
    //  🆕 OTP — High-level setRandomOtp (used by AuthController)
    // ══════════════════════════════════════════════════════════

    /**
     * 🆕 ساخت + ذخیره + ارسال OTP در یک عملیات.
     *
     * این متد را AuthController باید استفاده کند.
     * خودش provider مناسب (بر اساس active provider) را انتخاب می‌کند.
     *
     * @return OtpResult
     */
    public function setRandomOtp(string $mobile, int $length = 5, int $validMinutes = 5, string $type = ''): OtpResult
    {
        $provider = $this->resolveOtpProvider();

        if ($type === '') {
            $type = (string) get_option('rehub_mrotp_default_type', 'SMS');
        }
        $type = strtoupper(trim($type));

        $length       = max(3, min(8, $length));
        $validMinutes = max(1, min(30, $validMinutes));

        return $provider->setRandomOtp($mobile, $length, $validMinutes, $type);
    }

    // ══════════════════════════════════════════════════════════
    //  OTP — Low-level
    // ══════════════════════════════════════════════════════════

    public function sendOtp(string $mobile, string $token = '', string $template = '', string $type = ''): OtpResult
    {
        $provider = $this->resolveOtpProvider();

        if ($type === '') {
            $type = (string) get_option('rehub_mrotp_default_type', 'SMS');
        }
        $type = strtoupper(trim($type));

        return $provider->sendOtp($mobile, $token, $template, $type);
    }

    public function verifyOtp(string $mobile, string $otp): VerifyResult
    {
        $provider = $this->resolveOtpProvider();
        return $provider->verifyOtp($mobile, $otp);
    }

    // ══════════════════════════════════════════════════════════
    //  Account info
    // ══════════════════════════════════════════════════════════

    public function getCredit($providerName = null): float
    {
        return $this->provider($providerName)->getCredit();
    }

    // ══════════════════════════════════════════════════════════
    //  Convenience helpers
    // ══════════════════════════════════════════════════════════

    public function sendUssdOtp(string $mobile, string $token = ''): OtpResult
    {
        return $this->sendOtp($mobile, $token, '', 'USSD');
    }

    public function sendVoiceOtp(string $mobile, string $token = ''): OtpResult
    {
        return $this->sendOtp($mobile, $token, '', 'VOICE');
    }

    public function sendSmsOtp(string $mobile, string $token = ''): OtpResult
    {
        return $this->sendOtp($mobile, $token, '', 'SMS');
    }

    public function getUssdCode(): string
    {
        if (!isset($this->providers['mrotp'])) {
            return '';
        }
        /** @var MrOtpProvider $mrOtp */
        $mrOtp = $this->providers['mrotp'];
        return $mrOtp->getUssdCode();
    }
}