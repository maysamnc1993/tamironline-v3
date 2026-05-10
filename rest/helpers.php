<?php

namespace Rehub\Rest;

if (!defined('ABSPATH')) exit;

/**
 * Very small helpers used throughout the framework.
 */
final class Helpers
{
    /** Parse "name:arg1,arg2" → ['name', ['arg1','arg2']] */
    public static function parseMiddlewareToken(string $token): array
    {
        $token = trim($token);
        if ($token === '') return ['', []];
        $name  = $token;
        $args  = [];
        if (str_contains($token, ':')) {
            [$name, $rest] = explode(':', $token, 2);
            $args = array_values(array_filter(array_map('trim', explode(',', $rest))));
        }
        return [strtolower($name), $args];
    }

    /** Constant-time string compare */
    public static function hashEquals(string $a, string $b): bool
    {
        if (function_exists('hash_equals')) return hash_equals($a, $b);
        if (strlen($a) !== strlen($b)) return false;
        $res = 0;
        $len = strlen($a);
        for ($i = 0; $i < $len; $i++) $res |= ord($a[$i]) ^ ord($b[$i]);
        return $res === 0;
    }

    /** Merge params (query + body + JSON) safely */
    public static function allParams(\WP_REST_Request $req): array
    {
        $params = array_merge($req->get_params() ?: [], $req->get_json_params() ?: []);
        return is_array($params) ? $params : [];
    }
}
