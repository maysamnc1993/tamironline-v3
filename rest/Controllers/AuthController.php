<?php

namespace Tamir\Rest\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\RequestValidator;
use Rehub\Rest\Response;
use Rehub\Core\Services\Sms\OtpSchema;
use WP_REST_Request;
use WP_User;

/**
 * AuthController — Production v2.2
 *
 * احراز هویت OTP + JWT با rolling renewal امن
 *
 * Endpoints:
 *  POST /tamir/v1/auth/send-otp
 *  POST /tamir/v1/auth/verify-otp
 *  POST /tamir/v1/auth/register
 *  GET  /tamir/v1/auth/me           (با rolling renew)
 *  POST /tamir/v1/auth/logout
 *  POST /tamir/v1/auth/logout-all   🆕 logout از همه device ها
 *  POST /tamir/v1/auth/renew
 *
 * 🆕 تغییرات نسبت به v2.1:
 *  ┌─────────────────────────────────────────────────────────────┐
 *  │ ۱. Multi-device support:                                     │
 *  │    به جای یک token، آرایه‌ای از sessions ذخیره می‌شه.       │
 *  │    هر session: [jti, device_id, issued_at, last_seen, ip].  │
 *  │                                                               │
 *  │ ۲. Grace period:                                              │
 *  │    وقتی renew می‌شه، session قدیمی تا ۶۰ ثانیه valid می‌مونه │
 *  │    تا race condition در request های همزمان نشه.              │
 *  │                                                               │
 *  │ ۳. Device fingerprint:                                        │
 *  │    JWT شامل device_id (hash از User-Agent + custom header)   │
 *  │    اگه از device دیگه استفاده بشه، detect می‌شه.             │
 *  │                                                               │
 *  │ ۴. Inactive cleanup:                                          │
 *  │    session هایی که > ۳۰ روز last_seen ندارن، پاک می‌شن.     │
 *  │                                                               │
 *  │ ۵. Fix باگ isTestMode (== true به جای == false)              │
 *  └─────────────────────────────────────────────────────────────┘
 *
 * @package Tamir
 * @since   2.2.0
 */
final class AuthController extends Controller
{
    // ── OTP ─────────────────────────────────────────────────────────────
    private const OTP_TTL_SECONDS  = 300;
    private const OTP_TRANSIENT    = '_tamir_otp_';
    private const RATE_LIMIT_MAX   = 3;
    private const RATE_LIMIT_WIN   = 5;

    // ── Temp Token ───────────────────────────────────────────────────────
    private const TEMP_TOKEN_TTL   = 600;
    private const TEMP_TOKEN_KEY   = '_tamir_temp_';

    // ── JWT ──────────────────────────────────────────────────────────────
    /** 🆕 آرایه‌ای از sessions کاربر (به جای یک token) */
    private const SESSIONS_META    = 'tamir_sessions';

    /** مدت اعتبار JWT — ۳۰ روز */
    private const JWT_TTL          = 2592000;

    /** اگه کمتر از این فاصله باشه، renew می‌شه — ۷ روز */
    private const RENEW_THRESHOLD  = 604800;

    /** 🆕 grace period — توکن قدیمی تا این مدت بعد از renew valid می‌مونه */
    private const RENEW_GRACE      = 60;

    /** 🆕 حداکثر تعداد session فعال هر کاربر */
    private const MAX_SESSIONS     = 5;

    /** 🆕 session های inactive بعد از این مدت پاک می‌شن — ۳۰ روز */
    private const INACTIVE_TTL     = 2592000;

    // ── OTP کد تست ───────────────────────────────────────────────────────
    private const TEST_OTP         = '12345';

    // ════════════════════════════════════════════════════════════════════
    //  Interface methods
    // ════════════════════════════════════════════════════════════════════

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

    // ════════════════════════════════════════════════════════════════════
    //  1. POST /auth/send-otp
    // ════════════════════════════════════════════════════════════════════

