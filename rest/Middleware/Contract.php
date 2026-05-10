<?php

namespace Rehub\Rest\Middleware;

use WP_REST_Request;

interface MiddlewareInterface
{
    /** @return \WP_REST_Response|mixed */
    public function handle(WP_REST_Request $request, callable $next);
}
