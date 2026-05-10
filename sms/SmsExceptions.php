<?php

namespace Rehub\Core\Services\Sms;

/**
 * Base SMS exception — catch this to handle any SMS error.
 */
class SmsException extends \RuntimeException {}

/**
 * Thrown when the API key is invalid or missing.
 */
class InvalidApiKeyException extends SmsException {}

/**
 * Thrown when a provider is not registered or misconfigured.
 */
class ProviderNotFoundException extends SmsException {}

/**
 * Thrown when the HTTP request to the provider fails.
 */
class ProviderRequestException extends SmsException
{
    private $providerCode = 0;
    private $provider = '';

    public function __construct($message, $providerCode = 0, $provider = '', \Throwable $previous = null)
    {
        $this->providerCode = (int) $providerCode;
        $this->provider = (string) $provider;

        parent::__construct($message, $this->providerCode, $previous);
    }

    public function getProviderCode(): int
    {
        return $this->providerCode;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
}

/**
 * Thrown when account credit is insufficient.
 */
class InsufficientCreditException extends SmsException {}

/**
 * Thrown when an OTP operation fails.
 */
class OtpException extends SmsException
{
    private $providerCode = 0;

    public function __construct($message, $providerCode = 0, \Throwable $previous = null)
    {
        $this->providerCode = (int) $providerCode;

        parent::__construct($message, $this->providerCode, $previous);
    }

    public function getProviderCode(): int
    {
        return $this->providerCode;
    }
}
