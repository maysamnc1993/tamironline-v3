<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;

require_once __DIR__ . '/Contract.php';

final class AuthCookie implements MiddlewareInterface
{
    private $cap = null;

    /** @param string|null $cap Optional required WP caps (e.g., 'manage_options') */
    public function __construct($cap = null)
    {
        $this->cap = $cap;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        if (!is_user_logged_in()) {
            return Response::error('Authentication required.', 401);
        }

        if ($this->cap && !current_user_can($this->cap)) {
            return Response::error('Forbidden.', 403);
        }

        return $next($req);
    }
}
