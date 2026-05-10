<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use WP_REST_Response;

final class Cache implements MiddlewareInterface
{
    private $seconds = 120;

    public function __construct($seconds = 120)
    {
        $this->seconds = (int) $seconds;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        if (strtoupper($req->get_method()) !== 'GET' || $this->seconds <= 0) {
            return $next($req);
        }

        $cacheKey = $this->generateCacheKey($req);
        $cached = get_transient($cacheKey);

        if ($cached !== false) {
            return $this->createResponseFromCache($cached);
        }

        $response = $next($req);
        if ($response instanceof WP_REST_Response) {
            $this->cacheResponse($cacheKey, $response);
        }

        return $response;
    }

    private function generateCacheKey(WP_REST_Request $req): string
    {
        $route = $req->get_route();
        $params = $this->sanitizeParams($req->get_params());
        $query = $req->get_query_params();

        unset($query['_links'], $query['context']);

        $keyData = $route . '|' . wp_json_encode($params, JSON_UNESCAPED_SLASHES) . '|' . wp_json_encode($query, JSON_UNESCAPED_SLASHES);

        return 'rest_cache_' . substr(hash('sha256', $keyData), 0, 16);
    }

    private function sanitizeParams(array $params): array
    {
        return array_map(function ($value) {
            return $this->deepSanitize($value);
        }, $params);
    }

    private function deepSanitize($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'deepSanitize'], $value);
        }

        if (is_object($value)) {
            if ($value instanceof \Closure || is_callable($value)) {
                return '__REMOVED_CLOSURE__';
            }

            if (method_exists($value, 'jsonSerialize')) {
                return $this->deepSanitize($value->jsonSerialize());
            }

            $properties = get_object_vars($value);
            foreach ($properties as $key => $prop) {
                $properties[$key] = $this->deepSanitize($prop);
            }

            return $properties;
        }

        return $value;
    }

    private function createResponseFromCache(array $cached): WP_REST_Response
    {
        $response = new WP_REST_Response($cached['data'], $cached['status']);

        foreach ($cached['headers'] as $name => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $response->header($name, (string) $value);
        }

        $response->header('X-Cache', 'HIT');
        $response->header('X-Cache-Key', $cached['key']);
        $response->header('X-Cache-TTL', (string) ($this->seconds - (time() - $cached['timestamp'])));

        return $response;
    }

    private function cacheResponse($key, WP_REST_Response $response): void
    {
        try {
            $cacheData = [
                'key'       => $key,
                'data'      => $this->deepSanitize($response->get_data()),
                'status'    => $response->get_status(),
                'headers'   => $this->deepSanitize($response->get_headers()),
                'timestamp' => current_time('timestamp'),
            ];

            maybe_serialize($cacheData);

            set_transient($key, $cacheData, $this->seconds);
            $response->header('X-Cache', 'MISS');
        } catch (\Exception $e) {
            $response->header('X-Cache', 'ERROR');
        }
    }
}
