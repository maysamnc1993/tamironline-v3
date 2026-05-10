<?php

namespace Rehub\Rest;

if (!defined('ABSPATH')) exit;

use WP_REST_Response;

/**
 * Response helpers (similar to Laravel response() helpers).
 */
final class Response
{
    public static function ok(array $data = [], int $status = 200, array $headers = []): WP_REST_Response
    {
        $r = new WP_REST_Response($data, $status);
        foreach ($headers as $k => $v) $r->header($k, $v);
        return $r;
    }

    public static function created(array $data = [], array $headers = []): WP_REST_Response
    {
        return self::ok($data, 201, $headers);
    }

    public static function error(string $message, int $status = 400, array $extra = []): WP_REST_Response
    {
        $payload = array_merge(['success' => false, 'message' => $message], $extra);
        return self::ok($payload, $status);
    }


    /** Simple paginator wrapper */
    public static function paginate(array $items, int $total, int $page, int $perPage, array $extra = []): WP_REST_Response
    {
        return self::ok(array_merge([
            'data'  => $items,
            'meta'  => [
                'total' => $total,
                'page'  => $page,
                'per_page' => $perPage,
                'pages' => (int) ceil(max(1, $total) / max(1, $perPage)),
            ],
        ], $extra));
    }
}