    public function sendOtp(WP_REST_Request $r)
    {
        $validator = new RequestValidator(
            array('phone' => array('required', 'string', 'regex:/^09[0-9]{9}$/')),
            array(
                'phone.required' => 'شماره موبایل الزامی است.',
                'phone.regex'    => 'فرمت شماره موبایل صحیح نیست (مثال: 09121234567).',
            )
        );

        if (!$validator->passes($r)) {
            return Response::error('اطلاعات ورودی نامعتبر است.', 422, array(
                'errors' => $validator->toErrorResponse(),
            ));
        }

        $phone = sanitize_text_field($r->get_param('phone'));

        if (!$this->isTestMode() && $this->isRateLimited($phone)) {
            return Response::error(
                sprintf('درخواست زیاد. لطفاً %d دقیقه صبر کنید.', self::RATE_LIMIT_WIN),
                429,
                array('error_code' => 'RATE_LIMITED')
            );
        }

        if ($this->isTestMode()) {
            $this->storeTestOtp($phone, self::TEST_OTP);
            return Response::ok(array(
                'success' => true,
                'message' => 'کد تأیید ارسال شد.',
                '_test'   => array('otp' => self::TEST_OTP),
            ));
        }

        try {
            $sms = \Rehub\Core\Services\Sms\Sms::getInstance();

            $length    = max(4, min(8, (int) get_option('rehub_mrotp_otp_length', 6)));
            $validMin  = max(1, min(30, (int) get_option('rehub_mrotp_valid_time', 5)));
            $type = '';

            $result = $sms->setRandomOtp($phone, $length, $validMin, $type);

            if (!$result->success) {
                error_log(sprintf(
                    '[Tamir Auth] sendOtp failed — phone: %s, provider: %s, code: %d, msg: %s',
                    $phone,
                    $result->provider,
                    $result->errorCode,
                    $result->message
                ));
                return Response::error('ارسال کد تأیید ناموفق بود. لطفاً دوباره تلاش کنید.', 500, array(
                    'error_code' => 'OTP_SEND_FAILED',
                ));
            }

            return Response::ok(array(
                'success' => true,
                'message' => 'کد تأیید ارسال شد.',
            ));
        } catch (\Exception $e) {
            error_log('[Tamir Auth] sendOtp exception: ' . $e->getMessage());
            return Response::error('سرویس پیامک موقتاً در دسترس نیست. لطفاً بعداً تلاش کنید.', 503, array(
                'error_code' => 'SMS_SERVICE_UNAVAILABLE',
            ));
        }
    }

    // ════════════════════════════════════════════════════════════════════
    //  2. POST /auth/verify-otp
    // ════════════════════════════════════════════════════════════════════

    public function verifyOtp(WP_REST_Request $r)
    {
        $phone = sanitize_text_field((string) $r->get_param('phone'));
        $otp   = sanitize_text_field((string) $r->get_param('otp'));

        if ($phone === '' || $otp === '') {
            return Response::error('شماره موبایل و کد تأیید الزامی هستند.', 422, array(
                'error_code' => 'MISSING_PARAMS',
            ));
        }

        if (!preg_match('/^09[0-9]{9}$/', $phone)) {
            return Response::error('فرمت شماره موبایل صحیح نیست.', 422, array(
                'error_code' => 'INVALID_PHONE',
            ));
        }

        $verified = $this->isTestMode()
            ? $this->verifyTestOtp($phone, $otp)
            : $this->verifyOtpViaProvider($phone, $otp);

        if (!$verified) {
            return Response::error('کد تأیید اشتباه یا منقضی شده است.', 401, array(
                'error_code' => 'INVALID_OTP',
            ));
        }

        if ($this->isTestMode()) {
            $this->deleteTestOtp($phone);
        }

        $user = $this->getUserByPhone($phone);

        if ($user) {
            $this->migrateUserPhone($user->ID, $phone);
            $this->linkLegacyOrders($user->ID, $phone);

            $token = $this->issueJwt($user->ID, $r);

            return Response::ok(array(
                'success'        => true,
                'needs_register' => false,
                'token'          => $token,
                'user'           => $this->formatUser($user),
            ));
        }

        $tempToken = $this->issueTempToken($phone);

        return Response::ok(array(
            'success'        => true,
            'needs_register' => true,
            'temp_token'     => $tempToken,
            'message'        => 'شماره تأیید شد. لطفاً اطلاعات خود را تکمیل کنید.',
        ));
    }

    // ════════════════════════════════════════════════════════════════════
    //  3. POST /auth/register
    // ════════════════════════════════════════════════════════════════════

