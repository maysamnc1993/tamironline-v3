<?php
// ═══════════════════════════════════════════════════════════
//  wp-content/themes/weblino/admin/ListManager.php
//
//  کلاس generic برای مدیریت لیست‌های admin-managed
//  (cancel reasons, objections, introductions, ...)
//
//  Storage: tamir_list_{namespace} (JSON in wp_options)
//
//  Item structure:
//      { id, label, is_active, sort }
// ═══════════════════════════════════════════════════════════
namespace Tamir\Admin;

if (!defined('ABSPATH')) exit;

class ListManager
{
    private static function optionKey(string $namespace): string
    {
        return 'tamir_list_' . sanitize_key($namespace);
    }

    private static function generateId(): string
    {
        return substr(md5(uniqid('', true)), 0, 8);
    }

    public static function getAll(string $namespace, bool $activeOnly = false): array
    {
        $key = self::optionKey($namespace);
        $raw = get_option($key, '[]');

        if (is_array($raw)) {
            $items = $raw;
        } else {
            $items = json_decode((string) $raw, true);
            if (!is_array($items)) $items = array();
        }

        $items = array_values(array_filter(array_map(function ($item) {
            if (!is_array($item)) return null;
            return array(
                'id'        => isset($item['id']) ? (string) $item['id'] : '',
                'label'     => isset($item['label']) ? (string) $item['label'] : '',
                'is_active' => isset($item['is_active']) ? (bool) $item['is_active'] : true,
                'sort'      => isset($item['sort']) ? (int) $item['sort'] : 0,
            );
        }, $items), function ($item) {
            return $item !== null && $item['id'] !== '' && $item['label'] !== '';
        }));

        usort($items, function ($a, $b) {
            return $a['sort'] <=> $b['sort'];
        });

        if ($activeOnly) {
            $items = array_values(array_filter($items, function ($item) {
                return $item['is_active'];
            }));
        }

        return $items;
    }

    public static function find(string $namespace, string $id): ?array
    {
        $items = self::getAll($namespace);
        foreach ($items as $item) {
            if ($item['id'] === $id) return $item;
        }
        return null;
    }

    public static function add(string $namespace, string $label)
    {
        $label = trim($label);
        if ($label === '') {
            return new \WP_Error('empty_label', 'متن خالی است.');
        }
        if (mb_strlen($label) > 200) {
            return new \WP_Error('label_too_long', 'متن نباید بیش از ۲۰۰ کاراکتر باشد.');
        }

        $items = self::getAll($namespace);
        foreach ($items as $existing) {
            if (mb_strtolower($existing['label']) === mb_strtolower($label)) {
                return new \WP_Error('duplicate', 'این مورد قبلاً اضافه شده است.');
            }
        }

        $maxSort = 0;
        foreach ($items as $existing) {
            if ($existing['sort'] > $maxSort) $maxSort = $existing['sort'];
        }

        $newItem = array(
            'id'        => self::generateId(),
            'label'     => $label,
            'is_active' => true,
            'sort'      => $maxSort + 1,
        );

        $items[] = $newItem;
        self::save($namespace, $items);

        return $newItem;
    }

    public static function update(string $namespace, string $id, array $patch)
    {
        $items = self::getAll($namespace);
        $found = false;

        foreach ($items as &$item) {
            if ($item['id'] !== $id) continue;
            $found = true;

            if (isset($patch['label'])) {
                $newLabel = trim((string) $patch['label']);
                if ($newLabel === '') {
                    return new \WP_Error('empty_label', 'متن خالی است.');
                }
                if (mb_strlen($newLabel) > 200) {
                    return new \WP_Error('label_too_long', 'متن نباید بیش از ۲۰۰ کاراکتر باشد.');
                }
                foreach ($items as $other) {
                    if ($other['id'] !== $id && mb_strtolower($other['label']) === mb_strtolower($newLabel)) {
                        return new \WP_Error('duplicate', 'این مورد قبلاً وجود دارد.');
                    }
                }
                $item['label'] = $newLabel;
            }
            if (isset($patch['is_active'])) {
                $item['is_active'] = (bool) $patch['is_active'];
            }
            if (isset($patch['sort'])) {
                $item['sort'] = (int) $patch['sort'];
            }
            break;
        }
        unset($item);

        if (!$found) {
            return new \WP_Error('not_found', 'مورد یافت نشد.');
        }

        self::save($namespace, $items);
        return self::find($namespace, $id);
    }

    public static function delete(string $namespace, string $id): bool
    {
        $items = self::getAll($namespace);
        $newItems = array_values(array_filter($items, function ($item) use ($id) {
            return $item['id'] !== $id;
        }));

        if (count($newItems) === count($items)) return false;

        self::save($namespace, $newItems);
        return true;
    }

    public static function reorder(string $namespace, array $orderedIds): bool
    {
        $items = self::getAll($namespace);
        $itemsById = array();
        foreach ($items as $item) {
            $itemsById[$item['id']] = $item;
        }

        $newItems = array();
        $sort = 0;
        foreach ($orderedIds as $id) {
            $id = (string) $id;
            if (isset($itemsById[$id])) {
                $itemsById[$id]['sort'] = $sort++;
                $newItems[] = $itemsById[$id];
                unset($itemsById[$id]);
            }
        }
        foreach ($itemsById as $remaining) {
            $remaining['sort'] = $sort++;
            $newItems[] = $remaining;
        }

        self::save($namespace, $newItems);
        return true;
    }

    /**
     * Snapshot برای ذخیره در سفارش (id + label در زمان لغو)
     */
    public static function snapshot(string $namespace, string $id): ?array
    {
        $item = self::find($namespace, $id);
        if (!$item) return null;
        return array(
            'id'    => $item['id'],
            'label' => $item['label'],
        );
    }

    private static function save(string $namespace, array $items): void
    {
        $key = self::optionKey($namespace);
        $json = wp_json_encode(array_values($items), JSON_UNESCAPED_UNICODE);
        update_option($key, $json, false); // autoload=false
    }
}
