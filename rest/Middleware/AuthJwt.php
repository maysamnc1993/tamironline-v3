<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;
use Rehub\Rest\Helpers;

/**
 * Simple HMAC-JWT (HS256). Use HTTPS in production.
 * Header: Authorization: Bearer <token>
 */
final class AuthJwt implements MiddlewareInterface
{
    private $secret = '';
    private $requiredClaims = [];

    public function __construct($secret = '', array $requiredClaims = [])
    {
        $this->secret = $secret;
        $this->requiredClaims = $requiredClaims;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        $hdr = $req->get_header('authorization') ?: '';
        if (!preg_match('~^Bearer\s+(.+)$~i', $hdr, $m)) {
            return Response::error('JWT required.', 401);
        }

        $token = $m[1];
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return Response::error('Invalid JWT.', 401);
        }

        list($h, $p, $s) = $parts;

        $sig = rtrim(strtr(base64_decode(strtr($s, '-_', '+/')), "\0", ''), "\0");
        $payloadRaw = $h . '.' . $p;
        $calc = hash_hmac('sha256', $payloadRaw, $this->secret, true);

        if (!Helpers::hashEquals($sig, $calc)) {
            return Response::error('Invalid signature.', 401);
        }

        $payload = json_decode(base64_decode(strtr($p, '-_', '+/')), true) ?: [];

        if (isset($payload['exp']) && time() >= (int) $payload['exp']) {
            return Response::error('Token expired.', 401);
        }

        foreach ($this->requiredClaims as $c) {
            if (!array_key_exists($c, $payload)) {
                return Response::error("Missing claim: $c", 401);
            }
        }

        // Attach to request for downstream usage
        $req->set_param('_jwt', $payload);

        return $next($req);
    }
}