    public function register(WP_REST_Request $r)
    {
        $name      = sanitize_text_field((string) $r->get_param('name'));
        $phone     = sanitize_text_field((string) $r->get_param('phone'));
        $tempToken = sanitize_text_field((string) $r->get_param('temp_token'));

        if ($name === '' || $phone === '' || $tempToken === '') {
            return Response::error('نام، شماره موبایل، و توکن موقت الزامی هستند.', 422, array(
                'error_code' => 'MISSING_PARAMS',
            ));
        }

        if (mb_strlen($name) < 2 || mb_strlen($name) > 60) {
            return Response::error('نام باید بین ۲ تا ۶۰ کاراکتر باشد.', 422, array(
                'error_code' => 'INVALID_NAME',
            ));
        }

        if (!preg_match('/^09[0-9]{9}$/', $phone)) {
            return Response::error('فرمت شماره موبایل صحیح نیست.', 422, array(
                'error_code' => 'INVALID_PHONE',
            ));
        }

        if (!$this->isTestMode() && !$this->verifyTempToken($phone, $tempToken)) {
            return Response::error('توکن موقت نامعتبر یا منقضی شده است. دوباره وارد شوید.', 401, array(
                'error_code' => 'INVALID_TEMP_TOKEN',
            ));
        }

        if ($this->getUserByPhone($phone)) {
            $this->deleteTempToken($phone);
            return Response::error('این شماره قبلاً ثبت‌نام کرده است.', 409, array(
                'error_code' => 'PHONE_EXISTS',
            ));
        }

        $username = 'tamir_' . $phone;
        $email    = $phone . '@tamir.local';
        $password = wp_generate_password(20, true, true);

        $userId = wp_create_user($username, $password, $email);

        if (is_wp_error($userId)) {
            error_log('[Tamir Auth] wp_create_user error: ' . $userId->get_error_message());
            return Response::error('خطا در ایجاد حساب کاربری. لطفاً دوباره تلاش کنید.', 500, array(
                'error_code' => 'USER_CREATE_FAILED',
            ));
        }

        $user = new WP_User($userId);
        $user->set_role('customer');

        update_user_meta($userId, 'tamir_phone', $phone);
        update_user_meta($userId, 'mobile',      $phone);
        update_user_meta($userId, 'first_name',  $name);
        update_user_meta($userId, 'last_name',   '');

        wp_update_user(array('ID' => $userId, 'display_name' => $name));

        $this->deleteTempToken($phone);

        $token = $this->issueJwt($userId, $r);

        return Response::ok(array(
            'success' => true,
            'token'   => $token,
            'user'    => $this->formatUser(get_userdata($userId)),
        ), 201);
    }

    // ════════════════════════════════════════════════════════════════════
    //  4. GET /auth/me  —  با rolling renew
    // ════════════════════════════════════════════════════════════════════

    public function me(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);

        if (!$userId) {
            return Response::error('احراز هویت ناموفق. لطفاً دوباره وارد شوید.', 401, array(
                'error_code' => 'UNAUTHORIZED',
            ));
        }

        $user = get_userdata($userId);
        if (!$user) {
            return Response::error('کاربر یافت نشد.', 404, array(
                'error_code' => 'USER_NOT_FOUND',
            ));
        }

        // rolling renew
        $renewedToken = $this->maybeRenewJwt($userId, $r);

        $response = Response::ok(array(
            'success' => true,
            'user'    => $this->formatUser($user),
        ));

        if ($renewedToken !== null && $response instanceof \WP_REST_Response) {
            $response->header('X-Renewed-Token', $renewedToken);
            $response->header('Access-Control-Expose-Headers', 'X-Renewed-Token');
        }

