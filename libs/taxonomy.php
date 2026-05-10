<?php


// brands
add_action('wp_ajax_brand', 'brand');

if (!function_exists('brand')):

    function brand()
    {


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $name = $_POST['name'];

        if ($type_action == "add") {

            $add = wp_insert_term($name, 'brand', array('slug' => $name));
            $status = true;
            $message = "برند با موفقیت ثبت گردید";
        } else if ($type_action == "update") {

            $update = wp_update_term($uid, 'brand', array(
                'name' => $name,
                'slug' => $name
            ));

            $status = true;
            $message = "برند با موفقیت به روز رسانی شد.";
        }

        print json_encode(array("status" => $status, "message" => $message));
        exit();
    }
endif;

// device
add_action('wp_ajax_device', 'device');

if (!function_exists('device')):

    function device()
    {


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $name = $_POST['name'];

        if ($type_action == "add") {

            $add = wp_insert_term($name, 'device', array('slug' => $name));
            $status = true;
            $message = "دستگاه با موفقیت ثبت گردید";
        } else if ($type_action == "update") {

            $update = wp_update_term($uid, 'device', array(
                'name' => $name,
                'slug' => $name
            ));

            $status = true;
            $message = "دستگاه با موفقیت به روز رسانی شد.";
        }

        print json_encode(array("status" => $status, "message" => $message));
        exit();
    }
endif;

// State
add_action('wp_ajax_state', 'state');

if (!function_exists('state')):

    function state()
    {


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $name = $_POST['name'];

        if ($type_action == "add") {

            $add = wp_insert_term($name, 'state', array('slug' => $name));
            $status = true;
            $message = "استان با موفقیت ثبت گردید";
        } else if ($type_action == "update") {

            $update = wp_update_term($uid, 'state', array(
                'name' => $name,
                'slug' => $name
            ));

            $status = true;
            $message = "استان با موفقیت به روز رسانی شد.";
        }

        print json_encode(array("status" => $status, "message" => $message));
        exit();
    }
endif;

// city
add_action('wp_ajax_city', 'city');

if (!function_exists('city')):

    function city()
    {


        $type_action = $_POST['type_action'];
        $uid = $_POST['uid'];
        $name = $_POST['name'];

        if ($type_action == "add") {

            $add = wp_insert_term($name, 'city', array('slug' => $name));
            $status = true;
            $message = "شهر با موفقیت ثبت گردید";
        } else if ($type_action == "update") {

            $update = wp_update_term($uid, 'city', array(
                'name' => $name,
                'slug' => $name
            ));

            $status = true;
            $message = "شهر با موفقیت به روز رسانی شد.";
        }

        print json_encode(array("status" => $status, "message" => $message));
        exit();
    }
endif;

// ──────────────────────────────────────────────────────────────
//  ⭐ Helper: پالت رنگ‌های پیش‌فرض (میانبر برای ادمین)
// ──────────────────────────────────────────────────────────────
function tamir_badge_presets()
{
    return array(
        array('name' => 'طلایی',  'bg' => '#fbbf24', 'color' => '#000000'),
        array('name' => 'آبی',    'bg' => '#0ea5e9', 'color' => '#ffffff'),
        array('name' => 'قرمز',   'bg' => '#ef4444', 'color' => '#ffffff'),
        array('name' => 'سبز',    'bg' => '#10b981', 'color' => '#ffffff'),
        array('name' => 'بنفش',   'bg' => '#8b5cf6', 'color' => '#ffffff'),
        array('name' => 'نارنجی', 'bg' => '#f97316', 'color' => '#ffffff'),
        array('name' => 'صورتی',  'bg' => '#ec4899', 'color' => '#ffffff'),
        array('name' => 'مشکی',   'bg' => '#1f2937', 'color' => '#ffffff'),
    );
}

