<?php

namespace Rehub\Core\Services\Sms;

use Rehub\Core\Services\Sms\AbstractSmsProvider;
use Rehub\Core\Services\Sms\OtpResult;
use Rehub\Core\Services\Sms\SendResult;
use Rehub\Core\Services\Sms\VerifyResult;
use Rehub\Core\Services\Sms\OtpException;
use Rehub\Core\Services\Sms\ProviderRequestException;

/**
 * MrOTP Provider
 */
final class MrOtpProvider extends AbstractSmsProvider
{
    const BASE_URL = 'https://my.mrotp.ir/api/OTP/v1/';

    /** Delivery types supported by MrOTP */
    const TYPE_SMS   = 'SMS';
    const TYPE_USSD  = 'USSD';
    const TYPE_VOICE = 'VOICE';

    /**
     * Valid OTP digit lengths accepted by the provider.
     */
    const OTP_LENGTH_MIN = 3;
    const OTP_LENGTH_MAX = 8;

    /**
     * Valid validTime range in minutes.
     */
    const VALID_TIME_MIN = 1;
    const VALID_TIME_MAX = 1440;

    public function getName(): string
    {
        return 'mrotp';
    }

    public function supportsOtp(): bool
    {
        return true;
    }

    /**
     * @param string|array $receptor
     * @param string|null  $sender
     * @return SendResult[]
     */
    public function send($receptor, string $message, $sender = null): array
    {
        $patternId = (int) get_option('rehub_mrotp_pattern_id', 0);

        if (!$patternId) {
            throw new \LogicException(
                'MrOTP plain send() requires a patternId. Set rehub_mrotp_pattern_id in options, or call sendPatternSms() directly.'
            );
        }

        $receptors = is_array($receptor) ? $receptor : [$receptor];
        $results = [];

        foreach ($receptors as $mobile) {
            $this->assertValidMobile($mobile);

            try {
                $raw  = $this->sendPatternSmsRaw((string) $mobile, $patternId, $message);
                $code = (int) (isset($raw['code']) ? $raw['code'] : 0);

                $results[] = $code > 1000
                    ? SendResult::ok((string) $code, $mobile, $code, isset($raw['message']) ? $raw['message'] : '')
                    : SendResult::fail($mobile, $code, isset($raw['message']) ? $raw['message'] : 'Unknown error');
            } catch (ProviderRequestException $e) {
                $results[] = SendResult::fail($mobile, $e->getProviderCode(), $e->getMessage());
            }
        }

        return $results;
    }

    public function sendOtp(string $mobile, string $token, string $template, string $type = self::TYPE_USSD): OtpResult
    {
        $this->assertValidMobile($mobile);

        // Normalize type early so it stays consistent through the whole flow
        $type = strtoupper(trim($type));
        if ($type === '') {
            $type = self::TYPE_USSD;
        }

        $validTime = (int) get_option('rehub_mrotp_valid_time', 5);
        $validTime = max(self::VALID_TIME_MIN, min(self::VALID_TIME_MAX, $validTime));

        try {
            if ($token === '') {
                $length = (int) get_option('rehub_mrotp_otp_length', 6);
                $length = max(self::OTP_LENGTH_MIN, min(self::OTP_LENGTH_MAX, $length));

                return $this->setRandomOtp($mobile, $length, $validTime, $type);
            }

            return $this->setOtp($mobile, $token, $validTime, $type);
        } catch (OtpException $e) {
            return OtpResult::fail($e->getProviderCode(), $e->getMessage(), $this->getName());
        }
    }

    public function verifyOtp(string $mobile, string $otp): VerifyResult
    {
        $this->assertValidMobile($mobile);

        $body = $this->httpPost(self::BASE_URL . 'verifyOTP', [
            'apiKey' => $this->apiKey,
            'mobile' => $mobile,
            'OTP'    => $otp,
        ]);

        $accept = strtoupper(trim(isset($body['accept']) ? $body['accept'] : 'NO'));
        $code   = (int) (isset($body['code']) ? $body['code'] : -10);

        if ($accept === 'YES') {
            return VerifyResult::accepted(
                isset($body['mobile']) ? $body['mobile'] : $mobile,
                (string) (isset($body['OTP']) ? $body['OTP'] : $otp),
                $this->getName(),
                isset($body['message']) ? $body['message'] : ''
            );
        }

        return VerifyResult::rejected(
            $mobile,
            $code,
            isset($body['message']) ? $body['message'] : 'OTP not accepted',
            $this->getName()
        );
    }

    public function getCredit(): float
    {
        $body = $this->httpPost(self::BASE_URL . 'getCredit', [
            'apiKey' => $this->apiKey,
        ]);

        return (float) (isset($body['credit']) ? $body['credit'] : 0.0);
    }

