<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;

/**
 * Application Passwords auth (Basic Auth over HTTPS).
 * WordPress core handles checking. Here we only enforce presence.
 */
final class AuthApp implements MiddlewareInterface
{
    public function handle(WP_REST_Request $req, callable $next)
    {
        $auth = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;

        if (!$auth) {
            header('WWW-Authenticate: Basic realm="App Passwords"');
            return Response::error('App authentication required.', 401);
        }

        return $next($req);
    }
}
