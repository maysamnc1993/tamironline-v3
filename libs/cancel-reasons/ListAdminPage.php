<?php
/**
 * ═══════════════════════════════════════════════════════════════
 *  ListAdminPage — صفحه‌ی wp-admin برای مدیریت لیست
 *
 *  Path: wp-content/themes/weblino/admin/ListAdminPage.php
 *
 *  استفاده:
 *    new ListAdminPage('cancel_reasons', 'دلایل لغو سفارش', 'tamir-cancel-reasons');
 *
 *  Features:
 *   - افزودن آیتم با AJAX
 *   - ویرایش inline
 *   - drag-drop sort
 *   - toggle is_active
 *   - حذف با confirm
 *   - بدون reload
 * ═══════════════════════════════════════════════════════════════
 */

namespace Tamir\Admin;

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/ListManager.php';

class ListAdminPage
{
    private $namespace;
    private $title;
    private $slug;
    private $capability;

    public function __construct($namespace, $title, $slug, $capability = 'manage_options')
    {
        $this->namespace  = $namespace;
        $this->title      = $title;
        $this->slug       = $slug;
        $this->capability = $capability;

        add_action('admin_menu', array($this, 'registerMenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueAssets'));
        add_action('rest_api_init', array($this, 'registerRestRoutes'));
    }

    // ─── Menu ──────────────────────────────────────────────────

    public function registerMenu()
    {
        add_menu_page(
            $this->title,
            $this->title,
            $this->capability,
            $this->slug,
            array($this, 'render'),
            'dashicons-list-view',
            58
        );
    }

    // ─── Assets ────────────────────────────────────────────────

    public function enqueueAssets($hook)
    {
        if (strpos($hook, $this->slug) === false) return;

        wp_enqueue_script('jquery-ui-sortable');
    }

    // ─── REST routes (admin-only) ──────────────────────────────

    public function registerRestRoutes()
    {
        $ns      = 'tamir-admin/v1';
        $base    = '/list/' . $this->namespace;
        $authCap = $this->capability;

        $authCallback = function () use ($authCap) {
            if (!current_user_can($authCap)) {
                return new \WP_Error('rest_forbidden', 'دسترسی ممنوع.', array('status' => 403));
            }
            return true;
        };

        register_rest_route($ns, $base, array(
            'methods'             => 'GET',
            'permission_callback' => $authCallback,
            'callback'            => function () {
                return rest_ensure_response(array(
                    'success' => true,
                    'data'    => ListManager::getAll($this->namespace),
                ));
            },
        ));

        register_rest_route($ns, $base, array(
            'methods'             => 'POST',
            'permission_callback' => $authCallback,
            'callback'            => function (\WP_REST_Request $r) {
                $label = (string) $r->get_param('label');
                $res = ListManager::add($this->namespace, $label);
                if (is_wp_error($res)) {
                    return new \WP_REST_Response(array(
                        'success' => false,
                        'message' => $res->get_error_message(),
                    ), 422);
                }
                return rest_ensure_response(array('success' => true, 'data' => $res));
            },
        ));

        register_rest_route($ns, $base . '/(?P<id>[a-zA-Z0-9]+)', array(
            'methods'             => 'PUT',
            'permission_callback' => $authCallback,
            'callback'            => function (\WP_REST_Request $r) {
                $id    = (string) $r->get_param('id');
                $patch = array();

                if ($r->get_param('label') !== null) {
                    $patch['label'] = (string) $r->get_param('label');
                }
                if ($r->get_param('is_active') !== null) {
                    $patch['is_active'] = filter_var($r->get_param('is_active'), FILTER_VALIDATE_BOOLEAN);
                }

                $res = ListManager::update($this->namespace, $id, $patch);
                if (is_wp_error($res)) {
                    $code = $res->get_error_code() === 'not_found' ? 404 : 422;
                    return new \WP_REST_Response(array(
                        'success' => false,
                        'message' => $res->get_error_message(),
                    ), $code);
                }
                return rest_ensure_response(array('success' => true, 'data' => $res));
            },
        ));

        register_rest_route($ns, $base . '/(?P<id>[a-zA-Z0-9]+)', array(
            'methods'             => 'DELETE',
            'permission_callback' => $authCallback,
            'callback'            => function (\WP_REST_Request $r) {
                $id  = (string) $r->get_param('id');
                $res = ListManager::delete($this->namespace, $id);
                if (is_wp_error($res)) {
                    return new \WP_REST_Response(array(
                        'success' => false,
                        'message' => $res->get_error_message(),
                    ), 404);
                }
                return rest_ensure_response(array('success' => true));
            },
        ));

        register_rest_route($ns, $base . '/reorder', array(
            'methods'             => 'POST',
            'permission_callback' => $authCallback,
            'callback'            => function (\WP_REST_Request $r) {
                $ids = $r->get_param('ids');
                if (!is_array($ids)) $ids = array();
                ListManager::reorder($this->namespace, $ids);
                return rest_ensure_response(array('success' => true));
            },
        ));
    }