// ──────────────────────────────────────────────────────────────
//  ⭐ Helper: sanitize hex color
// ──────────────────────────────────────────────────────────────
function tamir_sanitize_hex($color, $default = '')
{
    $color = sanitize_text_field($color);
    if (preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $color)) {
        return $color;
    }
    return $default;
}

// ──────────────────────────────────────────────────────────────
//  ترتیب نمایش taxonomy برای ادمین
// ──────────────────────────────────────────────────────────────
function tamir_sanitize_order($value)
{
    return isset($value) ? absint($value) : 0;
}

// ──────────────────────────────────────────────────────────────
//  بارگذاری WP color picker
// ──────────────────────────────────────────────────────────────
add_action('admin_enqueue_scripts', 'tamir_load_color_picker');
function tamir_load_color_picker($hook)
{
    $screen = get_current_screen();
    if (!$screen) return;
    if (!in_array($screen->taxonomy, array('device', 'brand'))) return;

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}

// ──────────────────────────────────────────────────────────────
//  استایل ادمین
// ──────────────────────────────────────────────────────────────
add_action('admin_head', 'tamir_tax_meta_styles');
function tamir_tax_meta_styles()
{
    $screen = get_current_screen();
    if (!$screen) return;
    if (!in_array($screen->taxonomy, array('device', 'brand'))) return;
?>
    <style>
        .tamir-field {
            margin-bottom: 15px;
        }

        .tamir-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .tamir-field input[type=text] {
            width: 100%;
            max-width: 400px;
        }

        .tamir-icon-preview {
            display: inline-block;
            font-size: 2rem;
            margin-right: 10px;
            vertical-align: middle;
            line-height: 1;
        }

        .tamir-image-preview {
            display: block;
            max-width: 120px;
            max-height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dde2ea;
            margin-top: 8px;
        }

        .tamir-desc {
            color: #6b7280;
            font-size: 12px;
            margin-top: 4px;
        }

        .tamir-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 6px;
        }

        /* ⭐ Badge preview */
        .tamir-badge-preview {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 8px;
        }

        /* پالت پیش‌فرض */
        .tamir-color-presets {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .tamir-color-preset {
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.15s;
        }

        .tamir-color-preset:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* دو فیلد رنگ کنار هم */
        .tamir-color-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .tamir-color-row>div {
            flex: 1;
            min-width: 200px;
        }

        .tamir-color-row label {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 13px;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            // فعال کردن WP color picker
            if ($.fn.wpColorPicker) {
                $('.tamir-color-picker').wpColorPicker({
                    change: function(event, ui) {
                        tamirUpdatePreview();
                    },
                    clear: function() {
                        tamirUpdatePreview();
                    }
                });
            }

            // تابع preview زنده
            window.tamirUpdatePreview = function() {
                var text = $('#tamir_badge_text').val() || 'پیش‌نمایش';
                var bg = $('#tamir_badge_bg').val() || '#fbbf24';
                var color = $('#tamir_badge_color').val() || '#000000';
                $('#tamir_badge_live_preview')
                    .text(text)
                    .css({
                        background: bg,
                        color: color,
                        display: text ? 'inline-block' : 'none'
                    });
            };

            // update preview on text change
            $('#tamir_badge_text').on('input', tamirUpdatePreview);

            // کلیک روی پالت پیش‌فرض
            $(document).on('click', '.tamir-color-preset', function(e) {
                e.preventDefault();
                var bg = $(this).data('bg');
                var color = $(this).data('color');

                // استفاده از API wpColorPicker
                $('#tamir_badge_bg').val(bg).trigger('change');
                $('#tamir_badge_color').val(color).trigger('change');

                if ($.fn.wpColorPicker) {
                    $('#tamir_badge_bg').wpColorPicker('color', bg);
                    $('#tamir_badge_color').wpColorPicker('color', color);
                }
                tamirUpdatePreview();
            });

            // اولیه
            tamirUpdatePreview();
        });
    </script>
<?php
}

