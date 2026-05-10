<?php

namespace Rehub\Rest\Middleware;

if (!defined('ABSPATH')) exit;

use WP_REST_Request;
use Rehub\Rest\Response;

// اطمینان از در دسترس بودن اینترفیس
require_once __DIR__ . '/Contract.php';

final class IpAllow implements MiddlewareInterface
{
    /** @var string[] */
    private $cidrsOrIps = [];

    /**
     * @param string ...$cidrsOrIps لیست IP یا CIDRها
     */
    public function __construct(...$cidrsOrIps)
    {
        $this->cidrsOrIps = $cidrsOrIps;
    }

    public function handle(WP_REST_Request $req, callable $next)
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';

        foreach ($this->cidrsOrIps as $cidr) {
            if ($this->match($ip, $cidr)) {
                return $next($req);
            }
        }

        return Response::error('IP not allowed.', 403);
    }

    private function match(string $ip, string $cidr): bool
    {
        // اگر بدون اسلش بود، تطبیق exact IP
        if (strpos($cidr, '/') === false) {
            return $ip === $cidr;
        }

        // تطبیق CIDR ساده برای IPv4
        list($subnet, $maskBits) = explode('/', $cidr, 2);
        $maskBits = (int) $maskBits;

        // اگر ورودی نامعتبر بود، اجازه نده
        if ($maskBits < 0 || $maskBits > 32) {
            return false;
        }

        $ipLong     = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = -1 << (32 - $maskBits);
        $mask = $mask & 0xFFFFFFFF;

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
