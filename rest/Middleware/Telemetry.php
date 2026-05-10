<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Support;

/**
 * Logs timing + outcome to error_log (can be extended to DB).
 */
final class Telemetry implements MiddlewareInterface
{
    public function handle(WP_REST_Request $req, callable $next)
    {
        $t0 = Support::nowMs();
        $res = $next($req);
        $t1 = Support::nowMs();

        $status = ($res instanceof \WP_REST_Response) ? $res->get_status() : 200;

        return $res;
    }
}
