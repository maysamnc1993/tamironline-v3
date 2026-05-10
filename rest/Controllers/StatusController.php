<?php

/**
 * StatusController — endpoint عمومی وضعیت اپ
 * ───────────────────────────────────────────────────────────
 *  GET /tamir/v1/status
 *  GET /tamir/v1/status?version=1.0.0
 *
 *  Public endpoint (بدون JWT) — چون کاربران قبل از login هم
 *  نیاز دارند این رو چک کنن.
 *
 *  Cache کوتاه (۳۰ ثانیه) — چون kill switch باید سریع عمل کند.
 * ───────────────────────────────────────────────────────────
 */

namespace Tamir\AppControl\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\Response;
use WP_REST_Request;
use Tamir\AppControl\AppStatusService;

defined('ABSPATH') || exit;

final class StatusController extends Controller
{
    public function authorize(WP_REST_Request $r): bool
    {
        return true;  // public
    }

    public function rules(): array
    {
        return [];
    }
    public function messages(): array
    {
        return [];
    }

    /**
     * GET /tamir/v1/status
     */
    public function index(WP_REST_Request $r)
    {
        $clientVersion = sanitize_text_field((string) ($r->get_param('version') ?: ''));

        $service = new AppStatusService();
        $status  = $service->getStatus($clientVersion !== '' ? $clientVersion : null);

        return Response::ok([
            'success' => true,
            'data'    => $status,
        ]);
    }
}
