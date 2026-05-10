<?php

namespace Tamir\Rest\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\RequestValidator;
use Rehub\Rest\Response;
use WP_REST_Request;

use Tamir\Notification\NotificationService;
use Tamir\Notification\NotificationTemplates;
use Tamir\Rest\Traits\JwtAuthTrait; // 🆕

// ══════════════════════════════════════════════════════════════
//  NotificationController
//  اعلان‌ها در user_meta با کلید tamir_notifications ذخیره می‌شوند
// ══════════════════════════════════════════════════════════════

/**
 * NotificationController — REST API
 *
 * GET  /tamir/v1/notifications          لیست اعلان‌ها
 * POST /tamir/v1/notifications/{id}/read خواندن یک اعلان
 * POST /tamir/v1/notifications/read-all  خواندن همه
 *
 * 🆕 تغییرات این نسخه:
 *  - استفاده از JwtAuthTrait
 *  - حذف const JWT_META_KEY
 *  - حذف private currentUserId()
 */
final class NotificationController extends Controller
{
    use JwtAuthTrait; // 🆕

    private $service;

    public function __construct()
    {
        $this->service = new NotificationService();
    }

    public function authorize(WP_REST_Request $r): bool
    {
        return $this->currentUserId($r) > 0;
    }

    public function rules(): array
    {
        return [];
    }
    public function messages(): array
    {
        return [];
    }

    // GET /notifications
    public function index(WP_REST_Request $r)
    {
        $userId  = $this->currentUserId($r);
        $page    = max(1, (int)($r->get_param('page') ?? 1));
        $perPage = min(50, max(1, (int)($r->get_param('per_page') ?? 20)));

        $result = $this->service->getUserNotifications($userId, $page, $perPage);

        return Response::ok(array(
            'success' => true,
            'data'    => $result['data'],
            'meta'    => array(
                'total'   => $result['total'],
                'pages'   => $result['pages'],
                'page'    => $page,
                'unread'  => $this->countUnread($userId),
            ),
        ));
    }

    // POST /notifications/{id}/read
    public function markRead(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        $postId = (int)($r->get_param('id') ?? 0);

        if (!$postId) {
            return Response::error('شناسه اعلان نامعتبر است.', 400);
        }

        $ok = $this->service->markRead($postId, $userId);
        if (!$ok) {
            return Response::error('اعلان یافت نشد یا دسترسی ممنوع.', 404);
        }

        return Response::ok(array('success' => true, 'message' => 'اعلان خوانده شد.'));
    }

    // POST /notifications/read-all
    public function markAllRead(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        $count  = $this->service->markAllRead($userId);

        return Response::ok(array(
            'success' => true,
            'message' => $count . ' اعلان خوانده شد.',
            'count'   => $count,
        ));
    }

    private function countUnread(int $userId): int
    {
        $posts = get_posts(array(
            'post_type'      => NotificationService::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array('key' => 'user_id', 'value' => $userId, 'compare' => '=', 'type' => 'NUMERIC'),
                array('key' => 'is_read', 'value' => 0,       'compare' => '=', 'type' => 'NUMERIC'),
                array('key' => 'channel', 'value' => array('in_app', 'all'), 'compare' => 'IN'),
            ),
        ));
        return count($posts);
    }

    // 🗑️ private function currentUserId() حذف شد — از JwtAuthTrait میاد
}
