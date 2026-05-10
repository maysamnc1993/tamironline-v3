<?php

namespace Tamir\Rest\Traits;

use WP_REST_Request;

/**
 * JwtAuthTrait
 * ───────────────────────────────────────────────────────────
 *  منطق مشترک احراز هویت JWT برای همه controllers.
 *
 *  ⚠️ این فایل **یگانه** منبع منطق JWT است. هیچ controller ای
 *  نباید currentUserId() خودش رو پیاده‌سازی کنه — همه باید
 *  از این trait استفاده کنن.
 *
 *  هماهنگ با AuthController v2.2:
 *   - sessions در usermeta('tamir_sessions') ذخیره می‌شن (آرایه)
 *   - هر session: [jti, did, iat, exp, last_seen, ip, ua, grace_until]
 *   - exp check با احتساب grace period
 *
 *  استفاده:
 *    use Tamir\Rest\Traits\JwtAuthTrait;
 *
 *    class MyController extends Controller {
 *        use JwtAuthTrait;
 *
 *        public function authorize(WP_REST_Request $r) {
 *            return $this->currentUserId($r) > 0;
 *        }
 *    }
 *
 * @since 2.2.0
 */
trait JwtAuthTrait
{
    /** نام usermeta ای که sessions در اونجا ذخیره می‌شن */
    private static $TAMIR_SESSIONS_META = 'tamir_sessions';

    /**
     * استخراج user ID از Bearer token.
     *
     * مراحل:
     *  ۱. اگه WP خودش login کرده، همون رو برگردون
     *  ۲. token از header Authorization
     *  ۳. signature verification (HMAC-SHA256)
     *  ۴. exp + grace period
     *  ۵. وجود session در usermeta
     *
     * @return int  user ID در صورت معتبر بودن، صفر در غیر این‌صورت
     */
    protected function currentUserId(WP_REST_Request $r): int
    {
        // ۱. اگه WP خودش login کرده
        $wpUid = get_current_user_id();
        if ($wpUid > 0) return $wpUid;

        // ۲. استخراج token
        $token = $this->extractBearerToken($r);
        if ($token === '') return 0;

        $parts = explode('.', $token);
        if (count($parts) !== 3) return 0;

        // ۳. decode payload
        $rawPayload = base64_decode(strtr($parts[1], '-_', '+/'));
        if ($rawPayload === false) return 0;

        $payload = json_decode($rawPayload, true);
        if (!is_array($payload)) return 0;

        $userId = isset($payload['sub']) ? (int) $payload['sub'] : 0;
        $jti    = isset($payload['jti']) ? (string) $payload['jti'] : '';
        $exp    = isset($payload['exp']) ? (int) $payload['exp'] : 0;

        if ($userId <= 0 || $jti === '') return 0;

        // ۴. signature
        $secret = $this->getJwtSecret();
        $expected = $this->base64url(
            hash_hmac('sha256', $parts[0] . '.' . $parts[1], $secret, true)
        );
        if (!hash_equals($expected, $parts[2])) return 0;

        // ۵. session check
        $sessions = get_user_meta($userId, self::$TAMIR_SESSIONS_META, true);
        if (!is_array($sessions) || !isset($sessions[$jti])) {
            return 0;
        }

        $session    = $sessions[$jti];
        $now        = time();
        $graceUntil = isset($session['grace_until']) ? (int) $session['grace_until'] : 0;

        // ۶. exp + grace period
        if ($exp < $now) {
            // expired — فقط اگه در grace period باشیم قبوله
            if ($graceUntil < $now) {
                return 0;
            }
        }

        // ✅ valid
        wp_set_current_user($userId);
        return $userId;
    }

    /**
     * استخراج Bearer token از request.
     *
     * چندین منبع رو چک می‌کنه چون بسته به وب‌سرور، header در جای متفاوتی
     * قرار می‌گیره:
     *  - WP_REST_Request::get_header (روش استاندارد WP)
     *  - $_SERVER['HTTP_AUTHORIZATION'] (Apache mod_php)
     *  - $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] (Apache rewrite)
     *  - getallheaders() (در صورت موجود بودن)
     */
    protected function extractBearerToken(WP_REST_Request $r): string
    {
        $auth = $r->get_header('Authorization');

        if (!$auth && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (!$auth && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        // برخی CDNها یا nginx ممکنه header رو با نام دیگه‌ای بفرستن
        if (!$auth && function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $key => $value) {
                    if (strcasecmp($key, 'Authorization') === 0) {
                        $auth = $value;
                        break;
                    }
                }
            }
        }

        $auth = trim((string) $auth);
        if ($auth === '' || stripos($auth, 'Bearer ') !== 0) return '';

        return trim(substr($auth, 7));
    }

    /**
     * JWT secret از wp-config.
     *
     * ⚠️ در production حتماً TAMIR_JWT_SECRET رو در wp-config تعریف کن
     * تا از AUTH_KEY استفاده نشه (چون اگه AUTH_KEY عوض بشه، همه session ها
     * invalidate می‌شن).
     */
    protected function getJwtSecret(): string
    {
        if (defined('TAMIR_JWT_SECRET') && TAMIR_JWT_SECRET !== '') {
            return TAMIR_JWT_SECRET;
        }
        if (defined('AUTH_KEY') && AUTH_KEY !== '') {
            return AUTH_KEY;
        }
        error_log('[Tamir JWT] WARNING: TAMIR_JWT_SECRET is not defined in wp-config.php');
        return 'TAMIR_JWT_SECRET_NOT_SET_PLEASE_DEFINE_IN_WP_CONFIG';
    }

    /**
     * base64url encode (همان format که AuthController استفاده می‌کنه).
     */
    protected function base64url($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
