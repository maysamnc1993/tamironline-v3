<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;

final class Maintenance implements MiddlewareInterface
{
    private $enabled = false;

    public function __construct($enabled = false)
    {
        $this->enabled = (bool) $enabled;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        if ($this->enabled || (bool) get_option('rehub_rest_maintenance', false)) {
            return Response::error('Maintenance mode.', 503);
        }

        return $next($req);
    }
}
