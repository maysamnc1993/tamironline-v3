<?php

namespace Tamir\Rest\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\RequestValidator;
use Rehub\Rest\Response;
use WP_REST_Request;

// ══════════════════════════════════════════════════════════════
//  AddressController
//  آدرس‌های کاربر در user_meta با کلید tamir_addresses ذخیره می‌شوند
// ══════════════════════════════════════════════════════════════

final class AddressController extends Controller
{
    private const META_KEY = 'tamir_addresses';

    public function authorize(WP_REST_Request $r): bool
    {
        return current_user_can('customer') || current_user_can('administrator');
    }

    public function rules(): array  { return []; }
    public function messages(): array { return []; }

    // GET /addresses
    public function index(WP_REST_Request $r)
    {
        $userId    = get_current_user_id();
        $addresses = $this->getAddresses($userId);

        return Response::ok([
            'success' => true,
            'data'    => array_values($addresses),
        ]);
    }

    // POST /addresses
    public function store(WP_REST_Request $r)
    {
        $validator = new RequestValidator(
            [
                'full_address' => ['required', 'string', 'min:5', 'max:300'],
                'city'         => ['required', 'string', 'min:2', 'max:60'],
                'province'     => ['required', 'string', 'min:2', 'max:60'],
                'title'        => ['sometimes', 'string', 'min:1', 'max:60'],
                'postal_code'  => ['sometimes', 'string', 'regex:/^\d{10}$/'],
            ],
            [
                'full_address.required' => 'آدرس کامل الزامی است.',
                'city.required'         => 'شهر الزامی است.',
                'province.required'     => 'استان الزامی است.',
                'postal_code.regex'     => 'کد پستی باید ۱۰ رقم باشد.',
            ]
        );

        if (! $validator->passes($r)) {
            return Response::error('اطلاعات نامعتبر است.', 422, [
                'errors' => $validator->toErrorResponse(),
            ]);
        }

        $userId    = get_current_user_id();
        $addresses = $this->getAddresses($userId);

        $newAddress = [
            'id'           => uniqid('addr_', false),
            'title'        => sanitize_text_field($r->get_param('title') ?? 'خانه'),
            'full_address' => sanitize_textarea_field($r->get_param('full_address')),
            'city'         => sanitize_text_field($r->get_param('city')),
            'province'     => sanitize_text_field($r->get_param('province')),
            'postal_code'  => sanitize_text_field($r->get_param('postal_code') ?? ''),
            'lat'          => (float) ($r->get_param('lat') ?? 0),
            'lng'          => (float) ($r->get_param('lng') ?? 0),
            'is_default'   => empty($addresses), // اولین آدرس پیش‌فرض است
        ];

        $addresses[] = $newAddress;
        update_user_meta($userId, self::META_KEY, $addresses);

        return Response::ok([
            'success' => true,
            'data'    => $newAddress,
        ], 201);
    }

    // PUT /addresses/{id}
    public function update(WP_REST_Request $r)
    {
        $userId    = get_current_user_id();
        $addrId    = sanitize_text_field($r->get_param('id'));
        $addresses = $this->getAddresses($userId);

        $found = false;
        foreach ($addresses as &$addr) {
            if ($addr['id'] === $addrId) {
                if ($r->get_param('title'))        $addr['title']        = sanitize_text_field($r->get_param('title'));
                if ($r->get_param('full_address'))  $addr['full_address'] = sanitize_textarea_field($r->get_param('full_address'));
                if ($r->get_param('city'))          $addr['city']         = sanitize_text_field($r->get_param('city'));
                if ($r->get_param('province'))      $addr['province']     = sanitize_text_field($r->get_param('province'));
                if ($r->get_param('postal_code'))   $addr['postal_code']  = sanitize_text_field($r->get_param('postal_code'));
                $found = $addr;
                break;
            }
        }
        unset($addr);

        if (! $found) {
            return Response::error('آدرس یافت نشد.', 404);
        }

        update_user_meta($userId, self::META_KEY, $addresses);

        return Response::ok([
            'success' => true,
            'data'    => $found,
        ]);
    }

    // DELETE /addresses/{id}
    public function destroy(WP_REST_Request $r)
    {
        $userId    = get_current_user_id();
        $addrId    = sanitize_text_field($r->get_param('id'));
        $addresses = $this->getAddresses($userId);

        $filtered = array_values(array_filter($addresses, fn($a) => $a['id'] !== $addrId));

        if (count($filtered) === count($addresses)) {
            return Response::error('آدرس یافت نشد.', 404);
        }

        update_user_meta($userId, self::META_KEY, $filtered);

        return Response::ok([
            'success' => true,
            'message' => 'آدرس حذف شد.',
        ]);
    }

    private function getAddresses(int $userId): array
    {
        return (array) (get_user_meta($userId, self::META_KEY, true) ?: []);
    }
}


// ══════════════════════════════════════════════════════════════
//  ProfileController
// ══════════════════════════════════════════════════════════════

final class ProfileController extends Controller
{
    public function authorize(WP_REST_Request $r): bool
    {
        return current_user_can('customer') || current_user_can('administrator');
    }

    public function rules(): array  { return []; }
    public function messages(): array { return []; }

