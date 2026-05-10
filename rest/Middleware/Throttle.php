<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;
use Rehub\Rest\Support;

final class Throttle implements MiddlewareInterface
{
    private $max = 60;
    private $minutes = 1;

    public function __construct($max = 60, $minutes = 1)
    {
        $this->max = (int) $max;
        $this->minutes = (int) $minutes;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        $key = 'rest_throttle:' . md5(($req->get_route() ? $req->get_route() : '') . '|' . Support::clientIdentity());
        $count = (int) get_transient($key);

        if ($count >= $this->max) {
            $resp = Response::error('تعداد درخواست ها بیش از حد مجاز است!', 429);
            $resp->header('Retry-After', (string) ($this->minutes * 60));
            return $resp;
        }

        set_transient($key, $count + 1, $this->minutes * 60);

        $res = $next($req);

        if ($res instanceof \WP_REST_Response) {
            $res->header('X-RateLimit-Limit', (string) $this->max);
            $res->header('X-RateLimit-Remaining', (string) max(0, $this->max - $count - 1));
        }

        return $res;
    }
}
