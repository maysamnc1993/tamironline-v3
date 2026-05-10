<?php

namespace Rehub\Core\Services\Sms;

/**
 * Contract for all SMS providers.
 *
 * Every provider must implement this interface.
 * Method signatures use value objects where possible
 * to keep callers decoupled from provider details.
 */
interface SmsProviderInterface
{
    /**
     * Send a plain-text SMS to one or more receptors.
     *
     * @param  string|array $receptor  Mobile number(s) — e.g. "09121112233"
     * @param  string       $message   UTF-8 message body
     * @param  string|null  $sender    Originating line number (null = provider default)
     * @return SendResult[]
     */
    public function send($receptor, string $message, $sender = null): array;

    /**
     * Send an OTP via the provider's OTP infrastructure.
     *
     * Depending on the provider this may be USSD, SMS, or Voice.
     *
     * @param  string $mobile     Recipient mobile — e.g. "09121112233"
     * @param  string $token      The OTP value (or template token)
     * @param  string $template   Template / pattern name or ID
     * @param  string $type       Delivery type: "sms" | "ussd" | "voice"
     * @return OtpResult
     */
    public function sendOtp(string $mobile, string $token, string $template, string $type = 'sms'): OtpResult;

    /**
     * 🆕 Generate a random OTP, persist it locally, and dispatch via provider.
     *
     * Unified entry-point that auth controllers should use. Each provider:
     *   - Generates a cryptographically random OTP of the requested length
     *   - Stores the OTP in {prefix}hdm_otp_codes via OtpSchema::insertOtp()
     *   - Sends it through the provider (Kavenegar template / MrOTP setRandomOtp)
     *   - Writes an audit row via OtpSchema::log()
     *
     * @param  string $mobile        Recipient mobile (09xxxxxxxxx)
     * @param  int    $length        Number of digits (3–8). Default 5.
     * @param  int    $validMinutes  Minutes until OTP expires. Default 5.
     * @param  string $type          'SMS' | 'USSD' | 'VOICE' (provider-dependent)
     * @return OtpResult
     */
    public function setRandomOtp(string $mobile, int $length = 5, int $validMinutes = 5, string $type = 'SMS'): OtpResult;

    /**
     * Verify an OTP code for a given mobile.
     *
     * @param  string $mobile   Mobile number
     * @param  string $otp      Code entered by the user
     * @return VerifyResult
     */
    public function verifyOtp(string $mobile, string $otp): VerifyResult;

    /**
     * Return remaining account credit in Rials (or provider currency).
     *
     * @return float
     */
    public function getCredit(): float;

    /**
     * Whether this provider natively supports OTP (USSD/SMS/Voice).
     */
    public function supportsOtp(): bool;

    /**
     * Return a human-readable provider name.
     */
    public function getName(): string;
}