// ──────────────────────────────────────────────────────────────
//  Helper: رندر پالت رنگ‌های پیش‌فرض
// ──────────────────────────────────────────────────────────────
function tamir_render_color_presets()
{
    $presets = tamir_badge_presets();
?>
    <div class="tamir-color-presets">
        <?php foreach ($presets as $preset): ?>
            <a href="#"
                class="tamir-color-preset"
                data-bg="<?php echo esc_attr($preset['bg']); ?>"
                data-color="<?php echo esc_attr($preset['color']); ?>"
                style="background: <?php echo esc_attr($preset['bg']); ?>; color: <?php echo esc_attr($preset['color']); ?>;">
                <?php echo esc_html($preset['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <p class="tamir-desc">
        کلیک کنید تا رنگ‌های از پیش تعیین شده انتخاب شوند.
    </p>
<?php
}

// ──────────────────────────────────────────────────────────────
//  فرم افزودن term جدید
// ──────────────────────────────────────────────────────────────
add_action('device_add_form_fields', 'tamir_add_term_fields');
add_action('brand_add_form_fields',  'tamir_add_term_fields');
function tamir_add_term_fields($taxonomy)
{
?>
    <div class="form-field tamir-field">
        <label for="tamir_icon">
            آیکون
            <span class="tamir-badge">اختیاری</span>
        </label>
        <input type="text" name="tamir_icon" id="tamir_icon" value=""
            placeholder="مثال: 🫧 یا https://site.com/icon.png">
        <p class="tamir-desc">
            می‌توانید یک emoji مستقیم وارد کنید (مثل 🫧 ❄️ 📺)
            یا آدرس کامل یک آیکون را paste کنید.
        </p>
    </div>

    <div class="form-field tamir-field">
        <label for="tamir_image">
            تصویر
            <span class="tamir-badge">اختیاری</span>
        </label>
        <input type="text" name="tamir_image" id="tamir_image" value=""
            placeholder="https://site.com/image.jpg">
        <p class="tamir-desc">آدرس کامل تصویر (jpg, png, webp)</p>
    </div>

    <div class="form-field tamir-field">
        <label for="tamir_order">
            ترتیب نمایش
            <span class="tamir-badge">اختیاری</span>
        </label>
        <input type="number" name="tamir_order" id="tamir_order" value="0" min="0" step="1">
        <p class="tamir-desc">
            عدد کمتر، زودتر نمایش داده می‌شود. مثال: 1، 2، 3
        </p>
    </div>

    <?php if ($taxonomy === 'device'): ?>
        <div class="form-field tamir-field">
            <label for="tamir_is_active">
                وضعیت دستگاه
            </label>

            <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                <input type="checkbox" name="tamir_is_active" id="tamir_is_active" value="1" checked>
                فعال باشد
            </label>

            <p class="tamir-desc">
                اگر غیرفعال شود، می‌توانید در خروجی سایت آن را نمایش ندهید.
            </p>
        </div>
    <?php endif; ?>

    <!-- ⭐ Badge Text -->
    <div class="form-field tamir-field">
        <label for="tamir_badge_text">
            متن نشان (badge)
            <span class="tamir-badge">اختیاری</span>
        </label>
        <input type="text" name="tamir_badge_text" id="tamir_badge_text" value=""
            placeholder="مثال: پرطرفدار، جدید، تخفیف"
            maxlength="15">
        <p class="tamir-desc">
            حداکثر ۱۵ کاراکتر. خالی = بدون نشان.
        </p>
    </div>

    <!-- ⭐ Badge Colors -->
    <div class="form-field tamir-field">
        <div class="tamir-color-row">
            <div>
                <label for="tamir_badge_bg">رنگ پس‌زمینه</label>
                <input type="text" name="tamir_badge_bg" id="tamir_badge_bg"
                    class="tamir-color-picker"
                    value="#fbbf24"
                    data-default-color="#fbbf24">
            </div>
            <div>
                <label for="tamir_badge_color">رنگ متن</label>
                <input type="text" name="tamir_badge_color" id="tamir_badge_color"
                    class="tamir-color-picker"
                    value="#000000"
                    data-default-color="#000000">
            </div>
        </div>

        <label style="margin-top: 12px;">پالت آماده (اختیاری):</label>
        <?php tamir_render_color_presets(); ?>

        <label style="margin-top: 12px;">پیش‌نمایش:</label>
        <div>
            <span class="tamir-badge-preview" id="tamir_badge_live_preview"
                style="background: #fbbf24; color: #000000;">
                پیش‌نمایش
            </span>
        </div>
    </div>
<?php
}

// ──────────────────────────────────────────────────────────────
//  فرم ویرایش term موجود
// ──────────────────────────────────────────────────────────────
add_action('device_edit_form_fields', 'tamir_edit_term_fields', 10, 2);
add_action('brand_edit_form_fields',  'tamir_edit_term_fields', 10, 2);
function tamir_edit_term_fields($term, $taxonomy)
{
    $icon  = get_term_meta($term->term_id, 'tamir_icon',  true);
    $image = get_term_meta($term->term_id, 'tamir_image', true);

    // ⭐ badge
    $badgeText  = get_term_meta($term->term_id, 'tamir_badge_text',  true);
    $badgeBg    = get_term_meta($term->term_id, 'tamir_badge_bg',    true) ?: '#fbbf24';
    $badgeColor = get_term_meta($term->term_id, 'tamir_badge_color', true) ?: '#000000';

    $order = get_term_meta($term->term_id, 'tamir_order', true);
    $order = $order !== '' ? absint($order) : 0;

    $isActive = get_term_meta($term->term_id, 'tamir_is_active', true);
    $isActive = $isActive === '' ? 1 : absint($isActive);

    // در مورد brand: device_ids
    $deviceIds = array();
    if ($taxonomy === 'brand') {
        $saved = get_term_meta($term->term_id, 'tamir_device_ids', true);
        $deviceIds = is_array($saved) ? $saved : array();
    }
?>
    <tr class="form-field tamir-field">
        <th scope="row">
            <label for="tamir_icon">آیکون</label>
        </th>
        <td>
            <?php if ($icon): ?>
                <span class="tamir-icon-preview"><?php echo esc_html($icon); ?></span>
            <?php endif; ?>
            <input type="text" name="tamir_icon" id="tamir_icon"
                value="<?php echo esc_attr($icon); ?>"
                placeholder="🫧 یا https://...">
            <p class="tamir-desc">emoji یا URL تصویر آیکون</p>
        </td>
    </tr>

    <tr class="form-field tamir-field">
        <th scope="row">
            <label for="tamir_image">تصویر</label>
        </th>
        <td>
            <?php if ($image && filter_var($image, FILTER_VALIDATE_URL)): ?>
                <img src="<?php echo esc_url($image); ?>"
                    class="tamir-image-preview" alt="تصویر">
            <?php endif; ?>
            <input type="text" name="tamir_image" id="tamir_image"
                value="<?php echo esc_attr($image); ?>"
                placeholder="https://site.com/image.jpg">
            <p class="tamir-desc">آدرس کامل تصویر</p>
        </td>
    </tr>

    <tr class="form-field tamir-field">
        <th scope="row">
            <label for="tamir_order">ترتیب نمایش</label>
        </th>
        <td>
            <input type="number" name="tamir_order" id="tamir_order"
                value="<?php echo esc_attr($order); ?>"
                min="0" step="1">
            <p class="tamir-desc">
                عدد کمتر، زودتر نمایش داده می‌شود. مثال: 1، 2، 3
            </p>
        </td>
    </tr>

    <?php if ($taxonomy === 'device'): ?>
        <tr class="form-field tamir-field">
            <th scope="row">
                <label for="tamir_is_active">وضعیت دستگاه</label>
            </th>
            <td>
                <label style="display:flex;align-items:center;gap:8px;font-weight:400;">
                    <input type="checkbox" name="tamir_is_active" id="tamir_is_active" value="1"
                        <?php checked($isActive, 1); ?>>
                    فعال باشد
                </label>

                <p class="tamir-desc">
                    اگر غیرفعال شود، می‌توانید در خروجی سایت آن را نمایش ندهید.
                </p>
            </td>
        </tr>
    <?php endif; ?>

    <!-- ⭐ Badge Text -->
    <tr class="form-field tamir-field">
        <th scope="row">
            <label for="tamir_badge_text">متن نشان</label>
        </th>
        <td>
            <input type="text" name="tamir_badge_text" id="tamir_badge_text"
                value="<?php echo esc_attr($badgeText); ?>"
                placeholder="پرطرفدار، جدید، تخفیف"
                maxlength="15">
            <p class="tamir-desc">
                حداکثر ۱۵ کاراکتر. خالی = بدون نشان.
            </p>
        </td>
    </tr>

    <!-- ⭐ Badge Colors -->
    <tr class="form-field tamir-field">
        <th scope="row">
            <label>رنگ نشان</label>
        </th>
        <td>
            <div class="tamir-color-row">
                <div>
                    <label for="tamir_badge_bg">رنگ پس‌زمینه</label>
                    <input type="text" name="tamir_badge_bg" id="tamir_badge_bg"
                        class="tamir-color-picker"
                        value="<?php echo esc_attr($badgeBg); ?>"
                        data-default-color="#fbbf24">
                </div>
                <div>
                    <label for="tamir_badge_color">رنگ متن</label>
                    <input type="text" name="tamir_badge_color" id="tamir_badge_color"
                        class="tamir-color-picker"
                        value="<?php echo esc_attr($badgeColor); ?>"
                        data-default-color="#000000">
                </div>
            </div>

            <label style="margin-top: 12px;">پالت آماده (کلیک برای انتخاب):</label>
            <?php tamir_render_color_presets(); ?>

            <label style="margin-top: 12px;">پیش‌نمایش:</label>
            <div>
                <span class="tamir-badge-preview" id="tamir_badge_live_preview"
                    style="background: <?php echo esc_attr($badgeBg); ?>; color: <?php echo esc_attr($badgeColor); ?>;">
                    <?php echo $badgeText ? esc_html($badgeText) : 'پیش‌نمایش'; ?>
                </span>
            </div>
        </td>
    </tr>

    <?php if ($taxonomy === 'brand'): ?>
        <tr class="form-field tamir-field">
            <th scope="row">
                <label>دستگاه‌های مرتبط</label>
            </th>
            <td>
                <?php
                $devices = get_terms(array(
                    'taxonomy'   => 'device',
                    'hide_empty' => false,
                ));
                if (!is_wp_error($devices) && !empty($devices)):
                    foreach ($devices as $device):
                        $checked = in_array($device->term_id, $deviceIds) ? 'checked' : '';
                ?>
                        <label style="display:block;margin-bottom:6px">
                            <input type="checkbox" name="tamir_device_ids[]"
                                value="<?php echo esc_attr($device->term_id); ?>"
                                <?php echo $checked; ?>>
                            <?php echo esc_html($device->name); ?>
                        </label>
                <?php
                    endforeach;
                else:
                    echo '<p style="color:#9ca3af">هیچ دستگاهی ثبت نشده</p>';
                endif;
                ?>
                <p class="tamir-desc">
                    اگر هیچ دستگاهی انتخاب نشود، این برند برای همه دستگاه‌ها نمایش داده می‌شود.
                </p>
            </td>
        </tr>
    <?php endif; ?>
<?php
}

// ──────────────────────────────────────────────────────────────
//  ذخیره — هنگام create
// ──────────────────────────────────────────────────────────────
add_action('created_device', 'tamir_save_term_meta', 10, 2);
add_action('created_brand',  'tamir_save_term_meta', 10, 2);
function tamir_save_term_meta($termId, $ttId)
{
    if (isset($_POST['tamir_icon'])) {
        update_term_meta($termId, 'tamir_icon', sanitize_text_field($_POST['tamir_icon']));
    }

    if (isset($_POST['tamir_image'])) {
        update_term_meta($termId, 'tamir_image', esc_url_raw($_POST['tamir_image']));
    }

    if (isset($_POST['tamir_order'])) {
        update_term_meta($termId, 'tamir_order', tamir_sanitize_order($_POST['tamir_order']));
    }

    if (isset($_POST['tamir_badge_text'])) {
        $badgeText = sanitize_text_field($_POST['tamir_badge_text']);
        update_term_meta($termId, 'tamir_badge_text', mb_substr($badgeText, 0, 15));
    }

    if (isset($_POST['tamir_badge_bg'])) {
        $bg = tamir_sanitize_hex($_POST['tamir_badge_bg'], '#fbbf24');
        update_term_meta($termId, 'tamir_badge_bg', $bg);
    }

    if (isset($_POST['tamir_badge_color'])) {
        $color = tamir_sanitize_hex($_POST['tamir_badge_color'], '#000000');
        update_term_meta($termId, 'tamir_badge_color', $color);
    }

    // وضعیت فعال / غیرفعال دستگاه
    $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';

    if ($taxonomy === 'device') {
        $isActive = isset($_POST['tamir_is_active']) ? 1 : 0;
        update_term_meta($termId, 'tamir_is_active', $isActive);
    }
}

// ──────────────────────────────────────────────────────────────
//  ذخیره — هنگام edit
// ──────────────────────────────────────────────────────────────
add_action('edited_device', 'tamir_update_term_meta', 10, 2);
add_action('edited_brand',  'tamir_update_term_meta', 10, 2);
function tamir_update_term_meta($termId, $ttId)
{
    $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';

    if (isset($_POST['tamir_icon'])) {
        update_term_meta($termId, 'tamir_icon', sanitize_text_field($_POST['tamir_icon']));
    }

    if (isset($_POST['tamir_image'])) {
        update_term_meta($termId, 'tamir_image', esc_url_raw($_POST['tamir_image']));
    }

    if (isset($_POST['tamir_order'])) {
        update_term_meta($termId, 'tamir_order', tamir_sanitize_order($_POST['tamir_order']));
    }

    if (isset($_POST['tamir_badge_text'])) {
        $badgeText = sanitize_text_field($_POST['tamir_badge_text']);
        update_term_meta($termId, 'tamir_badge_text', mb_substr($badgeText, 0, 15));
    }

    if (isset($_POST['tamir_badge_bg'])) {
        $bg = tamir_sanitize_hex($_POST['tamir_badge_bg'], '#fbbf24');
        update_term_meta($termId, 'tamir_badge_bg', $bg);
    }

    if (isset($_POST['tamir_badge_color'])) {
        $color = tamir_sanitize_hex($_POST['tamir_badge_color'], '#000000');
        update_term_meta($termId, 'tamir_badge_color', $color);
    }

    // وضعیت فعال / غیرفعال دستگاه
    if ($taxonomy === 'device') {
        $isActive = isset($_POST['tamir_is_active']) ? 1 : 0;
        update_term_meta($termId, 'tamir_is_active', $isActive);
    }

    // device_ids برای brand
    if ($taxonomy === 'brand') {
        $deviceIds = array();

        if (isset($_POST['tamir_device_ids']) && is_array($_POST['tamir_device_ids'])) {
            foreach ($_POST['tamir_device_ids'] as $id) {
                $deviceIds[] = absint($id);
            }
        }

        update_term_meta($termId, 'tamir_device_ids', $deviceIds);
    }
}

// ──────────────────────────────────────────────────────────────
//  ستون آیکون در لیست taxonomy
// ──────────────────────────────────────────────────────────────
add_filter('manage_edit-device_columns', 'tamir_taxonomy_columns');
add_filter('manage_edit-brand_columns',  'tamir_taxonomy_columns');
function tamir_taxonomy_columns($columns)
{
    $new = array();

    foreach ($columns as $key => $val) {
        $new[$key] = $val;

        if ($key === 'name') {
            $new['tamir_order']  = 'ترتیب';
            $new['tamir_status'] = 'وضعیت';
            $new['tamir_icon']   = 'آیکون';
            $new['tamir_image']  = 'تصویر';
            $new['tamir_badge']  = 'نشان';
        }
    }

    return $new;
}

add_filter('manage_device_custom_column', 'tamir_taxonomy_column_content', 10, 3);
add_filter('manage_brand_custom_column',  'tamir_taxonomy_column_content', 10, 3);
function tamir_taxonomy_column_content($content, $column_name, $term_id)
{
    if ($column_name === 'tamir_icon') {
        $icon = get_term_meta($term_id, 'tamir_icon', true);
        if ($icon) {
            if (filter_var($icon, FILTER_VALIDATE_URL)) {
                return '<img src="' . esc_url($icon) . '" style="width:28px;height:28px;object-fit:cover;border-radius:4px">';
            }
            return '<span style="font-size:1.4rem;line-height:1">' . esc_html($icon) . '</span>';
        }
        return '<span style="color:#d1d5db">—</span>';
    }

    if ($column_name === 'tamir_image') {
        $image = get_term_meta($term_id, 'tamir_image', true);
        if ($image && filter_var($image, FILTER_VALIDATE_URL)) {
            return '<img src="' . esc_url($image) . '" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb">';
        }
        return '<span style="color:#d1d5db">—</span>';
    }

    if ($column_name === 'tamir_badge') {
        $badgeText = get_term_meta($term_id, 'tamir_badge_text', true);
        if (!$badgeText) {
            return '<span style="color:#d1d5db">—</span>';
        }
        $bg    = get_term_meta($term_id, 'tamir_badge_bg',    true) ?: '#fbbf24';
        $color = get_term_meta($term_id, 'tamir_badge_color', true) ?: '#000000';
        return '<span style="background:' . esc_attr($bg) . ';color:' . esc_attr($color) . ';padding:3px 10px;border-radius:100px;font-size:11px;font-weight:700;display:inline-block;">' . esc_html($badgeText) . '</span>';
    }

    if ($column_name === 'tamir_order') {
        $order = get_term_meta($term_id, 'tamir_order', true);
        $order = $order !== '' ? absint($order) : 0;

        return '<strong>' . esc_html($order) . '</strong>';
    }

    if ($column_name === 'tamir_status') {
        $term = get_term($term_id);

        if (!$term || is_wp_error($term) || $term->taxonomy !== 'device') {
            return '<span style="color:#d1d5db">—</span>';
        }

        $isActive = get_term_meta($term_id, 'tamir_is_active', true);
        $isActive = $isActive === '' ? 1 : absint($isActive);

        if ($isActive) {
            return '<span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:700;display:inline-block;">فعال</span>';
        }

        return '<span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:700;display:inline-block;">غیرفعال</span>';
    }

    return $content;
}


// ──────────────────────────────────────────────────────────────
//  sortable column برای ترتیب در لیست taxonomy
// ──────────────────────────────────────────────────────────────
add_filter('manage_edit-device_sortable_columns', 'tamir_taxonomy_sortable_columns');
add_filter('manage_edit-brand_sortable_columns',  'tamir_taxonomy_sortable_columns');

function tamir_taxonomy_sortable_columns($columns)
{
    $columns['tamir_order'] = 'tamir_order';
    return $columns;
}

add_action('pre_get_terms', 'tamir_taxonomy_admin_orderby');

function tamir_taxonomy_admin_orderby($query)
{
    if (!is_admin()) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen || !in_array($screen->taxonomy, array('device', 'brand'))) {
        return;
    }

    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';

    if ($orderby === 'tamir_order') {
        $query->query_vars['meta_key'] = 'tamir_order';
        $query->query_vars['orderby']  = 'meta_value_num';
    }
}
