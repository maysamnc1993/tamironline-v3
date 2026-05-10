<?php

use Rehub\Rest\Kernel;

/**
 * Bootstrap — Tamir Online REST Framework (Optimized)
 */

if (! defined('ABSPATH')) {
    exit;
}

// ════════════════════════════════════════════════════════════════════════
//  ① EARLY EXIT — فقط REST requests به این bootstrap نیاز دارند
// ════════════════════════════════════════════════════════════════════════

if (! function_exists('tamir_is_rest_request')) {
    function tamir_is_rest_request()
    {
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return true;
        }
        $prefix = function_exists('rest_get_url_prefix') ? rest_get_url_prefix() : 'wp-json';
        $uri    = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        return strpos($uri, '/' . $prefix . '/') !== false;
    }
}

if (! tamir_is_rest_request()) {
    return;
}

// ════════════════════════════════════════════════════════════════════════
//  $base
// ════════════════════════════════════════════════════════════════════════

$templateDir = get_template_directory();
$base        = $templateDir . '/rest';

// ════════════════════════════════════════════════════════════════════════
//  Core classes
// ════════════════════════════════════════════════════════════════════════

require_once $base . '/helpers.php';
require_once $base . '/Response.php';
require_once $base . '/RequestValidator.php';
require_once $base . '/Controller.php';
require_once $base . '/Router.php';
require_once $base . '/Kernel.php';
require_once $base . '/Support/Http.php';
require_once $base . '/Support.php';

// ════════════════════════════════════════════════════════════════════════
//  Services — utility classes استفاده‌شده توسط Controllers
// ════════════════════════════════════════════════════════════════════════

require_once $base . '/Services/IdempotencyHandler.php';

// ════════════════════════════════════════════════════════════════════════
//  🆕 Traits — منطق مشترک بین Controllers
// ════════════════════════════════════════════════════════════════════════

require_once $base . '/Traits/JwtAuthTrait.php';

// ════════════════════════════════════════════════════════════════════════
//  Middleware
// ════════════════════════════════════════════════════════════════════════

$mwBase = $base . '/Middleware/';

foreach (
    array(
        'AuthCookie',
        'AuthNonce',
        'AuthApp',
        'AuthJwt',
        'Throttle',
        'Etag',
        'Cache',
        'Telemetry',
        'Cors',
    ) as $_mw
) {
    require_once $mwBase . $_mw . '.php';
}

foreach (array('IpAllow', 'Capability', 'SignatureHmac', 'Maintenance') as $_mw) {
    $p = $mwBase . $_mw . '.php';
    if (file_exists($p)) {
        require_once $p;
    }
}

unset($mwBase, $_mw, $p);

// ════════════════════════════════════════════════════════════════════════
//  Controllers
// ════════════════════════════════════════════════════════════════════════

$ctrlBase = $base . '/Controllers/';
require_once $ctrlBase . 'AuthController.php';
require_once $ctrlBase . 'OrderController.php';
require_once $ctrlBase . 'ServiceController.php';
require_once $ctrlBase . 'LocationController.php';
require_once $ctrlBase . 'NotificationController.php';
require_once $ctrlBase . 'AddressController.php';
require_once $ctrlBase . 'ProfileController.php';
require_once $ctrlBase . 'HolidaysController.php';
require_once $ctrlBase . 'StatusController.php';
unset($ctrlBase);

// ════════════════════════════════════════════════════════════════════════
//  SMS / OTP Service
// ════════════════════════════════════════════════════════════════════════

$smsEntry = $templateDir . '/sms/sms.php';
if (file_exists($smsEntry)) {
    require_once $smsEntry;
}
unset($smsEntry);


// ════════════════════════════════════════════════════════════════════════
//  Lazy helpers با static cache
// ════════════════════════════════════════════════════════════════════════

if (! function_exists('tamir_jwt_secret')) {
    function tamir_jwt_secret()
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        if (defined('TAMIR_JWT_SECRET') && strlen(TAMIR_JWT_SECRET) >= 32) {
            $cached = TAMIR_JWT_SECRET;
            return $cached;
        }

        if (defined('AUTH_KEY') && strlen(AUTH_KEY) > 10) {
            error_log('[Tamir] WARNING: TAMIR_JWT_SECRET not set — falling back to AUTH_KEY. Add it to wp-config.php.');
            $cached = AUTH_KEY;
            return $cached;
        }

        error_log('[Tamir] CRITICAL: No JWT secret. Add TAMIR_JWT_SECRET to wp-config.php immediately!');
        $cached = 'TAMIR_JWT_SECRET_NOT_SET';
        return $cached;
    }
}

