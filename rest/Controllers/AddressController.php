<?php

namespace Tamir\Rest\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\Response;
use WP_REST_Request;
use Tamir\Rest\Traits\JwtAuthTrait; // 🆕

/**
 * AddressController v5
 *
 * 🆕 تغییرات این نسخه:
 *  - استفاده از JwtAuthTrait برای احراز هویت
 *  - حذف const JWT_META_KEY
 *  - حذف private currentUserId()
 *
 * بقیه‌ی منطق (storage در wp_options، migration از user_meta) دست‌نخورده.
 */
final class AddressController extends Controller
{
    use JwtAuthTrait; // 🆕

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

    // ══ GET /addresses ════════════════════════════════════════
    public function index(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);
        return Response::ok([
            'success' => true,
            'data'    => $this->load($userId),
        ]);
    }

    // ══ POST /addresses ═══════════════════════════════════════
    public function store(WP_REST_Request $r)
    {
        $userId = $this->currentUserId($r);

        $fullAddress = trim(sanitize_textarea_field($r->get_param('full_address') ?? ''));
        $province    = trim(sanitize_text_field($r->get_param('province') ?? ''));

        if (strlen($fullAddress) < 5) {
            return Response::error('آدرس کامل الزامی است (حداقل ۵ کاراکتر).', 422);
        }
        if (strlen($province) < 2) {
            return Response::error('استان الزامی است.', 422);
        }

        $addresses = $this->load($userId);
        $isDefault = filter_var($r->get_param('is_default') ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($isDefault) {
            foreach ($addresses as &$a) {
                $a['is_default'] = false;
            }
            unset($a);
        }

        $new = [
            'id'            => uniqid('addr_', false),
            'title'         => sanitize_text_field($r->get_param('title') ?: 'خانه'),
            'full_address'  => $fullAddress,
            'city'          => sanitize_text_field($r->get_param('city')     ?: ''),
            'province'      => $province,
            'city_term_id'  => (int)($r->get_param('city_term_id')  ?: 0),
            'state_term_id' => (int)($r->get_param('state_term_id') ?: 0),
            'phone'         => sanitize_text_field($r->get_param('phone')    ?: ''),
            'postal_code'   => sanitize_text_field($r->get_param('postal_code') ?: ''),
            'lat'           => (float)($r->get_param('lat') ?: 0),
            'lng'           => (float)($r->get_param('lng') ?: 0),
            'is_default'    => $isDefault || empty($addresses),
            'created_at'    => current_time('mysql'),
        ];

        $addresses[] = $new;
        $this->save($userId, $addresses);

        return Response::ok(['success' => true, 'data' => $new], 201);
    }

    // ══ PUT /addresses/{id} ═══════════════════════════════════
    public function update(WP_REST_Request $r)
    {
        $userId    = $this->currentUserId($r);
        $addrId    = sanitize_text_field($r->get_param('id') ?? '');
        $addresses = $this->load($userId);

        $found = null;
        foreach ($addresses as &$addr) {
            if ($addr['id'] === $addrId) {
                if ($r->get_param('title'))         $addr['title']        = sanitize_text_field($r->get_param('title'));
                if ($r->get_param('full_address'))  $addr['full_address'] = sanitize_textarea_field($r->get_param('full_address'));
                if ($r->get_param('city'))          $addr['city']         = sanitize_text_field($r->get_param('city'));
                if ($r->get_param('province'))      $addr['province']     = sanitize_text_field($r->get_param('province'));
                if ($r->get_param('is_default'))    $addr['is_default']   = true;
                $found = $addr;
                break;
            }
        }
        unset($addr);

        if (!$found) return Response::error('آدرس یافت نشد.', 404);

        $this->save($userId, $addresses);
        return Response::ok(['success' => true, 'data' => $found]);
    }

    // ══ DELETE /addresses/{id} ════════════════════════════════
    public function destroy(WP_REST_Request $r)
    {
        $userId    = $this->currentUserId($r);
        $addrId    = sanitize_text_field($r->get_param('id') ?? '');
        $addresses = $this->load($userId);

        $filtered = array_values(array_filter($addresses, function ($a) use ($addrId) {
            return $a['id'] !== $addrId;
        }));

        if (count($filtered) === count($addresses)) {
            return Response::error('آدرس یافت نشد.', 404);
        }

        $this->save($userId, $filtered);
        return Response::ok(['success' => true, 'message' => 'آدرس حذف شد.']);
    }

    // 🗑️ private function currentUserId() حذف شد — از JwtAuthTrait میاد

    // ══ Storage — JSON در wp_options (دست‌نخورده) ══════════════

    private function optionKey(int $userId): string
    {
        return 'tamir_addr_' . $userId;
    }

    private function load(int $userId): array
    {
        $key  = $this->optionKey($userId);
        $json = get_option($key, '[]');

        if (!is_string($json) || $json === '') return [];

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) return [];

        if (empty($decoded)) {
            $old = $this->migrateFromUserMeta($userId);
            if (!empty($old)) {
                $this->save($userId, $old);
                return $old;
            }
        }

        return $decoded;
    }

    private function save(int $userId, array $addresses): void
    {
        $key  = $this->optionKey($userId);
        $json = wp_json_encode(array_values($addresses), JSON_UNESCAPED_UNICODE);
        update_option($key, $json, false);
    }

    private function migrateFromUserMeta(int $userId): array
    {
        $raw = get_user_meta($userId, 'tamir_addresses', true);
        if (empty($raw)) return [];

        if (is_string($raw)) {
            $raw = maybe_unserialize($raw);
        }
        if (!is_array($raw)) return [];

        if (isset($raw['id']) && is_string($raw['id'])) {
            return [$raw];
        }

        return array_values($raw);
    }
}
