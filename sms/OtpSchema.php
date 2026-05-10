<?php

namespace Rehub\Core\Services\Sms;

/**
 * OtpSchema
 *
 * Manages database tables for the OTP system.
 *
 * Tables:
 *  - {prefix}hdm_otp_codes       — Stores active OTP codes (set via MrOTP or Kavenegar)
 *  - {prefix}hdm_verify_sessions — Short-lived tokens issued after successful OTP verify
 *  - {prefix}hdm_sms_log         — Audit log for all SMS/OTP send attempts
 *
 * Usage:
 *   OtpSchema::install();           // Create / upgrade tables
 *   OtpSchema::scheduleCleanup();   // Register WP-Cron cleanup job
 *   OtpSchema::cleanupExpired();    // Delete expired rows (called by cron)
 *   OtpSchema::getStats();          // Dashboard statistics
 *
 * @package Rehub
 * @since   1.0.0
 */
class OtpSchema
{
    // ------------------------------------------------------------------ //
    //  Constants
    // ------------------------------------------------------------------ //

    /** Cron hook name */
    const CRON_HOOK = 'hdm_otp_cleanup_cron';

    /** How often the cleanup cron runs */
    const CRON_INTERVAL = 'hourly';

    /** Schema version stored in WP options — bump when altering tables */
    const SCHEMA_VERSION = '1.2.0';
    const SCHEMA_VERSION_OPTION = 'hdm_otp_schema_version';

    // OTP status values
    const OTP_STATUS_ACTIVE   = 'active';
    const OTP_STATUS_VERIFIED = 'verified';
    const OTP_STATUS_EXPIRED  = 'expired';

    // VS token status values
    const VS_STATUS_ACTIVE = 'active';
    const VS_STATUS_USED   = 'used';
    const VS_STATUS_EXPIRED = 'expired';

    // SMS log status values
    const LOG_STATUS_SENT   = 'sent';
    const LOG_STATUS_FAILED = 'failed';

    // ------------------------------------------------------------------ //
    //  Install / Upgrade
    // ------------------------------------------------------------------ //