if (! function_exists('tamir_cors_origins')) {
    function tamir_cors_origins()
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        if (defined('TAMIR_ALLOWED_ORIGINS') && is_array(TAMIR_ALLOWED_ORIGINS)) {
            $cached = array_values(array_filter(array_map('trim', TAMIR_ALLOWED_ORIGINS)));
            return $cached;
        }

        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            error_log('[Tamir] WARNING: TAMIR_ALLOWED_ORIGINS not set — using * (dev mode only).');
            $cached = array('*');
            return $cached;
        }

        error_log('[Tamir] WARNING: TAMIR_ALLOWED_ORIGINS not set in production. CORS blocked.');
        $cached = array();
        return $cached;
    }
}

// ════════════════════════════════════════════════════════════════════════
//  Register middleware aliases — priority 1
// ════════════════════════════════════════════════════════════════════════

add_action('after_setup_theme', static function () {

    $jwtSecret   = tamir_jwt_secret();
    $corsOrigins = tamir_cors_origins();
    $hasWildcard = in_array('*', $corsOrigins, true);

    Kernel::registerMiddlewareMap(array(

        'auth:cookie' => static function () {
            return new \Rehub\Rest\Middleware\AuthCookie();
        },

        'auth:nonce'  => static function () {
            return new \Rehub\Rest\Middleware\AuthNonce();
        },

        'auth:app'    => static function () {
            return new \Rehub\Rest\Middleware\AuthApp();
        },

        'auth:jwt'    => static function () use ($jwtSecret) {
            return new \Rehub\Rest\Middleware\AuthJwt(
                $jwtSecret,
                array('sub', 'exp')
            );
        },

        'throttle'    => static function ($max = 60, $minutes = 1) {
            return new \Rehub\Rest\Middleware\Throttle((int) $max, (int) $minutes);
        },

        'ip_allow'    => static function ($ips = '') {
            return new \Rehub\Rest\Middleware\IpAllow($ips);
        },

        'cap'         => static function ($capability = 'read') {
            return new \Rehub\Rest\Middleware\Capability($capability);
        },

        'signature'   => static function ($secret = '', $header = 'X-Signature') {
            return new \Rehub\Rest\Middleware\SignatureHmac($secret, $header);
        },

        'maintenance' => static function () {
            return new \Rehub\Rest\Middleware\Maintenance();
        },

        'cors'        => static function () use ($corsOrigins, $hasWildcard) {
            return new \Rehub\Rest\Middleware\Cors(
                $corsOrigins,
                array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'),
                array('Content-Type', 'Authorization', 'X-WP-Nonce', 'X-Signature', 'X-Timestamp', 'Idempotency-Key'),
                (! $hasWildcard && ! empty($corsOrigins)),
                600
            );
        },

        'etag'      => static function () {
            return new \Rehub\Rest\Middleware\Etag();
        },

        'cache'     => static function ($seconds = 120) {
            return new \Rehub\Rest\Middleware\Cache((int) $seconds);
        },

        'telemetry' => static function () {
            return new \Rehub\Rest\Middleware\Telemetry();
        },

    ));
}, 1);

// ════════════════════════════════════════════════════════════════════════
//  Boot Kernel — priority 20
// ════════════════════════════════════════════════════════════════════════

add_action('after_setup_theme', static function () use ($base) {

    $registry = require $base . '/routes.php';

    if (! is_array($registry)) {
        $registry = array('routes' => array(), 'groups' => array(), 'middlewareMap' => array());
    }

    $globalMap = Kernel::getRegisteredMiddlewareMap();

    if (! isset($registry['middlewareMap']) || ! is_array($registry['middlewareMap'])) {
        $registry['middlewareMap'] = array();
    }

    $registry['middlewareMap'] = array_merge($globalMap, $registry['middlewareMap']);

    (new Kernel($registry))->boot();
}, 20);

/**
 * 🆕 اضافه‌کردن header های custom به Access-Control-Allow-Headers
 *
 * این فیلتر روی همه‌ی REST endpoints اعمال می‌شه — نه فقط tamir/v1.
 * اگه می‌خوای فقط روی namespace خودت اعمال شه، در داخل callback
 * چک کن $_SERVER['REQUEST_URI']
 */
add_filter('rest_allowed_cors_headers', static function ($allow_headers) {
    $custom = array(
        'Idempotency-Key',
        'X-Signature',
        'X-Timestamp',
        'x-device-id',
    );

    foreach ($custom as $h) {
        if (!in_array($h, $allow_headers, true)) {
            $allow_headers[] = $h;
        }
    }

    return $allow_headers;
}, 99);