    // ─── Render Page ───────────────────────────────────────────

    public function render()
    {
        if (!current_user_can($this->capability)) {
            wp_die('دسترسی ممنوع.');
        }

        $items   = ListManager::getAll($this->namespace);
        $restUrl = esc_url_raw(rest_url('tamir-admin/v1/list/' . $this->namespace));
        $nonce   = wp_create_nonce('wp_rest');
        ?>
        <div class="wrap tamir-list-admin" dir="rtl">
            <h1 class="wp-heading-inline"><?php echo esc_html($this->title); ?></h1>
            <p class="description">این لیست در اپ موبایل به کاربران نمایش داده می‌شود.</p>

            <hr class="wp-header-end">

            <div id="tla-toast" class="tla-toast"></div>

            <div class="tla-toolbar">
                <div class="tla-add-form">
                    <input
                        type="text"
                        id="tla-new-input"
                        placeholder="مثلاً: تغییر زمان مراجعه"
                        maxlength="200"
                    />
                    <button type="button" class="button button-primary" id="tla-add-btn">
                        <span class="dashicons dashicons-plus-alt2"></span>
                        افزودن
                    </button>
                </div>
                <div class="tla-stats">
                    <span id="tla-count"><?php echo count($items); ?></span> مورد
                </div>
            </div>

            <div class="tla-card">
                <ul id="tla-list" class="tla-list">
                    <?php foreach ($items as $item) : ?>
                        <?php $this->renderItem($item); ?>
                    <?php endforeach; ?>
                </ul>

                <?php if (empty($items)) : ?>
                    <div class="tla-empty">
                        <span class="dashicons dashicons-info"></span>
                        <p>هنوز موردی اضافه نشده است.</p>
                        <p class="tla-empty-hint">اولین مورد را از فرم بالا اضافه کنید.</p>
                    </div>
                <?php endif; ?>
            </div>

            <p class="tla-help">
                <span class="dashicons dashicons-info-outline"></span>
                نکته: برای تغییر ترتیب، آیتم‌ها را با ماوس بکشید. تغییرات به‌صورت خودکار ذخیره می‌شوند.
            </p>
        </div>

        <style>
            <?php $this->printStyles(); ?>
        </style>

        <script>
            (function ($) {
                'use strict';

                const REST_URL = <?php echo wp_json_encode($restUrl); ?>;
                const NONCE    = <?php echo wp_json_encode($nonce); ?>;

                <?php $this->printScript(); ?>
            })(jQuery);
        </script>
        <?php
    }

    // ─── Render single item (used in PHP and reused as template) ──

    private function renderItem($item)
    {
        $id     = esc_attr($item['id']);
        $label  = esc_html($item['label']);
        $active = !empty($item['is_active']);
        ?>
        <li class="tla-item <?php echo $active ? '' : 'is-inactive'; ?>" data-id="<?php echo $id; ?>">
            <span class="tla-handle dashicons dashicons-menu" title="جابه‌جایی"></span>

            <label class="tla-toggle" title="<?php echo $active ? 'فعال' : 'غیرفعال'; ?>">
                <input type="checkbox" class="tla-active-checkbox" <?php checked($active); ?>>
                <span class="tla-toggle-track"><span class="tla-toggle-thumb"></span></span>
            </label>

            <span class="tla-label"><?php echo $label; ?></span>
            <input type="text" class="tla-edit-input" value="<?php echo esc_attr($item['label']); ?>" maxlength="200">

            <div class="tla-actions">
                <button type="button" class="tla-btn tla-btn-edit" title="ویرایش">
                    <span class="dashicons dashicons-edit"></span>
                </button>
                <button type="button" class="tla-btn tla-btn-save" title="ذخیره">
                    <span class="dashicons dashicons-yes"></span>
                </button>
                <button type="button" class="tla-btn tla-btn-cancel" title="لغو">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
                <button type="button" class="tla-btn tla-btn-delete" title="حذف">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </li>
        <?php
    }