    // GET /profile
    public function show(WP_REST_Request $r)
    {
        $userId = get_current_user_id();
        $user   = get_userdata($userId);

        if (! $user) {
            return Response::error('کاربر یافت نشد.', 404);
        }

        return Response::ok([
            'success' => true,
            'data'    => $this->formatProfile($user),
        ]);
    }

    // PUT /profile
    public function update(WP_REST_Request $r)
    {
        $validator = new RequestValidator(
            [
                'name'  => ['sometimes', 'string', 'min:2', 'max:60'],
                'email' => ['sometimes', 'string'],
            ],
            [
                'name.min'  => 'نام باید حداقل ۲ کاراکتر باشد.',
                'name.max'  => 'نام نباید بیشتر از ۶۰ کاراکتر باشد.',
            ]
        );

        if (! $validator->passes($r)) {
            return Response::error('اطلاعات نامعتبر است.', 422, [
                'errors' => $validator->toErrorResponse(),
            ]);
        }

        $userId = get_current_user_id();
        $data   = ['ID' => $userId];

        $name  = sanitize_text_field($r->get_param('name') ?? '');
        $email = sanitize_email($r->get_param('email') ?? '');

        if ($name) {
            $data['display_name'] = $name;
            $data['first_name']   = $name;
            update_user_meta($userId, 'first_name', $name);
        }

        if ($email && is_email($email)) {
            // بررسی تکراری نبودن email
            $existing = get_user_by('email', $email);
            if ($existing && $existing->ID !== $userId) {
                return Response::error('این آدرس ایمیل قبلاً استفاده شده است.', 409);
            }
            $data['user_email'] = $email;
        }

        $result = wp_update_user($data);

        if (is_wp_error($result)) {
            return Response::error('خطا در بروزرسانی پروفایل.', 500);
        }

        return Response::ok([
            'success' => true,
            'message' => 'پروفایل با موفقیت بروزرسانی شد.',
            'data'    => $this->formatProfile(get_userdata($userId)),
        ]);
    }

    private function formatProfile(\WP_User $user): array
    {
        // آمار سفارش‌ها
        $total = (int) (new \WP_Query([
            'post_type'      => 'tamir_order',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [['key' => '_tamir_customer_id', 'value' => $user->ID, 'type' => 'NUMERIC']],
        ]))->found_posts;

        $completed = (int) (new \WP_Query([
            'post_type'      => 'tamir_order',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                ['key' => '_tamir_customer_id', 'value' => $user->ID, 'type' => 'NUMERIC'],
                ['key' => '_tamir_status',      'value' => 'completed'],
            ],
        ]))->found_posts;

        return [
            'id'              => $user->ID,
            'name'            => $user->display_name,
            'phone'           => get_user_meta($user->ID, 'tamir_phone', true),
            'email'           => strpos($user->user_email, '@tamir.local') === false ? $user->user_email : null,
            'avatar'          => get_avatar_url($user->ID, ['size' => 192]),
            'orders_count'    => $total,
            'completed_count' => $completed,
            'pending_count'   => $total - $completed,
            'registered_at'   => $user->user_registered,
        ];
    }
}


// ══════════════════════════════════════════════════════════════
//  NotificationController
//  اعلان‌ها در user_meta با کلید tamir_notifications ذخیره می‌شوند
// ══════════════════════════════════════════════════════════════

final class NotificationController extends Controller
{
    private const META_KEY = 'tamir_notifications';

    public function authorize(WP_REST_Request $r): bool
    {
        return current_user_can('customer') || current_user_can('administrator');
    }

    public function rules(): array  { return []; }
    public function messages(): array { return []; }

    // GET /notifications
    public function index(WP_REST_Request $r)
    {
        $userId        = get_current_user_id();
        $notifications = $this->getNotifications($userId);

        // جدیدترین اول
        $sorted = array_reverse($notifications);

        return Response::ok([
            'success'      => true,
            'data'         => array_values($sorted),
            'unread_count' => count(array_filter($sorted, fn($n) => ! $n['is_read'])),
        ]);
    }

    // POST /notifications/{id}/read
    public function markRead(WP_REST_Request $r)
    {
        $userId        = get_current_user_id();
        $notifId       = $r->get_param('id');
        $notifications = $this->getNotifications($userId);

        $found = false;
        foreach ($notifications as &$notif) {
            if ((string) $notif['id'] === (string) $notifId) {
                $notif['is_read'] = true;
                $found = true;
                break;
            }
        }
        unset($notif);

        if (! $found) {
            return Response::error('اعلان یافت نشد.', 404);
        }

        update_user_meta($userId, self::META_KEY, $notifications);

        return Response::ok([
            'success' => true,
            'message' => 'اعلان خوانده شد.',
        ]);
    }

    // POST /notifications/read-all
    public function markAllRead(WP_REST_Request $r)
    {
        $userId        = get_current_user_id();
        $notifications = $this->getNotifications($userId);

        $notifications = array_map(function ($n) {
            $n['is_read'] = true;
            return $n;
        }, $notifications);

        update_user_meta($userId, self::META_KEY, $notifications);

        return Response::ok([
            'success' => true,
            'message' => 'همه اعلان‌ها خوانده شدند.',
        ]);
    }

    private function getNotifications(int $userId): array
    {
        return (array) (get_user_meta($userId, self::META_KEY, true) ?: []);
    }
}
