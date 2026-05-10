<?php

namespace Rehub\Rest\Support;

if (!defined('ABSPATH')) exit;

/**
 * Safe header fetcher (case-insensitive).
 * Returns null when header is missing.
 */
function header_get(string $name): ?string
{
    // Try native getallheaders()
    $headers = function_exists('getallheaders') ? getallheaders() : [];

    // Case-insensitive map
    if (!empty($headers)) {
        foreach ($headers as $k => $v) {
            if (strcasecmp($k, $name) === 0) {
                return is_string($v) ? $v : null;
            }
        }
    }

    // Fallback to $_SERVER (HTTP_X_REQUEST_ID format)
    $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    if (isset($_SERVER[$serverKey]) && is_string($_SERVER[$serverKey])) {
        return $_SERVER[$serverKey];
    }

    return null;
}