    // ─── Styles ────────────────────────────────────────────────

    private function printStyles()
    {
        ?>
        .tamir-list-admin { font-family: Tahoma, sans-serif; max-width: 920px; }
        .tamir-list-admin .description { color: #6b7280; font-size: 13px; margin-top: 4px; }

        /* Toast */
        .tla-toast {
            position: fixed;
            top: 50px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: #1f2937;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s;
            z-index: 9999;
        }
        .tla-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        .tla-toast.success { background: #059669; }
        .tla-toast.error { background: #dc2626; }

        /* Toolbar */
        .tla-toolbar {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            padding: 16px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }
        .tla-add-form {
            display: flex;
            gap: 8px;
            flex: 1;
            min-width: 0;
        }
        .tla-add-form input[type="text"] {
            flex: 1;
            min-width: 0;
            height: 38px;
            padding: 0 12px;
            border-radius: 6px;
            border: 1.5px solid #d1d5db;
            font-size: 14px;
            font-family: inherit;
        }
        .tla-add-form input[type="text"]:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }
        #tla-add-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            height: 38px;
            font-size: 13px;
        }
        #tla-add-btn .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
            line-height: 16px;
        }
        .tla-stats {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
        }
        .tla-stats #tla-count {
            font-weight: 700;
            color: #111827;
            font-size: 16px;
        }

