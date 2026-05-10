<?php

namespace Rehub\Rest;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;

/**
 * Router: holds groups and routes (Laravel style).
 */
final class Router
{
    private static array $groups = [];
    private static array $routes = []; // each: ['namespace','route','methods','action','middleware'=>[]]

    /** Define a route group with ['prefix'=>'rehub/v1','middleware'=>['throttle:60,1']] */
    public static function group(array $opts, callable $callback): void
    {
        self::$groups[] = $opts;
        $callback(new class {
            public function get(string $route, $action, array $mw = [])
            {
                Router::add(['GET'], $route, $action, $mw);
            }
            public function post(string $route, $action, array $mw = [])
            {
                Router::add(['POST'], $route, $action, $mw);
            }
            public function put(string $route, $action, array $mw = [])
            {
                Router::add(['PUT'], $route, $action, $mw);
            }
            public function patch(string $route, $action, array $mw = [])
            {
                Router::add(['PATCH'], $route, $action, $mw);
            }
            public function delete(string $route, $action, array $mw = [])
            {
                Router::add(['DELETE'], $route, $action, $mw);
            }
        });
        array_pop(self::$groups);
    }

    public static function add(array $methods, string $route, $action, array $mw): void
    {
        // Gather prefix + inherited middleware
        $prefix = '';
        $inherited = [];
        foreach (self::$groups as $g) {
            $prefix .= isset($g['prefix']) ? '/' . ltrim($g['prefix'], '/') : '';
            if (!empty($g['middleware'])) $inherited = array_merge($inherited, (array)$g['middleware']);
        }
        $full = trim($prefix, '/');               // e.g. 'rehub/v1'
        $ns   = $full !== '' ? $full : '';        // namespace for WP
        $route = '/' . ltrim($route, '/');        // e.g. '/experts'

        self::$routes[] = [
            'namespace'  => $ns,
            'route'      => $route,
            'methods'    => $methods,
            'action'     => $action,
            'middleware' => array_merge($inherited, $mw),
        ];
    }

    /** Iterate all routes (used by Kernel::boot to register with WP) */
    public static function all(): array
    {
        return self::$routes;
    }
}
