<?php

namespace Rehub\Rest;

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/Support/Http.php';
require_once __DIR__ . '/Response.php';

use WP_REST_Request;
use WP_REST_Response;
use function Rehub\Rest\Support\header_get;



final class Kernel
{
    /** @var array{routes: array, groups: array, middlewareMap: array} */
    private array $registry = [
        'routes' => [],
        'groups' => [],
        'middlewareMap' => [],
    ];

    /** @var bool */
    private bool $booted = false;

    /** @var array<string, callable> */
    private static array $globalMiddlewareMap = [];

    /**
     * Allow bootstrap to register global middleware factories by alias.
     */
    public static function registerMiddlewareMap(array $map): void
    {
        // Do not put Persian in comments; runtime strings may be Persian.
        foreach ($map as $alias => $factory) {
            if (is_callable($factory)) {
                self::$globalMiddlewareMap[$alias] = $factory;
            }
        }
    }

    /**
     * Expose the aliases so bootstrap can merge them into the registry.
     */
    public static function getRegisteredMiddlewareMap(): array
    {
        return self::$globalMiddlewareMap;
    }

    public function __construct(array $registry)
    {
        // Merge any globally-registered aliases if caller did not merge them
        if (!isset($registry['middlewareMap']) || !is_array($registry['middlewareMap'])) {
            $registry['middlewareMap'] = [];
        }
        $registry['middlewareMap'] = array_merge(self::$globalMiddlewareMap, $registry['middlewareMap']);

        $this->registry = $registry;
    }

    public function boot(): void
    {
        if ($this->booted) return;
        $this->booted = true;

        add_action('rest_api_init', function () {
            foreach ($this->registry['routes'] as $def) {
                register_rest_route(
                    $def['namespace'],
                    $def['route'],
                    [
                        'methods'             => $def['methods'],
                        'permission_callback' => '__return_true',
                        'callback'            => function (WP_REST_Request $req) use ($def) {
                            return $this->dispatch($req, $def);
                        },
                        'args'                => $def['args'] ?? [],
                    ]
                );
            }
        });
    }

    private function dispatch(WP_REST_Request $req, array $def)
    {

        $requestId = header_get('X-Request-ID') ?: wp_generate_uuid4();
        $req->set_param('_request_id', $requestId);

        $start = microtime(true);
        $stack = $this->resolveMiddlewareStack($def);

        $runner = array_reduce(
            array_reverse($stack),
            function ($next, $mw) {
                return function (WP_REST_Request $req) use ($mw, $next) {
                    if (is_callable($mw)) {
                        return $mw($req, $next);
                    }
                    if (is_array($mw) && count($mw) === 2 && is_callable($mw)) {
                        return $mw($req, $next);
                    }
                    if (is_object($mw) && method_exists($mw, 'handle')) {
                        return $mw->handle($req, $next);
                    }
                    return Response::error('خطای داخلی.', 500);
                };
            },
            function (WP_REST_Request $req) use ($def) {
                $controller = $def['controller'];
                $action     = $def['action'] ?? 'handle';
                $instance   = new $controller();
                return $instance->$action($req);
            }
        );

        try {
            $resp = $runner($req);
        } catch (Exception $e) {
            // 
            $resp = Response::error('خطای داخلی.', 500);
        }

        if (!$resp instanceof WP_REST_Response) {
            $resp = new WP_REST_Response($resp, 200);
        }

        $resp->header('X-Request-ID', $requestId);
        $elapsed = (int) ((microtime(true) - $start) * 1000);
        $resp->header('X-Elapsed', (string)$elapsed);

        return $resp;
    }

    private function resolveMiddlewareStack(array $def): array
    {
        $stack = [];

        if (!empty($def['group']) && isset($this->registry['groups'][$def['group']])) {
            foreach ($this->registry['groups'][$def['group']]['middleware'] as $m) {
                $stack[] = $this->makeMiddleware($m);
            }
        }

        foreach ($def['middleware'] ?? [] as $m) {
            $stack[] = $this->makeMiddleware($m);
        }

        return $stack;
    }

    private function makeMiddleware(string $signature)
    {
        [$name, $args] = array_pad(explode(':', $signature, 2), 2, '');
        $argv = strlen($args) ? array_map('trim', explode(',', $args)) : [];

        if (!isset($this->registry['middlewareMap'][$name])) {
            return function (WP_REST_Request $req, callable $next) {
                return $next($req);
            };
        }

        $factory = $this->registry['middlewareMap'][$name];

        // Support callable factories (preferred)
        if (is_callable($factory)) {
            return $factory(...$argv);
        }

        // Support class-string mapping for BC
        if (is_string($factory) && class_exists($factory)) {
            $ref = new \ReflectionClass($factory);
            return $ref->getConstructor()
                ? $ref->newInstanceArgs($argv)
                : $ref->newInstance();
        }

        return function (WP_REST_Request $req, callable $next) {
            return $next($req);
        };
    }
}
