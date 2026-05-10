<?php

namespace Tamir\Rest\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\Response;
use Tamir\AppControl\AcfFields;
use Tamir\Admin\ListManager;
use WP_REST_Request;

/**
 * ServiceController v3 — PHP 7 Compatible
 *
 * 🆕 تغییرات این نسخه:
 *   - متد cancelReasons() از ListManager استفاده می‌کنه (به جای get_option('cancel'))
 *   - get_option('cancel') قدیمی **دست‌نخورده** برای CRM ادمین
 */
class ServiceController extends Controller
{
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

    /**
     * Helper: ساخت badge array از term_meta
     */
    private function buildBadge($termId)
    {
        $text = get_term_meta($termId, 'tamir_badge_text', true);
        if (empty($text)) {
            return null;
        }

        $bg    = get_term_meta($termId, 'tamir_badge_bg',    true) ?: '#fbbf24';
        $color = get_term_meta($termId, 'tamir_badge_color', true) ?: '#000000';

        return array(
            'text'  => (string) $text,
            'bg'    => (string) $bg,
            'color' => (string) $color,
        );
    }
    // ══════════════════════════════════════════════════════════
    //  GET /tamir/v1/services/categories
    // ══════════════════════════════════════════════════════════
    public function categories(WP_REST_Request $r)
    {
        $terms = get_terms(array(
            'taxonomy'   => 'device',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'number'     => 0,

            // فقط دستگاه‌های فعال
            // دستگاه‌های قدیمی که meta ندارند هم فعال حساب می‌شوند
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'tamir_is_active',
                    'value'   => '1',
                    'compare' => '=',
                ),
                array(
                    'key'     => 'tamir_is_active',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        ));

        if (is_wp_error($terms) || empty($terms)) {
            return Response::ok(array(
                'success' => true,
                'data'    => array(),
                'message' => 'هیچ دستگاه فعالی ثبت نشده.',
            ));
        }

        usort($terms, function ($a, $b) {
            $orderA = get_term_meta($a->term_id, 'tamir_order', true);
            $orderB = get_term_meta($b->term_id, 'tamir_order', true);

            $orderA = ($orderA !== '' && $orderA !== null) ? absint($orderA) : 999999;
            $orderB = ($orderB !== '' && $orderB !== null) ? absint($orderB) : 999999;

            if ($orderA === $orderB) {
                return strcmp($a->name, $b->name);
            }

            return $orderA <=> $orderB;
        });

        $data = array();

        foreach ($terms as $term) {
            $order = get_term_meta($term->term_id, 'tamir_order', true);

            $isActive = get_term_meta($term->term_id, 'tamir_is_active', true);
            $isActive = ($isActive === '' || $isActive === null) ? 1 : absint($isActive);

            $data[] = array(
                'id'        => $term->term_id,
                'name'      => $term->name,
                'slug'      => $term->slug,
                'order'     => ($order !== '' && $order !== null) ? absint($order) : 0,
                'icon'      => get_term_meta($term->term_id, 'tamir_icon',  true) ?: '',
                'image'     => get_term_meta($term->term_id, 'tamir_image', true) ?: '',
                'badge'     => $this->buildBadge($term->term_id),

                // اختیاری؛ اگر نمی‌خواهی در خروجی API باشد، این خط را حذف کن
                'is_active' => $isActive,
            );
        }

        return Response::ok(array(
            'success' => true,
            'data'    => $data,
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  GET /tamir/v1/services/brands?category_id=X
    // ══════════════════════════════════════════════════════════
    public function brands(WP_REST_Request $r)
    {
        $categoryId = absint($r->get_param('category_id'));

        $args = array(
            'taxonomy'   => 'brand',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'number'     => 0,
        );

        if ($categoryId > 0) {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key'     => 'tamir_device_ids',
                    'value'   => '"' . $categoryId . '"',
                    'compare' => 'LIKE',
                ),
                array(
                    'key'     => 'tamir_device_ids',
                    'compare' => 'NOT EXISTS',
                ),
            );
        }

        $terms = get_terms($args);

        if (is_wp_error($terms) || empty($terms)) {
            if ($categoryId > 0) {
                $terms = get_terms(array(
                    'taxonomy'   => 'brand',
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                    'number'     => 0,
                ));
            }
            if (is_wp_error($terms) || empty($terms)) {
                return Response::ok(array(
                    'success' => true,
                    'data'    => array(),
                    'message' => 'هیچ برندی ثبت نشده.',
                ));
            }
        }

        $data = array();
        foreach ($terms as $term) {
            $deviceIds = get_term_meta($term->term_id, 'tamir_device_ids', true);
            if (!empty($deviceIds) && $categoryId > 0) {
                if (!in_array($categoryId, (array) $deviceIds, true)) {
                    continue;
                }
            }
            $data[] = array(
                'id'    => $term->term_id,
                'name'  => $term->name,
                'slug'  => $term->slug,
                'icon'  => get_term_meta($term->term_id, 'tamir_icon',  true) ?: '',
                'image' => get_term_meta($term->term_id, 'tamir_image', true) ?: '',
                'badge' => $this->buildBadge($term->term_id),
            );
        }

        if (empty($data)) {
            $data = array();
            foreach ($terms as $term) {
                $data[] = array(
                    'id'    => $term->term_id,
                    'name'  => $term->name,
                    'slug'  => $term->slug,
                    'icon'  => get_term_meta($term->term_id, 'tamir_icon',  true) ?: '',
                    'image' => get_term_meta($term->term_id, 'tamir_image', true) ?: '',
                    'badge' => $this->buildBadge($term->term_id),
                );
            }
        }

        return Response::ok(array(
            'success' => true,
            'data'    => $data,
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  GET /tamir/v1/services/objections
    // ══════════════════════════════════════════════════════════
    public function objections(WP_REST_Request $r)
    {
        $list = get_option('objectionsList', array());

        $items = array_values(array_filter(
            (array) $list,
            function ($v) {
                return !empty(trim((string) $v));
            }
        ));

        return Response::ok(array(
            'success' => true,
            'data'    => $items,
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  GET /tamir/v1/orders/cancel-reasons
    //
    //  🆕 از ListManager (admin-managed) خوانده می‌شود.
    //
    //  پاسخ شامل id و label است (snapshot در سفارش ذخیره می‌شه).
    //
    //  ⚠️ get_option('cancel') قدیمی **دست‌نخورده** باقی می‌ماند —
    //     فقط برای CRM ادمین استفاده می‌شه.
    // ══════════════════════════════════════════════════════════
    public function cancelReasons(\WP_REST_Request $r)
    {
        $items = ListManager::getAll('cancel_reasons', true);

        // فقط id و label به فرانت می‌فرستیم
        $payload = array_map(function ($it) {
            return array(
                'id'    => $it['id'],
                'label' => $it['label'],
            );
        }, $items);

        return Response::ok(array(
            'success' => true,
            'data'    => $payload,
        ));
    }

    // ══════════════════════════════════════════════════════════
    //  GET /tamir/v1/services/banners
    // ══════════════════════════════════════════════════════════
    public function banners(WP_REST_Request $r)
    {
        $placement = sanitize_text_field((string) ($r->get_param('placement') ?: ''));
        $all = $this->loadAllBanners();

        if ($placement !== '') {
            $list = isset($all[$placement]) ? $all[$placement] : array();
            return Response::ok(array(
                'success' => true,
                'data'    => $list,
            ));
        }

        return Response::ok(array(
            'success' => true,
            'data'    => $all,
        ));
    }

    private function loadAllBanners(): array
    {
        $result = array();

        if (class_exists('\Tamir\AppControl\AcfFields')) {
            foreach (array_keys(AcfFields::getPlacements()) as $placement) {
                $result[$placement] = array();
            }
        } else {
            $result = array(
                'home_top'        => array(),
                'home_promotions' => array(),
                'orders_list'     => array(),
                'profile'         => array(),
            );
        }

        $raw = null;
        if (function_exists('get_field')) {
            $raw = get_field('tamir_app_banners', 'option');
        }

        if (empty($raw)) {
            $raw = get_option('tamir_app_banners', array());
        }

        if (!is_array($raw) || empty($raw)) {
            return $result;
        }

        foreach ($raw as $row) {
            if (!is_array($row)) continue;

            $active = isset($row['active']) ? (bool) $row['active'] : false;
            if (!$active) continue;

            $imageUrl = $this->extractImageUrl($row['image'] ?? null);
            if ($imageUrl === '') continue;

            $placement = isset($row['placement']) ? (string) $row['placement'] : 'home_top';

            if (!isset($result[$placement])) {
                continue;
            }

            $result[$placement][] = array(
                'image' => $imageUrl,
                'title' => (string) (isset($row['title']) ? $row['title'] : ''),
                'link'  => (string) (isset($row['link'])  ? $row['link']  : ''),
                'order' => (int)    (isset($row['order']) ? $row['order'] : 10),
            );
        }

        foreach ($result as $placement => $items) {
            usort($items, function ($a, $b) {
                $aOrder = isset($a['order']) ? (int) $a['order'] : 10;
                $bOrder = isset($b['order']) ? (int) $b['order'] : 10;
                if ($aOrder === $bOrder) return 0;
                return ($aOrder < $bOrder) ? -1 : 1;
            });
            $result[$placement] = $items;
        }

        return $result;
    }

    private function extractImageUrl($image): string
    {
        if (empty($image)) return '';

        if (is_array($image)) {
            if (!empty($image['sizes']) && !empty($image['sizes']['large'])) {
                return (string) $image['sizes']['large'];
            }
            if (!empty($image['url'])) {
                return (string) $image['url'];
            }
            return '';
        }

        if (is_string($image)) {
            return $image;
        }

        if (is_numeric($image)) {
            $src = wp_get_attachment_image_src((int) $image, 'large');
            return $src ? (string) $src[0] : '';
        }

        return '';
    }
}
