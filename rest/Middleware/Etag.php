<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;

/**
 * Adds/validates ETag for GET responses.
 * Requires the controller to set body in WP_REST_Response->get_data().
 */
final class Etag implements MiddlewareInterface
{
    public function handle(WP_REST_Request $req, callable $next)
    {
        if (strtoupper($req->get_method()) !== 'GET') {
            return $next($req);
        }

        $res = $next($req);

        if ($res instanceof \WP_REST_Response) {
            $data = $res->get_data();
            $etag = '"' . md5(maybe_serialize($data)) . '"';
            $res->header('ETag', $etag);

            $inm = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
            if ($inm && $inm === $etag) {
                $res->set_status(304);
                $res->set_data(null);
            }
        }

        return $res;
    }
}