    /**
     * Create or upgrade all OTP-related database tables.
     *
     * Safe to call multiple times — uses dbDelta() which is idempotent.
     * Call this on theme activation and after plugin/theme upgrades.
     *
     * @return void
     */
    public static function install(): void
    {
        global $wpdb;

        $charset = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // ── Table 1: OTP Codes ──────────────────────────────────────────
        // Stores OTP codes created via setOTP / setRandomOTP.
        // Kavenegar users who store their own codes use this table too.
        $otpTable = $wpdb->prefix . 'hdm_otp_codes';

        dbDelta("CREATE TABLE {$otpTable} (
            id           BIGINT(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
            mobile       VARCHAR(15)  NOT NULL,
            otp_code     VARCHAR(8)   NOT NULL,
            provider     VARCHAR(20)  NOT NULL DEFAULT 'mrotp',
            otp_type     VARCHAR(10)  NOT NULL DEFAULT 'USSD'
                            COMMENT 'USSD | SMS | VOICE',
            status       VARCHAR(10)  NOT NULL DEFAULT 'active'
                            COMMENT 'active | verified | expired',
            valid_until  DATETIME     NOT NULL
                            COMMENT 'UTC timestamp when this OTP expires',
            ussd_code    VARCHAR(50)  NOT NULL DEFAULT ''
                            COMMENT 'e.g. *6655*100200300#',
            ip_address   VARCHAR(45)  NOT NULL DEFAULT ''
                            COMMENT 'IPv4 or IPv6 of requester',
            user_agent   VARCHAR(255) NOT NULL DEFAULT '',
            created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            verified_at  DATETIME     NULL      DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY idx_mobile        (mobile),
            KEY idx_status        (status),
            KEY idx_valid_until   (valid_until),
            KEY idx_mobile_status (mobile, status)
        ) {$charset};");

        // ── Table 2: Verification Sessions ─────────────────────────────
        // A short-lived token issued to the front-end after a successful
        // verifyOtp() call. The REST controller exchanges this for a WP
        // auth cookie / JWT. Prevents replay attacks.
        $vsTable = $wpdb->prefix . 'hdm_verify_sessions';

        dbDelta("CREATE TABLE {$vsTable} (
            id           BIGINT(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
            token        CHAR(64)     NOT NULL
                            COMMENT 'SHA-256 hex token sent to the client',
            mobile       VARCHAR(15)  NOT NULL,
            otp_id       BIGINT(20)   UNSIGNED NOT NULL DEFAULT 0
                            COMMENT 'FK to hdm_otp_codes.id',
            status       VARCHAR(10)  NOT NULL DEFAULT 'active'
                            COMMENT 'active | used | expired',
            expires_at   DATETIME     NOT NULL
                            COMMENT 'Token valid for 15 minutes after issue',
            ip_address   VARCHAR(45)  NOT NULL DEFAULT '',
            created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            used_at      DATETIME     NULL      DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY   uq_token     (token),
            KEY idx_mobile            (mobile),
            KEY idx_status            (status),
            KEY idx_expires_at        (expires_at)
        ) {$charset};");

        // ── Table 3: SMS Log ────────────────────────────────────────────
        // Audit trail for every send attempt (OTP and pattern SMS).
        $logTable = $wpdb->prefix . 'hdm_sms_log';

        dbDelta("CREATE TABLE {$logTable} (
            id           BIGINT(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
            mobile       VARCHAR(15)  NOT NULL,
            provider     VARCHAR(20)  NOT NULL DEFAULT '',
            action       VARCHAR(30)  NOT NULL DEFAULT ''
                            COMMENT 'setOTP | setRandomOTP | verifyOTP | sendPatternSMS | send',
            otp_type     VARCHAR(10)  NOT NULL DEFAULT ''
                            COMMENT 'USSD | SMS | VOICE | plain',
            status       VARCHAR(10)  NOT NULL DEFAULT 'sent'
                            COMMENT 'sent | failed',
            error_code   INT(6)       NOT NULL DEFAULT 0,
            error_msg    VARCHAR(255) NOT NULL DEFAULT '',
            request_data TEXT         NOT NULL DEFAULT ''
                            COMMENT 'JSON-encoded sanitised request params (no OTP value)',
            response_data TEXT        NOT NULL DEFAULT ''
                            COMMENT 'JSON-encoded provider response',
            ip_address   VARCHAR(45)  NOT NULL DEFAULT '',
            created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_mobile     (mobile),
            KEY idx_provider   (provider),
            KEY idx_status     (status),
            KEY idx_created_at (created_at)
        ) {$charset};");

        // Store the schema version so future upgrades know what to do
        update_option(self::SCHEMA_VERSION_OPTION, self::SCHEMA_VERSION);
    }

    /**
     * Drop all OTP tables.
     *
     * ⚠️  Destructive — only call on full uninstall.
     *
     * @return void
     */
    public static function uninstall(): void
    {
        global $wpdb;

        foreach (['hdm_sms_log', 'hdm_verify_sessions', 'hdm_otp_codes'] as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
        }

        delete_option(self::SCHEMA_VERSION_OPTION);
        delete_option('hdm_public_token');
    }

    // ------------------------------------------------------------------ //
    //  WP-Cron helpers
    // ------------------------------------------------------------------ //

    /**
     * Register the cleanup cron job if not already scheduled.
     *
     * @return void
     */
    public static function scheduleCleanup(): void
    {
        if (! wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time(), self::CRON_INTERVAL, self::CRON_HOOK);
        }
    }

    /**
     * Remove the cleanup cron job.
     * Call on theme deactivation.
     *
     * @return void
     */
    public static function unscheduleCleanup(): void
    {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }
    }

    // ------------------------------------------------------------------ //
    //  OTP CRUD
    // ------------------------------------------------------------------ //

    /**
     * Insert a new OTP record into the database.
     *
     * Called immediately after a successful setOTP / setRandomOTP API call
     * so we have a local record for auditing and fallback verification.
     *
     * @param  string $mobile      Iranian mobile — e.g. 09121112233
     * @param  string $otpCode     The OTP value (3–8 digits)
     * @param  int    $validMinutes Minutes until expiry
     * @param  string $provider    'mrotp' | 'kavenegar'
     * @param  string $otpType     'USSD' | 'SMS' | 'VOICE'
     * @param  string $ussdCode    USSD dial string returned by the provider
     * @return int|false           Inserted row ID, or false on failure
     */
    public static function insertOtp(
        string $mobile,
        string $otpCode,
        int    $validMinutes,
        string $provider = 'mrotp',
        string $otpType  = 'USSD',
        string $ussdCode = ''
    ) {
        global $wpdb;

        $validUntil = gmdate('Y-m-d H:i:s', time() + ($validMinutes * 60));

        $rows = $wpdb->insert(
            $wpdb->prefix . 'hdm_otp_codes',
            [
                'mobile'      => $mobile,
                'otp_code'    => $otpCode,
                'provider'    => $provider,
                'otp_type'    => strtoupper($otpType),
                'status'      => self::OTP_STATUS_ACTIVE,
                'valid_until' => $validUntil,
                'ussd_code'   => $ussdCode,
                'ip_address'  => self::getClientIp(),
                'user_agent'  => isset($_SERVER['HTTP_USER_AGENT'])
                                    ? substr(sanitize_text_field($_SERVER['HTTP_USER_AGENT']), 0, 255)
                                    : '',
                'created_at'  => current_time('mysql', true),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        return $rows ? (int) $wpdb->insert_id : false;
    }

    /**
     * Mark an OTP record as verified.
     *
     * @param  int $id  Row ID from hdm_otp_codes
     * @return bool
     */
    public static function markOtpVerified(int $id): bool
    {
        global $wpdb;

        return (bool) $wpdb->update(
            $wpdb->prefix . 'hdm_otp_codes',
            [
                'status'      => self::OTP_STATUS_VERIFIED,
                'verified_at' => current_time('mysql', true),
            ],
            ['id' => $id],
            ['%s', '%s'],
            ['%d']
        );
    }

    /**
     * Find the most recent active OTP for a mobile number.
     *
     * Returns null if none found or all are expired.
     *
     * @param  string $mobile
     * @return object|null   Row from hdm_otp_codes
     */
    public static function findActiveOtp(string $mobile): ?object
    {
        global $wpdb;

        $table = $wpdb->prefix . 'hdm_otp_codes';
        $now   = current_time('mysql', true);

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table}
             WHERE  mobile      = %s
             AND    status      = %s
             AND    valid_until > %s
             ORDER  BY id DESC
             LIMIT  1",
            $mobile,
            self::OTP_STATUS_ACTIVE,
            $now
        ));
    }

