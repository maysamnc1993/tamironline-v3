<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;

final class Capability implements MiddlewareInterface
{
    private $capability;

    public function __construct($capability)
    {
        $this->capability = $capability;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        if (!current_user_can($this->capability)) {
            return Response::error('Forbidden.', 403);
        }

        return $next($req);
    }
}
