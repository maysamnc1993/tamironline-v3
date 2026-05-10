<?php

namespace Rehub\Core\Services\Sms;

/**
 * Result of a plain SMS send operation.
 */
final class SendResult
{
    public $success;
    public $messageId;
    public $receptor;
    public $statusCode;
    public $statusMessage;

    public function __construct($success, $messageId, $receptor, $statusCode, $statusMessage)
    {
        $this->success = (bool) $success;
        $this->messageId = (string) $messageId;
        $this->receptor = (string) $receptor;
        $this->statusCode = (int) $statusCode;
        $this->statusMessage = (string) $statusMessage;
    }

    public static function ok($messageId, $receptor, $code = 200, $msg = 'OK')
    {
        return new self(true, $messageId, $receptor, $code, $msg);
    }

    public static function fail($receptor, $code, $msg)
    {
        return new self(false, '', $receptor, $code, $msg);
    }
}

/**
 * Result of an OTP creation/send operation.
 */
final class OtpResult
{
    public $success;
    public $otp;
    public $ussdCode;
    public $errorCode;
    public $message;
    public $provider;

    public function __construct($success, $otp, $ussdCode, $errorCode, $message, $provider)
    {
        $this->success = (bool) $success;
        $this->otp = (string) $otp;
        $this->ussdCode = (string) $ussdCode;
        $this->errorCode = (int) $errorCode;
        $this->message = (string) $message;
        $this->provider = (string) $provider;
    }

    public static function ok($otp, $ussd, $provider, $msg = '')
    {
        return new self(true, $otp, $ussd, 0, $msg, $provider);
    }

    public static function fail($code, $msg, $provider)
    {
        return new self(false, '', '', $code, $msg, $provider);
    }
}

/**
 * Result of an OTP verification check.
 */
final class VerifyResult
{
    public $accepted;
    public $mobile;
    public $otp;
    public $errorCode;
    public $message;
    public $provider;

    public function __construct($accepted, $mobile, $otp, $errorCode, $message, $provider)
    {
        $this->accepted = (bool) $accepted;
        $this->mobile = (string) $mobile;
        $this->otp = (string) $otp;
        $this->errorCode = (int) $errorCode;
        $this->message = (string) $message;
        $this->provider = (string) $provider;
    }

    public static function accepted($mobile, $otp, $provider, $msg = '')
    {
        return new self(true, $mobile, $otp, 0, $msg, $provider);
    }

    public static function rejected($mobile, $code, $msg, $provider)
    {
        return new self(false, $mobile, '', $code, $msg, $provider);
    }
}
