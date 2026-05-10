<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;
use Rehub\Rest\Helpers;

/**
 * HMAC signature middleware.
 * Required headers: X-Signature, X-Timestamp (unix seconds)
 * Server shared secret passed to constructor.
 */
final class SignatureHmac implements MiddlewareInterface
{
    private $secret;
    private $skew = 300;

    public function __construct($secret, $skew = 300)
    {
        $this->secret = $secret;
        $this->skew = (int) $skew;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        $sig = $req->get_header('X-Signature') ?: '';
        $ts  = (int) ($req->get_header('X-Timestamp') ?: 0);

        if (!$sig || !$ts) {
            return Response::error('Signature required.', 401);
        }

        if (abs(time() - $ts) > $this->skew) {
            return Response::error('Signature expired.', 401);
        }

        $payload = json_encode([
            'route'  => $req->get_route(),
            'params' => \Rehub\Rest\Helpers::allParams($req),
            'ts'     => $ts,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $calc = base64_encode(hash_hmac('sha256', $payload, $this->secret, true));

        if (!Helpers::hashEquals($sig, $calc)) {
            return Response::error('Invalid signature.', 401);
        }

        return $next($req);
    }
}
