<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;

final class AuthNonce implements MiddlewareInterface
{
    private $action = 'wp_rest';

    /** @param string $action Nonce action (default 'wp_rest') */
    public function __construct($action = 'wp_rest')
    {
        $this->action = $action;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        $nonce = $req->get_header('X-WP-Nonce') ?: ($req->get_param('_wpnonce') ?: '');

        if (!$nonce || !wp_verify_nonce($nonce, $this->action)) {
            return Response::error('Invalid or missing nonce.', 401);
        }

        return $next($req);
    }
}
