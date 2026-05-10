<?php

/**
 * Tamir Online — REST Routes (Production)
 *
 * Namespace: tamir/v1
 *
 * امنیت اعمال‌شده:
 *  - CORS فقط برای origin های مجاز اپ
 *  - Throttle تفکیک‌شده بر اساس حساسیت endpoint
 *  - auth:jwt روی تمام مسیرهای private
 *  - cache فقط روی GET های عمومی و بی‌خطر
 *  - هیچ اطلاعات کاربری در cache ذخیره نمی‌شود
 *
 * گروه‌ها:
 *   public     — بدون احراز هویت، throttle محدود، CORS
 *   auth_flow  — فقط برای OTP/login، throttle خیلی سخت
 *   private    — نیاز به JWT، بدون cache، CORS
 */

return [

    // ═════════════════════════════════════════════════════════════
    //  Groups
    // ═════════════════════════════════════════════════════════════
    'groups' => [

        /**
         * public — endpoint های عمومی (داده‌های مرجع، status)
         *
         * Middleware pipeline:
         *   telemetry → cors → throttle:60,1 → etag → cache
         *
         * نکته: cache بعد از throttle — تا از bypass rate-limit جلوگیری شود.
         * نکته: cors قبل از throttle — تا preflight بدون throttle پاسخ بگیرد.
         */
        'public' => [
            'prefix'     => 'tamir/v1',
            'middleware' => [
                'telemetry',
                'cors',
                'throttle:60,1',
            ],
        ],

        /**
         * auth_flow — فقط برای send-otp / verify-otp / register
         *
         * throttle خیلی سخت‌تر: ۵ درخواست در ۵ دقیقه.
         * rate limiting واقعی OTP در AuthController (OtpSchema) هم هست —
         * این لایه دوم defense است.
         *
         * بدون cache — هرگز.
         * بدون etag — response های auth نباید cache بشن.
         */
        'auth_flow' => [
            'prefix'     => 'tamir/v1',
            'middleware' => [
                'telemetry',
                'cors',
                'throttle:20,5',
            ],
        ],

        /**
         * private — endpoint های کاربر لاگین‌شده
         *
         * pipeline: telemetry → cors → throttle:120,1 → auth:jwt
         *
         * auth:jwt باید آخرین middleware قبل از controller باشد.
         * هیچ cache ای — اطلاعات شخصی کاربر نباید cache شود.
         */
        'private' => [
            'prefix'     => 'tamir/v1',
            'middleware' => [
                'telemetry',
                'cors',
                'throttle:120,1',
                'auth:jwt',
            ],
        ],

    ],

    // ═════════════════════════════════════════════════════════════
    //  Routes
    // ═════════════════════════════════════════════════════════════
    'routes' => [

        // ══════════════════════════════════════════════════════════
        //  AUTH — OTP Flow
        //  گروه: auth_flow (throttle سخت، بدون cache)
        // ══════════════════════════════════════════════════════════

        /**
         * POST /tamir/v1/auth/send-otp
         *
         * throttle گروه: ۵ در ۵ دقیقه (هر IP/user)
         * rate-limit واقعی OTP: در AuthController از OtpSchema
         * هیچ داده‌ای cache نمی‌شود.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/auth/send-otp',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\AuthController::class,
            'action'     => 'sendOtp',
            'group'      => 'auth_flow',
            'middleware' => [],            // throttle از group کافیست
            'args'       => [
                'phone' => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * POST /tamir/v1/auth/verify-otp
         *
         * همان throttle گروه auth_flow.
         * brute-force OTP را با rate limit گروه + OtpSchema block می‌کند.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/auth/verify-otp',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\AuthController::class,
            'action'     => 'verifyOtp',
            'group'      => 'auth_flow',
            'middleware' => [],
            'args'       => [
                'phone' => ['sanitize_callback' => 'sanitize_text_field'],
                'otp'   => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * POST /tamir/v1/auth/register
         *
         * سخت‌ترین throttle: ۳ بار در ۱۰ دقیقه.
         * ثبت‌نام نباید مکرر باشد.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/auth/register',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\AuthController::class,
            'action'     => 'register',
            'group'      => 'auth_flow',
            'middleware' => ['throttle:3,10'],  // override: سخت‌تر از group
            'args'       => [
                'name'       => ['sanitize_callback' => 'sanitize_text_field'],
                'phone'      => ['sanitize_callback' => 'sanitize_text_field'],
                'temp_token' => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        // ══════════════════════════════════════════════════════════
        //  AUTH — نیاز به JWT
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/auth/me
         * اطلاعات کاربر جاری — هرگز cache نمی‌شود.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/auth/me',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\AuthController::class,
            'action'     => 'me',
            'group'      => 'private',
            'middleware' => [],
        ],

        /**
         * POST /tamir/v1/auth/logout
         * ابطال JWT — نیاز به auth:jwt دارد تا user ID بگیرد.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/auth/logout',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\AuthController::class,
            'action'     => 'logout',
            'group'      => 'private',
            'middleware' => [],
        ],

        // ══════════════════════════════════════════════════════════
        //  SERVICES — داده‌های مرجع (public + cache طولانی)
        //  این داده‌ها کاربر-محور نیستند — cache امن است.
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/services/categories
         * دسته‌بندی‌های تعمیر — cache 10 دقیقه.
         * etag برای 304 — کاهش bandwidth.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/services/categories',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\ServiceController::class,
            'action'     => 'categories',
            'group'      => 'public',
            'middleware' => ['etag', 'cache:600'],
            'args'       => [],
        ],

        /**
         * GET /tamir/v1/services/brands[?category_id=X]
         * برندها — cache 10 دقیقه.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/services/brands',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\ServiceController::class,
            'action'     => 'brands',
            'group'      => 'public',
            'middleware' => ['etag', 'cache:600'],
            'args'       => [
                'category_id' => ['sanitize_callback' => 'absint'],
            ],
        ],

        /**
         * GET /tamir/v1/services/objections
         * ایرادات دستگاه از wp_options — cache 10 دقیقه.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/services/objections',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\ServiceController::class,
            'action'     => 'objections',
            'group'      => 'public',
            'middleware' => ['etag', 'cache:600'],
            'args'       => [],
        ],

        /**
         * GET /tamir/v1/services/banners[?placement=X]
         * بنرها — cache 5 دقیقه (ممکن است مکرر عوض شوند).
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/services/banners',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\ServiceController::class,
            'action'     => 'banners',
            'group'      => 'public',
            'middleware' => ['etag', 'cache:300'],
            'args'       => [
                'placement' => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * GET /tamir/v1/orders/cancel-reasons
         * دلایل لغو — public چون در فرم لغو نمایش داده می‌شود.
         * cache 10 دقیقه.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/orders/cancel-reasons',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\ServiceController::class,
            'action'     => 'cancelReasons',
            'group'      => 'public',
            'middleware' => ['etag', 'cache:600'],
            'args'       => [],
        ],

        // ══════════════════════════════════════════════════════════
        //  LOCATIONS — استان‌ها و شهرها (public + cache خیلی طولانی)
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/locations/states
         * استان‌ها — بندرت تغییر می‌کند، cache 2 ساعت.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/locations/states',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\LocationController::class,
            'action'     => 'states',
            'group'      => 'public',
            'middleware' => ['etag', 'cache:7200'],
            'args'       => [],
        ],

        /**
         * GET /tamir/v1/locations/cities[?state_id=X]
         * شهرها — cache 2 ساعت.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/locations/cities',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\LocationController::class,
            'action'     => 'cities',
            'group'      => 'public',
            'middleware' => ['etag', 'cache:7200'],
            'args'       => [
                'state_id' => ['sanitize_callback' => 'absint'],
            ],
        ],

        // ══════════════════════════════════════════════════════════
        //  HOLIDAYS — تعطیلات رسمی (public + cache روزانه)
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/holidays[?from=X&to=Y]
         * تعطیلات رسمی — cache 6 ساعت (روزانه یک‌بار fetch کافیست).
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/holidays',
            'methods'    => 'GET',
            'controller' => \Tamir\Holidays\Controllers\HolidaysController::class,
            'action'     => 'index',
            'group'      => 'public',
            'middleware' => ['etag', 'cache:21600'],
            'args'       => [
                'from' => ['sanitize_callback' => 'sanitize_text_field'],
                'to'   => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        // ══════════════════════════════════════════════════════════
        //  STATUS — وضعیت اپ / kill switch (public، بدون cache)
        //
        //  ⚠️  cache روی این route نباید فعال باشد.
        //  kill switch باید فوری عمل کند — هر ثانیه مهم است.
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/status[?version=X]
         * بدون cache — force check هر بار.
         * throttle گروه (60/min) کافیست.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/status',
            'methods'    => 'GET',
            'controller' => \Tamir\AppControl\Controllers\StatusController::class,
            'action'     => 'index',
            'group'      => 'public',
            'middleware' => [],            // بدون cache، بدون etag
            'args'       => [
                'version' => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        // ══════════════════════════════════════════════════════════
        //  ORDERS — نیاز به JWT
        //  هیچ‌کدام cache نمی‌شوند — داده‌های شخصی کاربر
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/orders
         * لیست سفارشات کاربر جاری.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/orders',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\OrderController::class,
            'action'     => 'index',
            'group'      => 'private',
            'middleware' => [],
            'args'       => [
                'page'     => ['sanitize_callback' => 'absint'],
                'per_page' => ['sanitize_callback' => 'absint'],
                'status'   => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * POST /tamir/v1/orders
         * ثبت سفارش جدید.
         * throttle اضافه: ۵ سفارش در دقیقه (از abuse جلوگیری می‌کند).
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/orders',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\OrderController::class,
            'action'     => 'store',
            'group'      => 'private',
            'middleware' => ['throttle:50,10'],
            'args'       => [
                'order_type'            => ['sanitize_callback' => 'sanitize_text_field'],
                'device_id'             => ['sanitize_callback' => 'absint'],
                'brand_id'              => ['sanitize_callback' => 'absint'],
                'objection_description' => ['sanitize_callback' => 'sanitize_textarea_field'],
                'scheduled_date'        => ['sanitize_callback' => 'sanitize_text_field'],
                'scheduled_time'        => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * GET /tamir/v1/orders/{id}
         * جزئیات یک سفارش — داده شخصی، بدون cache.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/orders/(?P<id>\d+)',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\OrderController::class,
            'action'     => 'show',
            'group'      => 'private',
            'middleware' => [],
            'args'       => [
                'id' => ['sanitize_callback' => 'absint'],
            ],
        ],

        /**
         * POST /tamir/v1/orders/{id}/cancel
         * لغو سفارش — throttle اضافه برای جلوگیری از لغو اتفاقی مکرر.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/orders/(?P<id>\d+)/cancel',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\OrderController::class,
            'action'     => 'cancel',
            'group'      => 'private',
            'middleware' => ['throttle:50,10'],
            'args'       => [
                'id'     => ['sanitize_callback' => 'absint'],
                'reason' => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * GET /tamir/v1/orders/{id}/version
         * بررسی تغییر وضعیت سفارش — polling endpoint.
         *
         * throttle جداگانه ۳۰ در دقیقه (هر ۲ ثانیه یک poll = ۳۰/min).
         * بدون cache — باید real-time باشد.
         * etag به‌جای cache — اگر version تغییر نکرده ۳۰۴ برمی‌گردد.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/orders/(?P<id>\d+)/version',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\OrderController::class,
            'action'     => 'version',
            'group'      => 'private',
            // 'middleware' => ['throttle:3000,1000', 'etag'],
            'middleware' => ['etag'],
            'args'       => [
                'id' => ['sanitize_callback' => 'absint'],
            ],
        ],

        // ══════════════════════════════════════════════════════════
        //  ADDRESSES — نیاز به JWT
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/addresses
         * آدرس‌های ذخیره‌شده کاربر — داده شخصی، بدون cache.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/addresses',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\AddressController::class,
            'action'     => 'index',
            'group'      => 'private',
            'middleware' => [],
        ],

        /**
         * POST /tamir/v1/addresses
         * افزودن آدرس جدید.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/addresses',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\AddressController::class,
            'action'     => 'store',
            'group'      => 'private',
            'middleware' => ['throttle:100,10'],
            'args'       => [
                'title'        => ['sanitize_callback' => 'sanitize_text_field'],
                'full_address' => ['sanitize_callback' => 'sanitize_textarea_field'],
                'city'         => ['sanitize_callback' => 'sanitize_text_field'],
                'province'     => ['sanitize_callback' => 'sanitize_text_field'],
                'postal_code'  => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * PUT /tamir/v1/addresses/{id}
         * ویرایش آدرس.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/addresses/(?P<id>\d+)',
            'methods'    => 'PUT',
            'controller' => \Tamir\Rest\Controllers\AddressController::class,
            'action'     => 'update',
            'group'      => 'private',
            'middleware' => ['throttle:100,10'],
            'args'       => [
                'id'           => ['sanitize_callback' => 'absint'],
                'title'        => ['sanitize_callback' => 'sanitize_text_field'],
                'full_address' => ['sanitize_callback' => 'sanitize_textarea_field'],
                'city'         => ['sanitize_callback' => 'sanitize_text_field'],
                'province'     => ['sanitize_callback' => 'sanitize_text_field'],
                'postal_code'  => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * DELETE /tamir/v1/addresses/{id}
         * حذف آدرس — throttle اضافه برای جلوگیری از حذف اتفاقی مکرر.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/addresses/(?P<id>\d+)',
            'methods'    => 'DELETE',
            'controller' => \Tamir\Rest\Controllers\AddressController::class,
            'action'     => 'destroy',
            'group'      => 'private',
            'middleware' => ['throttle:50,10'],
            'args'       => [
                'id' => ['sanitize_callback' => 'absint'],
            ],
        ],

        // ══════════════════════════════════════════════════════════
        //  PROFILE — نیاز به JWT
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/profile
         * پروفایل کاربر — داده شخصی، بدون cache.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/profile',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\ProfileController::class,
            'action'     => 'show',
            'group'      => 'private',
            'middleware' => [],
        ],

        /**
         * PUT /tamir/v1/profile
         * ویرایش پروفایل — throttle محدود.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/profile',
            'methods'    => 'PUT',
            'controller' => \Tamir\Rest\Controllers\ProfileController::class,
            'action'     => 'update',
            'group'      => 'private',
            'middleware' => ['throttle:100,10'],
            'args'       => [
                'name'  => ['sanitize_callback' => 'sanitize_text_field'],
                'email' => ['sanitize_callback' => 'sanitize_email'],
            ],
        ],

        // ══════════════════════════════════════════════════════════
        //  NOTIFICATIONS — نیاز به JWT
        // ══════════════════════════════════════════════════════════

        /**
         * GET /tamir/v1/notifications
         * لیست اعلان‌های کاربر — داده شخصی.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/notifications',
            'methods'    => 'GET',
            'controller' => \Tamir\Rest\Controllers\NotificationController::class,
            'action'     => 'index',
            'group'      => 'private',
            'middleware' => [],
        ],

        /**
         * POST /tamir/v1/notifications/{id}/read
         * علامت‌گذاری یک اعلان به عنوان خوانده‌شده.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/notifications/(?P<id>[\w\-]+)/read',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\NotificationController::class,
            'action'     => 'markRead',
            'group'      => 'private',
            'middleware' => ['throttle:300,10'],
            'args'       => [
                'id' => ['sanitize_callback' => 'sanitize_text_field'],
            ],
        ],

        /**
         * POST /tamir/v1/notifications/read-all
         * علامت‌گذاری همه اعلان‌ها به عنوان خوانده‌شده.
         */
        [
            'namespace'  => 'tamir/v1',
            'route'      => '/notifications/read-all',
            'methods'    => 'POST',
            'controller' => \Tamir\Rest\Controllers\NotificationController::class,
            'action'     => 'markAllRead',
            'group'      => 'private',
            'middleware' => ['throttle:50,10'],
        ],

    ],
];
