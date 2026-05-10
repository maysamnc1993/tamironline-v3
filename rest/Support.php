<?php

namespace Rehub\Rest;

if (!defined('ABSPATH')) exit;

/**
 * Low-level support utilities (IDs, timers, sanitization).
 */
final class Support
{
    public static function reqId(): string
    {
        return wp_generate_uuid4();
    }

    public static function nowMs(): float
    {
        return microtime(true) * 1000.0;
    }

    public static function sanitizeSlug(?string $v): string
    {
        $v = (string) ($v ?? '');
        $v = remove_accents($v);
        $v = sanitize_title($v);
        return $v;
    }

    public static function clientIdentity(): string
    {
        if (is_user_logged_in()) return 'u:' . (int) get_current_user_id();
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        if (strpos($ip, ',') !== false) $ip = trim(explode(',', $ip)[0]);
        return 'ip:' . $ip;
    }
}

/**
 * Decode repeatedly to handle double-encoded inputs like %25D8...
 */
function urldecode_recursive(string $value, int $max = 3): string
{
    $prev = $value;
    for ($i = 0; $i < $max; $i++) {
        $decoded = rawurldecode($prev);
        if ($decoded === $prev) break;
        $prev = $decoded;
    }
    return $prev;
}

/**
 * Normalize slug for WordPress terms.
 * - recursive decode
 * - trim, collapse spaces to hyphens
 * - keep Unicode letters/numbers plus "-" and "_"
 * - lowercase (UTF-8 safe)
 */
function normalize_slug(?string $value): string
{
    if (!is_string($value) || $value === '') return '';
    $s = urldecode_recursive($value);
    $s = trim($s);
    $s = preg_replace('/\s+/u', '-', $s);
    $s = preg_replace('/[^\p{L}\p{N}\-_]+/u', '', $s);
    if ($s === null) $s = '';
    if ($s === '') return '';
    if (function_exists('mb_strtolower')) {
        $s = mb_strtolower($s, 'UTF-8');
    } else {
        $s = strtolower($s);
    }
    return $s;
}
