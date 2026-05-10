<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;

final class Cors implements MiddlewareInterface
{
    private $allowOrigins = ['*'];
    private $allowMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    private $allowHeaders = ['Content-Type', 'X-WP-Nonce', 'Authorization', 'X-Signature', 'X-Timestamp'];
    private $allowCredentials = false;
    private $maxAge = 600;

    /** @param array $allowOrigins ['https://example.com'] or ['*'] */
    public function __construct(
        array $allowOrigins = ['*'],
        array $allowMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        array $allowHeaders = ['Content-Type', 'X-WP-Nonce', 'Authorization', 'X-Signature', 'X-Timestamp'],
        $allowCredentials = false,
        $maxAge = 600
    ) {
        $this->allowOrigins = $allowOrigins;
        $this->allowMethods = $allowMethods;
        $this->allowHeaders = $allowHeaders;
        $this->allowCredentials = (bool) $allowCredentials;
        $this->maxAge = (int) $maxAge;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        $allowedOrigin = in_array('*', $this->allowOrigins, true)
            ? '*'
            : (in_array($origin, $this->allowOrigins, true) ? $origin : '');

        if ($req->get_method() === 'OPTIONS') {
            $resp = Response::ok([], 204);

            if ($allowedOrigin) {
                $resp->header('Access-Control-Allow-Origin', $allowedOrigin);
            }

            $resp->header('Vary', 'Origin');
            $resp->header('Access-Control-Allow-Methods', implode(',', $this->allowMethods));
            $resp->header('Access-Control-Allow-Headers', implode(',', $this->allowHeaders));
            $resp->header('Access-Control-Max-Age', (string) $this->maxAge);

            if ($this->allowCredentials && $allowedOrigin !== '*') {
                $resp->header('Access-Control-Allow-Credentials', 'true');
            }

            return $resp;
        }

        $res = $next($req);

        if ($res instanceof \WP_REST_Response && $allowedOrigin) {
            $res->header('Access-Control-Allow-Origin', $allowedOrigin);
            $res->header('Vary', 'Origin');

            if ($this->allowCredentials && $allowedOrigin !== '*') {
                $res->header('Access-Control-Allow-Credentials', 'true');
            }
        }

        return $res;
    }
}