    public function setOtp(
        string $mobile,
        string $otp,
        int $validTime = 5,
        string $type = self::TYPE_USSD
    ): OtpResult {
        $this->assertValidOtp($otp);
        $this->assertValidTime($validTime);

        // Normalize and validate type — must be uppercase for MrOTP API
        $type = strtoupper(trim($type));
        $this->assertValidType($type);

        $body = $this->httpPost(self::BASE_URL . 'setOTP', [
            'apiKey'    => $this->apiKey,
            'mobile'    => $mobile,
            'OTP'       => $otp,
            'validTime' => $validTime,
            'type'      => $type,
        ]);

        $code = (int) (isset($body['code']) ? $body['code'] : -10);

        if ($code <= 100) {
            throw new OtpException(isset($body['message']) ? $body['message'] : 'setOTP failed', $code);
        }

        return OtpResult::ok(
            $otp,
            isset($body['USSD']) ? $body['USSD'] : '',
            $this->getName(),
            isset($body['message']) ? $body['message'] : ''
        );
    }

    public function setRandomOtp(
        string $mobile,
        int $length = 6,
        int $validTime = 5,
        string $type = self::TYPE_USSD
    ): OtpResult {
        $this->assertValidLength($length);
        $this->assertValidTime($validTime);

        // Normalize and validate type — must be uppercase for MrOTP API
        $type = strtoupper(trim($type));
        $this->assertValidType($type);

        $body = $this->httpPost(self::BASE_URL . 'setRandomOTP', [
            'apiKey'    => $this->apiKey,
            'mobile'    => $mobile,
            'length'    => $length,
            'validTime' => $validTime,
            'type'      => $type,
        ]);

        $code = (int) (isset($body['code']) ? $body['code'] : -10);

        if ($code <= 100) {
            throw new OtpException(isset($body['message']) ? $body['message'] : 'setRandomOTP failed', $code);
        }

        return OtpResult::ok(
            (string) (isset($body['OTP']) ? $body['OTP'] : ''),
            isset($body['USSD']) ? $body['USSD'] : '',
            $this->getName(),
            isset($body['message']) ? $body['message'] : ''
        );
    }

    public function sendPatternSms(string $mobile, int $patternId, string $params): array
    {
        $this->assertValidMobile($mobile);
        $this->assertValidParams($params);

        $body = $this->sendPatternSmsRaw($mobile, $patternId, $params);
        $code = (int) (isset($body['code']) ? $body['code'] : -10);

        if ($code <= 1000) {
            throw new OtpException(isset($body['message']) ? $body['message'] : 'sendPatternSMS failed', $code);
        }

        return $body;
    }

    public function getUssdCode(): string
    {
        $body = $this->httpPost(self::BASE_URL . 'getUSSD', [
            'apiKey' => $this->apiKey,
        ]);

        return (string) (isset($body['USSD']) ? $body['USSD'] : '');
    }

    private function sendPatternSmsRaw(string $mobile, int $patternId, string $params): array
    {
        return $this->httpPost(self::BASE_URL . 'sendPatternSMS', [
            'apiKey'    => $this->apiKey,
            'mobile'    => $mobile,
            'patternId' => $patternId,
            'params'    => $params,
        ]);
    }

    private function assertValidOtp(string $otp): void
    {
        $len = strlen($otp);

        if (!ctype_digit($otp) || $len < self::OTP_LENGTH_MIN || $len > self::OTP_LENGTH_MAX) {
            throw new \InvalidArgumentException(
                sprintf('OTP must be %d–%d digits. Got: "%s"', self::OTP_LENGTH_MIN, self::OTP_LENGTH_MAX, $otp)
            );
        }
    }

    private function assertValidLength(int $length): void
    {
        if ($length < self::OTP_LENGTH_MIN || $length > self::OTP_LENGTH_MAX) {
            throw new \InvalidArgumentException(
                sprintf('OTP length must be %d–%d. Got: %d', self::OTP_LENGTH_MIN, self::OTP_LENGTH_MAX, $length)
            );
        }
    }

    private function assertValidTime(int $validTime): void
    {
        if ($validTime < self::VALID_TIME_MIN || $validTime > self::VALID_TIME_MAX) {
            throw new \InvalidArgumentException(
                sprintf('validTime must be %d–%d minutes. Got: %d', self::VALID_TIME_MIN, self::VALID_TIME_MAX, $validTime)
            );
        }
    }

    private function assertValidType(string $type): void
    {
        $allowed = [self::TYPE_SMS, self::TYPE_USSD, self::TYPE_VOICE];

        // $type is expected to be already normalized (uppercase) by callers.
        if (!in_array($type, $allowed, true)) {
            throw new \InvalidArgumentException(
                sprintf('type must be one of: %s. Got: "%s"', implode(', ', $allowed), $type)
            );
        }
    }

    private function assertValidParams(string $params): void
    {
        $parts = explode(';', $params);

        if (count($parts) > 10) {
            throw new \InvalidArgumentException('sendPatternSMS: max 10 params allowed.');
        }

        foreach ($parts as $i => $part) {
            if (mb_strlen($part) > 15) {
                throw new \InvalidArgumentException(
                    sprintf('sendPatternSMS: param[%d] exceeds 15 characters: "%s"', $i, $part)
                );
            }
        }
    }
}