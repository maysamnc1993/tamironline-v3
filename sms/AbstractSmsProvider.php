<?php

namespace Rehub\Core\Services\Sms;

use Rehub\Core\Services\Sms\SmsProviderInterface;
use Rehub\Core\Services\Sms\ProviderRequestException;

/**
 * Abstract base for all SMS providers.
 */
abstract class AbstractSmsProvider implements SmsProviderInterface
{
    protected $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    protected function httpPost(string $url, array $body, int $timeout = 15): array
    {
        $response = wp_remote_post($url, [
            'timeout'     => $timeout,
            'redirection' => 5,
            'httpversion' => '1.1',
            'headers'     => ['Accept' => 'application/json'],
            'body'        => $body,
        ]);

        return $this->parseResponse($response, $url);
    }

    protected function httpGet(string $url, array $query = [], int $timeout = 15): array
    {
        if ($query) {
            $url = add_query_arg($query, $url);
        }

        $response = wp_remote_get($url, [
            'timeout'     => $timeout,
            'redirection' => 5,
            'httpversion' => '1.1',
            'headers'     => ['Accept' => 'application/json'],
        ]);

        return $this->parseResponse($response, $url);
    }

    /**
     * @throws ProviderRequestException
     */
    private function parseResponse($response, string $url): array
    {
        if (is_wp_error($response)) {
            throw new ProviderRequestException(
                sprintf('[%s] HTTP error: %s', $this->getName(), $response->get_error_message()),
                0,
                $this->getName()
            );
        }

        $statusCode = (int) wp_remote_retrieve_response_code($response);
        $rawBody    = wp_remote_retrieve_body($response);

        $body = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ProviderRequestException(
                sprintf('[%s] Non-JSON response (HTTP %d): %s', $this->getName(), $statusCode, substr($rawBody, 0, 200)),
                $statusCode,
                $this->getName()
            );
        }

        return $body ? $body : [];
    }

    protected function assertValidMobile(string $mobile): void
    {
        if (!preg_match('/^09[0-9]{9}$/', $mobile)) {
            throw new \InvalidArgumentException("Invalid mobile number: {$mobile}");
        }
    }
}
