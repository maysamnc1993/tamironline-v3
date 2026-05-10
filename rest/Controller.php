<?php

namespace Rehub\Rest;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;

/**
 * Base controller (Laravel-ish).
 * Override authorize(), rules(), messages(), then implement action methods.
 */
abstract class Controller
{
    public function authorize(WP_REST_Request $r)
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    protected function ok(array $data = [], int $status = 200, array $headers = [])
    {
        return Response::ok($data, $status, $headers);
    }

    protected function created(array $data = [], array $headers = [])
    {
        return Response::created($data, $headers);
    }

    protected function error(string $message, int $status = 400, array $extra = [])
    {
        return Response::error($message, $status, $extra);
    }
}
