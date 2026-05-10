<?php

/**
 * AppStatusService — منطق stateless
 * ───────────────────────────────────────────────────────────
 *  خوندن ACF options و ساخت response.
 *
 *  ⭐ بنرها بر اساس placement گروه‌بندی می‌شن.
 *  response['banners'] = {
 *    home_top: [{image, title, link, order}, ...],
 *    home_promotions: [...],
 *    orders_list: [...],
 *    profile: [...],
 *    login: [{image, title, link, order, accent, caption}, ...]
 *  }
 *
 *  🆕 فیلدهای accent و caption برای login اضافه شدند.
 *      در سایر placements، این فیلدها برگردانده می‌شوند ولی معمولاً
 *      خالی هستند (در ادمین فقط برای placement=login نمایش داده می‌شوند).
 * ───────────────────────────────────────────────────────────
 */

namespace Tamir\AppControl;

defined('ABSPATH') || exit;

final class AppStatusService
{
    /**
     * دریافت وضعیت کامل اپ برای فرانت.
     */
    public function getStatus(?string $clientVersion = null): array
    {
        $active        = $this->getOption('active', true);
        $message       = $this->getOption('message', '');
        $minVersion    = (string) $this->getOption('min_version', '');
        $updateUrl     = (string) $this->getOption('update_url', '');
        $forceReauthAt = $this->getOption('force_reauth_at', 0);

        $features = [
            'new_orders'    => (bool) $this->getOption('feat_new_orders', true),
            'cancel_orders' => (bool) $this->getOption('feat_cancel_orders', true),
            'support_chat'  => (bool) $this->getOption('feat_support_chat', true),
        ];

        // Top notification banner
        $bannerEnabled = (bool) $this->getOption('banner_enabled', false);
        $banner = null;
        if ($bannerEnabled) {
            $text = trim((string) $this->getOption('banner_text', ''));
            if ($text !== '') {
                $banner = [
                    'type'        => (string) $this->getOption('banner_type', 'info'),
                    'text'        => $text,
                    'dismissible' => (bool) $this->getOption('banner_dismissible', true),
                ];
            }
        }

        // Version check
        $versionOk = true;
        if ($minVersion !== '' && $clientVersion !== null && $clientVersion !== '') {
            $versionOk = version_compare($clientVersion, $minVersion, '>=');
        }

        // ⭐ Slider Banners — گروه‌بندی بر اساس placement
        $banners = $this->getBanners();

        return [
            'active'             => (bool) $active,
            'message'            => (string) $message,
            'min_version'        => $minVersion,
            'update_url'         => $updateUrl,
            'client_version_ok'  => $versionOk,
            'force_reauth_at'    => (int) $forceReauthAt,
            'features'           => $features,
            'banner'             => $banner,
            'banners'            => $banners,
            'server_time'        => time(),
        ];
    }

    /**
     * 🆕 Public: getBanners برای استفاده‌ی مستقیم از خارج کلاس.
     * ServiceController از این برای endpoint /services/banners استفاده می‌کند.
     *
     * @return array<string, array<int, array{image:string, title:string, link:string, order:int, accent:string, caption:string}>>
     */
    public function getAllBanners(): array
    {
        return $this->getBanners();
    }

    /**
     * دریافت بنرها — فقط فعال‌ها، گروه‌بندی شده بر اساس placement
     *
     * @return array<string, array<int, array{image:string, title:string, link:string, order:int, accent:string, caption:string}>>
     */
    private function getBanners(): array
    {
        // همه placement های ممکن رو initialize کنیم (حتی اگر خالی)
        $result = [];
        foreach (array_keys(AcfFields::getPlacements()) as $placement) {
            $result[$placement] = [];
        }

        // ACF repeater رو بگیریم
        $raw = $this->getOption('banners', []);
        if (!is_array($raw) || empty($raw)) {
            return $result;
        }

        foreach ($raw as $row) {
            if (!is_array($row)) continue;

            // فقط فعال‌ها
            $active = isset($row['active']) ? (bool) $row['active'] : false;
            if (!$active) continue;

            // ── تصویر ────────────────────────────────────────
            $imageUrl = '';
            if (isset($row['image'])) {
                if (is_array($row['image']) && !empty($row['image']['url'])) {
                    if (!empty($row['image']['sizes']['large'])) {
                        $imageUrl = (string) $row['image']['sizes']['large'];
                    } else {
                        $imageUrl = (string) $row['image']['url'];
                    }
                } elseif (is_string($row['image'])) {
                    $imageUrl = $row['image'];
                } elseif (is_numeric($row['image'])) {
                    $src = wp_get_attachment_image_src((int) $row['image'], 'large');
                    $imageUrl = $src ? (string) $src[0] : '';
                }
            }

            if ($imageUrl === '') continue;

            $placement = (string) ($row['placement'] ?? 'home_top');
            // اگر placement ناشناخته بود، بنر رو نادیده بگیر
            if (!isset($result[$placement])) continue;

            $result[$placement][] = [
                'image'   => $imageUrl,
                'title'   => (string) ($row['title']   ?? ''),
                'link'    => (string) ($row['link']    ?? ''),
                'order'   => (int)    ($row['order']   ?? 10),
                // 🆕 فیلدهای overlay (فقط برای login پر می‌شوند، در بقیه خالی)
                'accent'  => (string) ($row['accent']  ?? ''),
                'caption' => (string) ($row['caption'] ?? ''),
            ];
        }

        // مرتب‌سازی هر placement بر اساس order
        foreach ($result as $placement => &$items) {
            usort($items, function ($a, $b) {
                return ($a['order'] ?? 10) <=> ($b['order'] ?? 10);
            });
        }
        unset($items);

        return $result;
    }

    /**
     * چک سریع JWT
     */
    public function isJwtStillValid(int $jwtIat): bool
    {
        $forceReauthAt = (int) $this->getOption('force_reauth_at', 0);
        if ($forceReauthAt <= 0) return true;
        return $jwtIat >= $forceReauthAt;
    }

    public function isFeatureEnabled(string $name): bool
    {
        $key = strpos($name, 'feat_') === 0 ? $name : ('feat_' . $name);
        return (bool) $this->getOption($key, true);
    }

    public function isActive(): bool
    {
        return (bool) $this->getOption('active', true);
    }

    /**
     * خواندن option با ACF یا fallback به get_option
     */
    private function getOption(string $key, $default = null)
    {
        $fullKey = AcfFields::KEY_PREFIX . $key;

        if (function_exists('get_field')) {
            $val = get_field($fullKey, 'option');

            // برای array/repeater: اگر خالی بود، default برگردان
            if (is_array($default) && !is_array($val)) {
                return $default;
            }

            if ($val !== null && $val !== false && $val !== '') {
                return $val;
            }

            if (is_bool($default) && $val !== null) {
                return (bool) $val;
            }
        }

        return get_option($fullKey, $default);
    }
}