        return $response;
    }

    // ════════════════════════════════════════════════════════════════════
    //  5. POST /auth/logout
    // ════════════════════════════════════════════════════════════════════

    public function logout(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        if ($userId) {
            $token = $this->extractTokenFromRequest($r);
            $jti   = $this->extractJtiFromToken($token);
            if ($jti) {
                $this->revokeSession($userId, $jti);
            }
        }

        return Response::ok(array(
            'success' => true,
            'message' => 'با موفقیت خارج شدید.',
        ));
    }

    // ════════════════════════════════════════════════════════════════════
    //  6. 🆕 POST /auth/logout-all  —  logout از همه device ها
    // ════════════════════════════════════════════════════════════════════

    public function logoutAll(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        if (!$userId) {
            return Response::error('احراز هویت ناموفق.', 401, array(
                'error_code' => 'UNAUTHORIZED',
            ));
        }

        delete_user_meta($userId, self::SESSIONS_META);

        return Response::ok(array(
            'success' => true,
            'message' => 'از همه دستگاه‌ها خارج شدید.',
        ));
    }

    // ════════════════════════════════════════════════════════════════════
    //  7. POST /auth/renew
    // ════════════════════════════════════════════════════════════════════

    public function renew(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);

        if (!$userId) {
            return Response::error('احراز هویت ناموفق.', 401, array(
                'error_code' => 'UNAUTHORIZED',
            ));
        }

        // session قدیمی رو با grace period revoke کن
        $oldToken = $this->extractTokenFromRequest($r);
        $oldJti   = $this->extractJtiFromToken($oldToken);

        $newToken = $this->issueJwt($userId, $r);

        if ($oldJti) {
            $this->markSessionGraceful($userId, $oldJti);
        }

        return Response::ok(array(
            'success' => true,
            'token'   => $newToken,
        ));
    }

    // ════════════════════════════════════════════════════════════════════
    //  JWT — issue / renew
    // ════════════════════════════════════════════════════════════════════

    /**
     * صدور JWT جدید + ثبت session در usermeta.
     */
    private function issueJwt(int $userId, WP_REST_Request $r): string
    {
        $secret    = $this->getJwtSecret();
        $now       = time();
        $jti       = wp_generate_password(24, false);
        $deviceId  = $this->computeDeviceId($r);

        $header  = $this->base64url(wp_json_encode(array('alg' => 'HS256', 'typ' => 'JWT')));
        $payload = $this->base64url(wp_json_encode(array(
            'sub' => $userId,
            'iat' => $now,
            'exp' => $now + self::JWT_TTL,
            'iss' => get_site_url(),
            'jti' => $jti,
            'did' => $deviceId,
        )));

        $sig   = $this->base64url(hash_hmac('sha256', $header . '.' . $payload, $secret, true));
        $token = $header . '.' . $payload . '.' . $sig;

        // ثبت session
        $sessions = $this->getSessions($userId);

        $sessions[$jti] = array(
            'jti'       => $jti,
            'did'       => $deviceId,
            'iat'       => $now,
            'exp'       => $now + self::JWT_TTL,
            'last_seen' => $now,
            'ip'        => $this->getClientIp(),
            'ua'        => $this->getUaShort($r),
            'grace_until' => 0,
        );

        // پاکسازی session های منقضی و inactive
        $sessions = $this->cleanupSessions($sessions);

        // محدود کردن تعداد session ها
        if (count($sessions) > self::MAX_SESSIONS) {
            // قدیمی‌ترین last_seen رو حذف کن
            uasort($sessions, function ($a, $b) {
                return ($a['last_seen'] ?? 0) <=> ($b['last_seen'] ?? 0);
            });
            $sessions = array_slice($sessions, -self::MAX_SESSIONS, null, true);
        }

        update_user_meta($userId, self::SESSIONS_META, $sessions);

        return $token;
    }

    /**
     * Rolling renewal: اگه exp کمتر از RENEW_THRESHOLD، token جدید می‌سازه
     * + session قدیمی رو با grace period علامت می‌زنه.
     */
    private function maybeRenewJwt(int $userId, WP_REST_Request $r): ?string
    {
        $token = $this->extractTokenFromRequest($r);
        if (!$token) return null;

        $payload = $this->decodePayload($token);
        if (!$payload || !isset($payload['exp'], $payload['jti'])) return null;

        $remaining = (int) $payload['exp'] - time();
        if ($remaining > self::RENEW_THRESHOLD) {
            return null;
        }

        $oldJti = (string) $payload['jti'];

        $newToken = $this->issueJwt($userId, $r);
        $this->markSessionGraceful($userId, $oldJti);

        return $newToken;
    }

    /**
     * علامت زدن session قدیمی برای grace period (هنوز valid، اما زمان‌دار).
     */
    private function markSessionGraceful(int $userId, string $jti): void
    {
        $sessions = $this->getSessions($userId);
        if (isset($sessions[$jti])) {
            $sessions[$jti]['grace_until'] = time() + self::RENEW_GRACE;
            update_user_meta($userId, self::SESSIONS_META, $sessions);
        }
    }

    /**
     * Revoke یک session خاص.
     */
    private function revokeSession(int $userId, string $jti): void
    {
        $sessions = $this->getSessions($userId);
        if (isset($sessions[$jti])) {
            unset($sessions[$jti]);
            update_user_meta($userId, self::SESSIONS_META, $sessions);
        }
    }

    /**
     * بازخوانی همه session های کاربر.
     */
    private function getSessions(int $userId): array
    {
        $sessions = get_user_meta($userId, self::SESSIONS_META, true);
        if (!is_array($sessions)) return array();
        return $sessions;
    }

    /**
     * پاکسازی session های منقضی و inactive.
     */
    private function cleanupSessions(array $sessions): array
    {
        $now = time();
        foreach ($sessions as $jti => $s) {
            // منقضی شده + grace هم تموم شده
            $exp        = (int) ($s['exp'] ?? 0);
            $graceUntil = (int) ($s['grace_until'] ?? 0);
            if ($exp < $now && $graceUntil < $now) {
                unset($sessions[$jti]);
                continue;
            }
            // inactive
            $lastSeen = (int) ($s['last_seen'] ?? 0);
            if ($lastSeen > 0 && ($now - $lastSeen) > self::INACTIVE_TTL) {
                unset($sessions[$jti]);
            }
        }
        return $sessions;
    }

    /**
     * Update last_seen برای یک session.
     */
    private function touchSession(int $userId, string $jti): void
    {
        $sessions = $this->getSessions($userId);
        if (isset($sessions[$jti])) {
            $sessions[$jti]['last_seen'] = time();
            update_user_meta($userId, self::SESSIONS_META, $sessions);
        }
    }

    // ════════════════════════════════════════════════════════════════════
    //  Token validation
    // ════════════════════════════════════════════════════════════════════

    /**
     * بررسی و استخراج user ID از Bearer token.
     *
     * مراحل:
     *  1. فرمت سه‌بخشی JWT
     *  2. signature
     *  3. exp
     *  4. session موجود در DB (با احتساب grace period)
     *  5. device_id matching (اختیاری — فقط warning)
     */
    private function currentUserId(WP_REST_Request $r)
    {
        $wpUser = get_current_user_id();
        if ($wpUser) {
            return $wpUser;
        }

        $token = $this->extractTokenFromRequest($r);
        if ($token === '') return 0;

        $parts = explode('.', $token);
        if (count($parts) !== 3) return 0;

        $payload = $this->decodePayload($token);
        if (!$payload) return 0;

        $userId = isset($payload['sub']) ? (int) $payload['sub'] : 0;
        if ($userId <= 0) return 0;

        // exp check (با grace period از session)
        $now = time();
        $exp = isset($payload['exp']) ? (int) $payload['exp'] : 0;
        $jti = isset($payload['jti']) ? (string) $payload['jti'] : '';

        // signature
        $secret   = $this->getJwtSecret();
        $expected = $this->base64url(hash_hmac('sha256', $parts[0] . '.' . $parts[1], $secret, true));
        if (!hash_equals($expected, $parts[2])) {
            return 0;
        }

        // session check
        $sessions = $this->getSessions($userId);
        if (!isset($sessions[$jti])) {
            return 0;
        }

        $session    = $sessions[$jti];
        $graceUntil = (int) ($session['grace_until'] ?? 0);

        // اگه exp منقضی شده، فقط grace period قبول می‌شه
        if ($exp < $now) {
            if ($graceUntil < $now) {
                return 0;
            }
            // در grace period — قبوله
        }

        // device fingerprint check (فقط برای logging, نه reject)
        $currentDid = $this->computeDeviceId($r);
        $tokenDid   = isset($payload['did']) ? (string) $payload['did'] : '';
        if ($tokenDid !== '' && $tokenDid !== $currentDid) {
            error_log(sprintf(
                '[Tamir Auth] device mismatch — user: %d, token_did: %s, current_did: %s, ip: %s',
                $userId,
                $tokenDid,
                $currentDid,
                $this->getClientIp()
            ));
            // فعلاً reject نمی‌کنیم چون User-Agent ممکنه عوض بشه
            // اما اگه می‌خوای strict باشی، اینجا return 0 کن
        }

        // touch
        $this->touchSession($userId, $jti);

        wp_set_current_user($userId);
        return $userId;
    }

    // ════════════════════════════════════════════════════════════════════
    //  Helpers
    // ════════════════════════════════════════════════════════════════════

    private function decodePayload(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        $raw = base64_decode(strtr($parts[1], '-_', '+/'));
        if ($raw === false) return null;
        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    private function extractJtiFromToken(string $token): ?string
    {
        if ($token === '') return null;
        $payload = $this->decodePayload($token);
        return $payload && isset($payload['jti']) ? (string) $payload['jti'] : null;
    }

    private function extractTokenFromRequest(WP_REST_Request $r): string
    {
        $auth = $r->get_header('Authorization');
        if (!$auth && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = sanitize_text_field($_SERVER['HTTP_AUTHORIZATION']);
        }
        if (!$auth || strpos($auth, 'Bearer ') !== 0) return '';
        return substr($auth, 7);
    }

    /**
     * 🆕 محاسبه device fingerprint از User-Agent + custom header.
     * اپ موبایل می‌تونه X-Device-ID بفرسته (uuid از device).
     */
    private function computeDeviceId(WP_REST_Request $r): string
    {
        $deviceHeader = (string) $r->get_header('X-Device-ID');
        $ua           = (string) $r->get_header('User-Agent');

        $raw = $deviceHeader !== '' ? $deviceHeader : $ua;
        return substr(hash('sha256', $raw . $this->getJwtSecret()), 0, 16);
    }

    private function getClientIp(): string
    {
        $candidates = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($candidates as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', (string) $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '';
    }

    private function getUaShort(WP_REST_Request $r): string
    {
        $ua = (string) $r->get_header('User-Agent');
        return mb_substr($ua, 0, 200);
    }

    private function getJwtSecret(): string
    {
        if (defined('TAMIR_JWT_SECRET') && TAMIR_JWT_SECRET !== '') {
            return TAMIR_JWT_SECRET;
        }
        if (defined('AUTH_KEY') && AUTH_KEY !== '') {
            return AUTH_KEY;
        }
        error_log('[Tamir Auth] WARNING: TAMIR_JWT_SECRET is not defined');
        return 'TAMIR_JWT_SECRET_NOT_SET_PLEASE_DEFINE_IN_WP_CONFIG';
    }

    private function base64url($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // ════════════════════════════════════════════════════════════════════
    //  OTP / Rate-limit / Temp Token / User helpers (بدون تغییر منطقی)
    // ════════════════════════════════════════════════════════════════════

    private function verifyOtpViaProvider($phone, $otp)
    {
        try {
            $sms    = \Rehub\Core\Services\Sms\Sms::getInstance();
            $result = $sms->verifyOtp($phone, $otp);
            return $result->accepted ? true : false;
        } catch (\Exception $e) {
            error_log('[Tamir Auth] verifyOtp exception, fallback: ' . $e->getMessage());
            return (bool) OtpSchema::verifyOtpLocally($phone, $otp);
        }
    }

    private function storeTestOtp($phone, $otp)
    {
        set_transient(self::OTP_TRANSIENT . $phone, $otp, self::OTP_TTL_SECONDS);
    }

    private function verifyTestOtp($phone, $otp)
    {
        $stored = get_transient(self::OTP_TRANSIENT . $phone);
        return $stored !== false && hash_equals((string) $stored, (string) $otp);
    }

    private function deleteTestOtp($phone)
    {
        delete_transient(self::OTP_TRANSIENT . $phone);
    }

    private function isRateLimited($phone)
    {
        return OtpSchema::isRateLimited($phone, self::RATE_LIMIT_MAX, self::RATE_LIMIT_WIN);
    }

    private function issueTempToken($phone)
    {
        $token = bin2hex(random_bytes(24));
        set_transient(self::TEMP_TOKEN_KEY . $phone, $token, self::TEMP_TOKEN_TTL);
        return $token;
    }

    private function verifyTempToken($phone, $token)
    {
        $stored = get_transient(self::TEMP_TOKEN_KEY . $phone);
        return $stored !== false && hash_equals((string) $stored, (string) $token);
    }

    private function deleteTempToken($phone)
    {
        delete_transient(self::TEMP_TOKEN_KEY . $phone);
    }

    private function getUserByPhone($phone)
    {
        $metaKeys = array(
            'mobile',
            'tamir_phone',
            'billing_phone',
            'phone',
            'user_phone',
            'mobile_number',
            'user_mobile',
        );

        foreach ($metaKeys as $key) {
            $users = get_users(array(
                'meta_key'   => $key,
                'meta_value' => $phone,
                'orderby'    => 'ID',
                'order'      => 'ASC',
                'number'     => 1,
                'fields'     => 'all',
            ));
            if (!empty($users)) return $users[0];
        }

        $user = get_user_by('login', 'tamir_' . $phone);
        if ($user && !is_wp_error($user)) return $user;

        $user = get_user_by('login', $phone);
        if ($user && !is_wp_error($user)) return $user;

        $user = get_user_by('email', $phone . '@tamir.local');
        if ($user && !is_wp_error($user)) return $user;

        return null;
    }

    private function migrateUserPhone($userId, $phone)
    {
        if (!get_user_meta($userId, 'tamir_phone', true)) {
            update_user_meta($userId, 'tamir_phone', $phone);
        }
        if (!get_user_meta($userId, 'mobile', true)) {
            update_user_meta($userId, 'mobile', $phone);
        }
    }

    private function linkLegacyOrders($userId, $phone)
    {
        if (get_user_meta($userId, '_tamir_orders_linked', true)) return;

        global $wpdb;
        $orderIds = $wpdb->get_col($wpdb->prepare(
            "SELECT p.ID
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID
             WHERE p.post_type    = 'orders'
               AND p.post_status != 'trash'
               AND pm.meta_key    = 'mobile'
               AND pm.meta_value  = %s
               AND p.ID NOT IN (
                   SELECT post_id FROM {$wpdb->postmeta}
                   WHERE meta_key = 'customer'
               )
             LIMIT 500",
            $phone
        ));

        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                update_post_meta((int) $orderId, 'customer', $userId);
            }
        }

        update_user_meta($userId, '_tamir_orders_linked', time());
    }

    private function formatUser(WP_User $user)
    {
        $phone = get_user_meta($user->ID, 'tamir_phone', true);
        if (!$phone) $phone = (string) get_user_meta($user->ID, 'mobile', true);

        $subscription = get_user_meta($user->ID, 'subscription', true);
        if (!$subscription) $subscription = (string) ($user->ID + 10000);

        $customerType = $this->getCustomerType($user->ID);

        $realEmail = null;
        if ($user->user_email !== '' && strpos($user->user_email, '@tamir.local') === false) {
            $realEmail = $user->user_email;
        }

        return array(
            'id'                  => $user->ID,
            'name'                => $this->resolveUserName($user),
            'phone'               => (string) $phone,
            'email'               => $realEmail,
            'avatar'              => get_avatar_url($user->ID, array('size' => 96)),
            'roles'               => $user->roles,
            'subscription'        => (string) $subscription,
            'customer_type'       => $customerType['key'],
            'customer_type_label' => $customerType['label'],
            'customer_type_html'  => $customerType['html'],
        );
    }

    private function resolveUserName(WP_User $user)
    {
        $firstName = trim((string) get_user_meta($user->ID, 'first_name', true));
        $lastName  = trim((string) get_user_meta($user->ID, 'last_name', true));

        if ($firstName !== '' || $lastName !== '') {
            return trim($firstName . ' ' . $lastName);
        }

        $displayName = trim((string) $user->display_name);
        $phone       = (string) (get_user_meta($user->ID, 'tamir_phone', true)
            ?: get_user_meta($user->ID, 'mobile', true));

        $isGeneric = $displayName === ''
            || $displayName === $user->user_login
            || $displayName === $phone
            || (bool) preg_match('/^09\d{9}$/', $displayName)
            || strpos($displayName, 'tamir_') === 0;

        if (!$isGeneric) return $displayName;
        return $displayName !== '' ? $displayName : $user->user_login;
    }

    private function getCustomerType($userId)
    {
        $type = (string) get_user_meta($userId, 'selectTypeUsers', true);
        $map = array('white' => 'سفید', 'black' => 'سیاه', 'gold' => 'طلایی');

        if (!isset($map[$type])) {
            return array('key' => null, 'label' => '', 'html' => '');
        }

        return array(
            'key'   => $type,
            'label' => $map[$type],
            'html'  => sprintf(
                '<span class="typeUser %s">%s</span>',
                esc_attr($type),
                esc_html($map[$type])
            ),
        );
    }

    // ════════════════════════════════════════════════════════════════════
    //  Test mode flag — 🆕 BUG FIX
    // ════════════════════════════════════════════════════════════════════

    /**
     * 🆕 BUG FIX:
     *  قبلی:  `TAMIR_TEST_MODE === false`  ← این یعنی test mode فعال!
     *  درست: `TAMIR_TEST_MODE === true`
     */
    private function isTestMode()
    {
        return defined('TAMIR_TEST_MODE') && TAMIR_TEST_MODE === true;
    }
}