    /**
     * Verify an OTP locally (without hitting the provider API).
     *
     * Useful as a fallback when MrOTP API is unreachable.
     *
     * @param  string $mobile
     * @param  string $otpCode
     * @return object|null   The matched row, or null if invalid/expired
     */
    public static function verifyOtpLocally(string $mobile, string $otpCode): ?object
    {
        global $wpdb;

        $table = $wpdb->prefix . 'hdm_otp_codes';
        $now   = current_time('mysql', true);

        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table}
             WHERE  mobile      = %s
             AND    otp_code    = %s
             AND    status      = %s
             AND    valid_until > %s
             ORDER  BY id DESC
             LIMIT  1",
            $mobile,
            $otpCode,
            self::OTP_STATUS_ACTIVE,
            $now
        ));

        if ($row) {
            self::markOtpVerified((int) $row->id);
        }

        return $row;
    }

    // ------------------------------------------------------------------ //
    //  Verification Session CRUD
    // ------------------------------------------------------------------ //

    /**
     * Create a new verification session token.
     *
     * Issue this token to the client after a successful OTP verify.
     * The REST endpoint that does the final login should call consumeVsToken().
     *
     * @param  string $mobile   Verified mobile number
     * @param  int    $otpId    Row ID of the verified OTP
     * @param  int    $ttlMin   Token lifetime in minutes (default 15)
     * @return string|false     The plain-text token to send to the client, or false
     */
    public static function createVsToken(string $mobile, int $otpId, int $ttlMin = 15)
    {
        global $wpdb;

        // Generate a cryptographically random token
        $plainToken = bin2hex(random_bytes(32)); // 64-char hex
        $expiresAt  = gmdate('Y-m-d H:i:s', time() + ($ttlMin * 60));

        $rows = $wpdb->insert(
            $wpdb->prefix . 'hdm_verify_sessions',
            [
                'token'      => $plainToken,
                'mobile'     => $mobile,
                'otp_id'     => $otpId,
                'status'     => self::VS_STATUS_ACTIVE,
                'expires_at' => $expiresAt,
                'ip_address' => self::getClientIp(),
                'created_at' => current_time('mysql', true),
            ],
            ['%s', '%s', '%d', '%s', '%s', '%s', '%s']
        );

        return $rows ? $plainToken : false;
    }

    /**
     * Consume (use) a verification session token.
     *
     * Marks the token as 'used' so it cannot be replayed.
     * Returns the row data so the caller knows which mobile was verified.
     *
     * @param  string $token   Plain-text token from the client
     * @return object|null     The session row (with mobile), or null if invalid/used/expired
     */
    public static function consumeVsToken(string $token): ?object
    {
        global $wpdb;

        $table = $wpdb->prefix . 'hdm_verify_sessions';
        $now   = current_time('mysql', true);

        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table}
             WHERE  token      = %s
             AND    status     = %s
             AND    expires_at > %s
             LIMIT  1",
            $token,
            self::VS_STATUS_ACTIVE,
            $now
        ));

        if (! $row) {
            return null;
        }

        // Mark as used immediately — single-use guarantee
        $wpdb->update(
            $table,
            ['status' => self::VS_STATUS_USED, 'used_at' => $now],
            ['id'     => (int) $row->id],
            ['%s', '%s'],
            ['%d']
        );

        return $row;
    }

    // ------------------------------------------------------------------ //
    //  SMS Log
    // ------------------------------------------------------------------ //

    /**
     * Write an entry to the SMS audit log.
     *
     * Call this after every send attempt (success or failure).
     * OTP values are intentionally excluded from request_data.
     *
     * @param  string $mobile
     * @param  string $provider      'mrotp' | 'kavenegar'
     * @param  string $action        'setOTP' | 'setRandomOTP' | 'verifyOTP' | 'sendPatternSMS' | 'send'
     * @param  string $otpType       'USSD' | 'SMS' | 'VOICE' | 'plain'
     * @param  string $status        'sent' | 'failed'
     * @param  int    $errorCode     Provider error code (0 = no error)
     * @param  string $errorMsg      Human-readable error message
     * @param  array  $requestData   Sanitised request params (MUST NOT include OTP value)
     * @param  array  $responseData  Provider response payload
     * @return int|false             Inserted log ID, or false on failure
     */
    public static function log(
        string $mobile,
        string $provider,
        string $action,
        string $otpType,
        string $status,
        int    $errorCode   = 0,
        string $errorMsg    = '',
        array  $requestData = [],
        array  $responseData = []
    ) {
        global $wpdb;

        // Safety: strip any OTP/token fields from request data before logging
        $safeRequest = array_diff_key(
            $requestData,
            array_flip(['OTP', 'otp', 'token', 'password', 'apiKey', 'api_key'])
        );

        $rows = $wpdb->insert(
            $wpdb->prefix . 'hdm_sms_log',
            [
                'mobile'        => $mobile,
                'provider'      => $provider,
                'action'        => $action,
                'otp_type'      => $otpType,
                'status'        => $status,
                'error_code'    => $errorCode,
                'error_msg'     => substr($errorMsg, 0, 255),
                'request_data'  => wp_json_encode($safeRequest),
                'response_data' => wp_json_encode($responseData),
                'ip_address'    => self::getClientIp(),
                'created_at'    => current_time('mysql', true),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s']
        );

        return $rows ? (int) $wpdb->insert_id : false;
    }

    // ------------------------------------------------------------------ //
    //  Cleanup
    // ------------------------------------------------------------------ //

    /**
     * Delete expired OTP codes and verification sessions.
     *
     * Called by the WP-Cron job registered in scheduleCleanup().
     * Also expire OTPs that are still 'active' but past their valid_until.
     *
     * @return int  Total rows deleted
     */
    public static function cleanupExpired(): int
    {
        global $wpdb;

        $now   = current_time('mysql', true);
        $total = 0;

        // 1. Mark stale active OTPs as expired
        $total += (int) $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}hdm_otp_codes
             SET    status = %s
             WHERE  status = %s
             AND    valid_until <= %s",
            self::OTP_STATUS_EXPIRED,
            self::OTP_STATUS_ACTIVE,
            $now
        ));

        // 2. Hard-delete OTP rows older than 30 days
        $total += (int) $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}hdm_otp_codes
             WHERE  created_at < %s",
            gmdate('Y-m-d H:i:s', strtotime('-30 days'))
        ));

        // 3. Mark stale active VS tokens as expired
        $total += (int) $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}hdm_verify_sessions
             SET    status = %s
             WHERE  status = %s
             AND    expires_at <= %s",
            self::VS_STATUS_EXPIRED,
            self::VS_STATUS_ACTIVE,
            $now
        ));

        // 4. Hard-delete VS rows older than 7 days
        $total += (int) $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}hdm_verify_sessions
             WHERE  created_at < %s",
            gmdate('Y-m-d H:i:s', strtotime('-7 days'))
        ));

        // 5. Hard-delete SMS log rows older than 90 days
        $total += (int) $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}hdm_sms_log
             WHERE  created_at < %s",
            gmdate('Y-m-d H:i:s', strtotime('-90 days'))
        ));

        return $total;
    }

    // ------------------------------------------------------------------ //
    //  Statistics
    // ------------------------------------------------------------------ //

    /**
     * Return aggregate statistics for the admin dashboard.
     *
     * @return array{
     *   otp_total:        int,
     *   otp_active:       int,
     *   otp_verified_24h: int,
     *   vs_total:         int,
     *   vs_active:        int,
     *   sms_sent_24h:     int,
     *   sms_failed_24h:   int,
     * }
     */
    public static function getStats(): array
    {
        global $wpdb;

        $otpTable = $wpdb->prefix . 'hdm_otp_codes';
        $vsTable  = $wpdb->prefix . 'hdm_verify_sessions';
        $logTable = $wpdb->prefix . 'hdm_sms_log';
        $day      = gmdate('Y-m-d H:i:s', strtotime('-24 hours'));

        return [
            'otp_total'        => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$otpTable}"),
            'otp_active'       => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$otpTable} WHERE status = %s AND valid_until > %s",
                self::OTP_STATUS_ACTIVE, current_time('mysql', true)
            )),
            'otp_verified_24h' => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$otpTable} WHERE status = %s AND verified_at >= %s",
                self::OTP_STATUS_VERIFIED, $day
            )),
            'vs_total'         => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$vsTable}"),
            'vs_active'        => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$vsTable} WHERE status = %s AND expires_at > %s",
                self::VS_STATUS_ACTIVE, current_time('mysql', true)
            )),
            'sms_sent_24h'     => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$logTable} WHERE status = %s AND created_at >= %s",
                self::LOG_STATUS_SENT, $day
            )),
            'sms_failed_24h'   => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$logTable} WHERE status = %s AND created_at >= %s",
                self::LOG_STATUS_FAILED, $day
            )),
        ];
    }

    /**
     * Return time-bucketed activity stats.
     *
     * @param  string $period  'day' | 'week' | 'month'
     * @return array{
     *   otp_requests:       int,
     *   otp_verified:       int,
     *   verification_rate:  float,
     *   vs_created:         int,
     *   vs_used:            int,
     *   vs_usage_rate:      float,
     * }
     */
    public static function getActivityStats(string $period = 'day'): array
    {
        global $wpdb;

        $otpTable = $wpdb->prefix . 'hdm_otp_codes';
        $vsTable  = $wpdb->prefix . 'hdm_verify_sessions';

        if ($period === 'week') {
            $since = gmdate('Y-m-d H:i:s', strtotime('-7 days'));
        } elseif ($period === 'month') {
            $since = gmdate('Y-m-d H:i:s', strtotime('-30 days'));
        } else {
            $since = gmdate('Y-m-d H:i:s', strtotime('-24 hours'));
        }

        $otpRequests = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$otpTable} WHERE created_at >= %s", $since
        ));

        $otpVerified = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$otpTable} WHERE status = %s AND created_at >= %s",
            self::OTP_STATUS_VERIFIED, $since
        ));

        $vsCreated = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$vsTable} WHERE created_at >= %s", $since
        ));

        $vsUsed = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$vsTable} WHERE status = %s AND created_at >= %s",
            self::VS_STATUS_USED, $since
        ));

        return [
            'otp_requests'      => $otpRequests,
            'otp_verified'      => $otpVerified,
            'verification_rate' => $otpRequests > 0
                                    ? round(($otpVerified / $otpRequests) * 100, 1)
                                    : 0.0,
            'vs_created'        => $vsCreated,
            'vs_used'           => $vsUsed,
            'vs_usage_rate'     => $vsCreated > 0
                                    ? round(($vsUsed / $vsCreated) * 100, 1)
                                    : 0.0,
        ];
    }

    /**
     * Check whether all required tables exist.
     *
     * @return bool
     */
    public static function tablesExist(): bool
    {
        global $wpdb;

        foreach (['hdm_otp_codes', 'hdm_verify_sessions', 'hdm_sms_log'] as $table) {
            $full = $wpdb->prefix . $table;
            if ($wpdb->get_var("SHOW TABLES LIKE '{$full}'") !== $full) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the stored schema version.
     * Useful for running conditional upgrade migrations.
     *
     * @return string  e.g. '1.2.0', or '' if never installed
     */
    public static function getInstalledVersion(): string
    {
        return (string) get_option(self::SCHEMA_VERSION_OPTION, '');
    }

    // ------------------------------------------------------------------ //
    //  Rate-limiting helpers
    // ------------------------------------------------------------------ //

    /**
     * Count how many OTP requests a mobile number made in the last N minutes.
     *
     * Use this to implement server-side rate limiting before calling the
     * provider API (to avoid burning SMS credits on brute-force attempts).
     *
     * @param  string $mobile
     * @param  int    $windowMinutes  Lookback window in minutes
     * @return int
     */
    public static function countRecentRequests(string $mobile, int $windowMinutes = 5): int
    {
        global $wpdb;

        $since = gmdate('Y-m-d H:i:s', time() - ($windowMinutes * 60));

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}hdm_otp_codes
             WHERE  mobile     = %s
             AND    created_at >= %s",
            $mobile,
            $since
        ));
    }

    /**
     * Check whether a mobile is currently rate-limited.
     *
     * Default policy: max 3 OTP requests per 5-minute window.
     *
     * @param  string $mobile
     * @param  int    $maxRequests
     * @param  int    $windowMinutes
     * @return bool   true = rate-limited (deny the request)
     */
    public static function isRateLimited(
        string $mobile,
        int    $maxRequests    = 3,
        int    $windowMinutes  = 5
    ): bool {
        return self::countRecentRequests($mobile, $windowMinutes) >= $maxRequests;
    }

    // ------------------------------------------------------------------ //
    //  Private helpers
    // ------------------------------------------------------------------ //

    /**
     * Return the client IP address.
     * Respects X-Forwarded-For for load-balanced / proxied setups.
     *
     * @return string
     */
    private static function getClientIp(): string
    {
        // Prioritise X-Forwarded-For (first IP in the chain)
        if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip  = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }
}