        /* Card */
        .tla-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        /* List */
        .tla-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .tla-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.15s;
        }
        .tla-item:last-child { border-bottom: none; }
        .tla-item:hover { background: #f9fafb; }
        .tla-item.ui-sortable-helper {
            background: #eff6ff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 8px;
            border-color: #93c5fd;
        }
        .tla-item.is-inactive {
            opacity: 0.55;
        }
        .tla-item.is-inactive .tla-label {
            text-decoration: line-through;
            color: #9ca3af;
        }
        .tla-item.is-editing .tla-label,
        .tla-item.is-editing .tla-btn-edit,
        .tla-item.is-editing .tla-btn-delete,
        .tla-item.is-editing .tla-handle,
        .tla-item.is-editing .tla-toggle { display: none; }
        .tla-item.is-editing .tla-edit-input,
        .tla-item.is-editing .tla-btn-save,
        .tla-item.is-editing .tla-btn-cancel { display: inline-flex; }

        .tla-edit-input,
        .tla-btn-save,
        .tla-btn-cancel { display: none; }

        .tla-handle {
            cursor: grab;
            color: #9ca3af;
            font-size: 18px;
            width: 20px;
            height: 20px;
            line-height: 20px;
        }
        .tla-handle:active { cursor: grabbing; }

        /* iOS-like Toggle */
        .tla-toggle {
            position: relative;
            display: inline-block;
            cursor: pointer;
            flex-shrink: 0;
        }
        .tla-toggle input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        .tla-toggle-track {
            display: inline-block;
            width: 38px;
            height: 22px;
            background: #d1d5db;
            border-radius: 999px;
            position: relative;
            transition: background 0.2s;
        }
        .tla-toggle-thumb {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }
        .tla-toggle input:checked + .tla-toggle-track {
            background: #2563eb;
        }
        .tla-toggle input:checked + .tla-toggle-track .tla-toggle-thumb {
            transform: translateX(-16px);
        }

        .tla-label {
            flex: 1;
            font-size: 14px;
            color: #111827;
            min-width: 0;
            word-break: break-word;
        }

        .tla-edit-input {
            flex: 1;
            min-width: 0;
            height: 32px;
            padding: 0 10px;
            border-radius: 5px;
            border: 1.5px solid #2563eb;
            font-size: 14px;
            font-family: inherit;
        }
        .tla-edit-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .tla-actions {
            display: flex;
            gap: 4px;
            flex-shrink: 0;
        }
        .tla-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .tla-btn:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }
        .tla-btn .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
            line-height: 16px;
        }
        .tla-btn-edit:hover { color: #2563eb; border-color: #93c5fd; }
        .tla-btn-save { color: #059669; }
        .tla-btn-save:hover { background: #d1fae5; border-color: #10b981; }
        .tla-btn-cancel:hover { background: #fef2f2; }
        .tla-btn-delete:hover { color: #dc2626; border-color: #fca5a5; }

        /* Empty state */
        .tla-empty {
            text-align: center;
            padding: 50px 20px;
            color: #9ca3af;
        }
        .tla-empty .dashicons {
            font-size: 40px;
            width: 40px;
            height: 40px;
            margin-bottom: 8px;
        }
        .tla-empty p { margin: 4px 0; }
        .tla-empty-hint { font-size: 13px; color: #d1d5db; }

        .tla-help {
            margin-top: 20px;
            padding: 12px 16px;
            background: #eff6ff;
            border-radius: 8px;
            color: #1e40af;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tla-help .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
            line-height: 16px;
        }

        /* loading state */
        .tla-item.is-saving {
            opacity: 0.5;
            pointer-events: none;
        }
        <?php
    }

    // ─── Script ────────────────────────────────────────────────

    private function printScript()
    {
        ?>
        const $list  = $('#tla-list');
        const $count = $('#tla-count');
        const $input = $('#tla-new-input');
        const $addBtn= $('#tla-add-btn');

        function toast(msg, type) {
            const $t = $('#tla-toast');
            $t.removeClass('success error').addClass(type || '').text(msg);
            $t.addClass('show');
            clearTimeout(window.__tla_toast_t);
            window.__tla_toast_t = setTimeout(function () {
                $t.removeClass('show');
            }, 2400);
        }

        function updateCount() {
            $count.text($list.children('.tla-item').length);
        }

        function escapeHtml(s) {
            return $('<div>').text(s).html();
        }

        function renderItem(item) {
            const active = !!item.is_active;
            const cls = active ? '' : 'is-inactive';
            return [
                '<li class="tla-item ' + cls + '" data-id="' + escapeHtml(item.id) + '">',
                  '<span class="tla-handle dashicons dashicons-menu" title="جابه‌جایی"></span>',
                  '<label class="tla-toggle">',
                    '<input type="checkbox" class="tla-active-checkbox" ' + (active ? 'checked' : '') + '>',
                    '<span class="tla-toggle-track"><span class="tla-toggle-thumb"></span></span>',
                  '</label>',
                  '<span class="tla-label">' + escapeHtml(item.label) + '</span>',
                  '<input type="text" class="tla-edit-input" value="' + escapeHtml(item.label) + '" maxlength="200">',
                  '<div class="tla-actions">',
                    '<button type="button" class="tla-btn tla-btn-edit" title="ویرایش"><span class="dashicons dashicons-edit"></span></button>',
                    '<button type="button" class="tla-btn tla-btn-save" title="ذخیره"><span class="dashicons dashicons-yes"></span></button>',
                    '<button type="button" class="tla-btn tla-btn-cancel" title="لغو"><span class="dashicons dashicons-no-alt"></span></button>',
                    '<button type="button" class="tla-btn tla-btn-delete" title="حذف"><span class="dashicons dashicons-trash"></span></button>',
                  '</div>',
                '</li>'
            ].join('');
        }

        function ajax(method, path, data) {
            return $.ajax({
                method: method,
                url: REST_URL + (path || ''),
                data: data ? JSON.stringify(data) : undefined,
                contentType: 'application/json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', NONCE);
                }
            });
        }

        // ─── ADD ─────────────────────────────────────
        $addBtn.on('click', function () {
            const label = $input.val().trim();
            if (!label) {
                $input.focus();
                return;
            }
            $addBtn.prop('disabled', true);

            ajax('POST', '', { label: label })
                .done(function (res) {
                    if (res.success && res.data) {
                        $('.tla-empty').remove();
                        $list.append(renderItem(res.data));
                        $input.val('').focus();
                        updateCount();
                        toast('اضافه شد', 'success');
                    }
                })
                .fail(function (xhr) {
                    const m = (xhr.responseJSON && xhr.responseJSON.message) || 'خطا در افزودن';
                    toast(m, 'error');
                })
                .always(function () {
                    $addBtn.prop('disabled', false);
                });
        });

        $input.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $addBtn.click();
            }
        });

        // ─── EDIT ────────────────────────────────────
        $list.on('click', '.tla-btn-edit', function () {
            const $li = $(this).closest('.tla-item');
            $li.addClass('is-editing');
            const $inp = $li.find('.tla-edit-input');
            $inp.val($li.find('.tla-label').text()).focus();
        });

        $list.on('click', '.tla-btn-cancel', function () {
            const $li = $(this).closest('.tla-item');
            $li.removeClass('is-editing');
        });

        $list.on('click', '.tla-btn-save', function () {
            const $li = $(this).closest('.tla-item');
            const id  = $li.data('id');
            const newLabel = $li.find('.tla-edit-input').val().trim();

            if (!newLabel) {
                toast('متن نمی‌تواند خالی باشد', 'error');
                return;
            }

            $li.addClass('is-saving');

            ajax('PUT', '/' + id, { label: newLabel })
                .done(function (res) {
                    if (res.success && res.data) {
                        $li.find('.tla-label').text(res.data.label);
                        $li.removeClass('is-editing');
                        toast('ذخیره شد', 'success');
                    }
                })
                .fail(function (xhr) {
                    const m = (xhr.responseJSON && xhr.responseJSON.message) || 'خطا در ذخیره';
                    toast(m, 'error');
                })
                .always(function () {
                    $li.removeClass('is-saving');
                });
        });

        $list.on('keydown', '.tla-edit-input', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $(this).closest('.tla-item').find('.tla-btn-save').click();
            } else if (e.key === 'Escape') {
                $(this).closest('.tla-item').find('.tla-btn-cancel').click();
            }
        });

        // ─── DELETE ──────────────────────────────────
        $list.on('click', '.tla-btn-delete', function () {
            const $li = $(this).closest('.tla-item');
            const label = $li.find('.tla-label').text();
            if (!confirm('آیا مطمئن هستید که می‌خواهید "' + label + '" حذف شود؟')) return;

            const id = $li.data('id');
            $li.addClass('is-saving');

            ajax('DELETE', '/' + id)
                .done(function (res) {
                    if (res.success) {
                        $li.fadeOut(180, function () {
                            $li.remove();
                            updateCount();
                        });
                        toast('حذف شد', 'success');
                    }
                })
                .fail(function (xhr) {
                    const m = (xhr.responseJSON && xhr.responseJSON.message) || 'خطا در حذف';
                    toast(m, 'error');
                    $li.removeClass('is-saving');
                });
        });

        // ─── TOGGLE ACTIVE ───────────────────────────
        $list.on('change', '.tla-active-checkbox', function () {
            const $li = $(this).closest('.tla-item');
            const id  = $li.data('id');
            const isActive = $(this).is(':checked');

            $li.toggleClass('is-inactive', !isActive);

            ajax('PUT', '/' + id, { is_active: isActive ? 1 : 0 })
                .done(function (res) {
                    if (res.success) {
                        toast(isActive ? 'فعال شد' : 'غیرفعال شد', 'success');
                    }
                })
                .fail(function () {
                    // Revert
                    $(this).prop('checked', !isActive);
                    $li.toggleClass('is-inactive', isActive);
                    toast('خطا در تغییر وضعیت', 'error');
                });
        });

        // ─── REORDER (drag-drop) ─────────────────────
        let __reorderTimer;
        $list.sortable({
            handle: '.tla-handle',
            placeholder: 'tla-item ui-sortable-placeholder',
            tolerance: 'pointer',
            axis: 'y',
            update: function () {
                const ids = $list.children('.tla-item').map(function () {
                    return $(this).data('id');
                }).get();

                clearTimeout(__reorderTimer);
                __reorderTimer = setTimeout(function () {
                    ajax('POST', '/reorder', { ids: ids })
                        .done(function () { toast('ترتیب ذخیره شد', 'success'); })
                        .fail(function () { toast('خطا در ذخیره ترتیب', 'error'); });
                }, 300);
            }
        });

        // ─── Auto-focus input on page load ───────────
        $input.focus();
        <?php
    }
}
