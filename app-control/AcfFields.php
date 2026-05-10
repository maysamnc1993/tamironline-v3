<?php

/**
 * Tamir App Control — ACF Fields
 * ───────────────────────────────────────────────────────────
 *  ثبت یک Options Page در ACF Pro و فیلدهای آن.
 *
 *  مسیر ادمین:
 *    wp-admin → "کنترل اپ تعمیرآنلاین"
 *
 *  امکانات:
 *   1. Kill Switch (فعال/غیرفعال کل اپ)
 *   2. پیام برای کاربران (هنگام غیرفعالی)
 *   3. Min Version (اجبار آپدیت)
 *   4. Feature Flags (غیرفعال قابلیت خاص)
 *   5. Force Re-auth (invalidate JWT ها)
 *   6. Global Banner (اطلاع‌رسانی)
 *   7. ⭐ Banners (بنرهای سراسری با placement)
 *      🆕 placement جدید: 'login' — اسلایدر صفحه ورود
 *      🆕 subfields جدید: accent + caption — برای overlay در صفحه login
 * ───────────────────────────────────────────────────────────
 */

namespace Tamir\AppControl;

defined('ABSPATH') || exit;

final class AcfFields
{
    /** Option page slug */
    const PAGE_SLUG = 'tamir-app-control';

    /** prefix برای all ACF keys */
    const KEY_PREFIX = 'tamir_app_';

    /**
     * محل‌های نمایش بنرها
     * این لیست رو هر وقت خواستی می‌تونی گسترش بدی
     */
    public static function getPlacements(): array
    {
        return [
            'home_top'        => '🏠 بالای صفحه اصلی (اسلایدر)',
            'home_promotions' => '🎁 پیشنهادات ویژه (دو بنر)',
            'orders_list'     => '📋 بالای لیست سفارش‌ها',
            'profile'         => '👤 صفحه پروفایل',
            'login'           => '🔐 اسلایدر صفحه ورود',  // 🆕
        ];
    }

    /**
     * راه‌اندازی — از bootstrap صدا زده می‌شود
     */
    public static function register(): void
    {
        if (!function_exists('acf_add_options_page')) {
            add_action('admin_notices', [__CLASS__, 'showAcfMissingNotice']);
            return;
        }

        add_action('acf/init', [__CLASS__, 'registerOptionsPage']);
        add_action('acf/init', [__CLASS__, 'registerFieldGroup']);
    }

    /**
     * ثبت Options Page
     */
    public static function registerOptionsPage(): void
    {
        acf_add_options_page([
            'page_title'  => 'کنترل اپ تعمیرآنلاین',
            'menu_title'  => 'کنترل اپ',
            'menu_slug'   => self::PAGE_SLUG,
            'capability'  => 'manage_options',
            'icon_url'    => 'dashicons-smartphone',
            'position'    => 3,
            'redirect'    => false,
            'autoload'    => true,
            'update_button' => 'ذخیره تنظیمات',
            'updated_message' => '✅ تنظیمات اپ با موفقیت بروزرسانی شد.',
        ]);
    }

    /**
     * ثبت Field Group
     */
    public static function registerFieldGroup(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key'    => 'group_tamir_app_control',
            'title'  => 'تنظیمات اپ تعمیرآنلاین',
            'fields' => [

                // ══════════════════════════════════════════
                //  Tab 1: Kill Switch
                // ══════════════════════════════════════════
                [
                    'key'   => 'field_tab_killswitch',
                    'type'  => 'tab',
                    'label' => '🔴 وضعیت اپ',
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'active',
                    'label'         => 'اپ فعال است؟',
                    'name'          => self::KEY_PREFIX . 'active',
                    'type'          => 'true_false',
                    'instructions'  => 'اگر خاموش شود، همه کاربران بلافاصله در اپ خود پیام "غیرفعال" می‌بینند.',
                    'default_value' => 1,
                    'ui'            => 1,
                    'ui_on_text'    => 'فعال',
                    'ui_off_text'   => 'غیرفعال',
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'message',
                    'label'         => 'پیام در صورت غیرفعال بودن',
                    'name'          => self::KEY_PREFIX . 'message',
                    'type'          => 'textarea',
                    'default_value' => 'اپ در حال به‌روزرسانی است. لطفاً چند دقیقه دیگر تلاش کنید.',
                    'rows'          => 2,
                    'maxlength'     => 200,
                    'conditional_logic' => [
                        [['field' => 'field_' . self::KEY_PREFIX . 'active', 'operator' => '==', 'value' => '0']],
                    ],
                ],

                // ══════════════════════════════════════════
                //  Tab 2: Min Version
                // ══════════════════════════════════════════
                [
                    'key'   => 'field_tab_version',
                    'type'  => 'tab',
                    'label' => '📱 نسخه اپ',
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'min_version',
                    'label'         => 'حداقل نسخه مجاز',
                    'name'          => self::KEY_PREFIX . 'min_version',
                    'type'          => 'text',
                    'default_value' => '1.0.0',
                    'placeholder'   => '1.0.0',
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'update_url',
                    'label'         => 'لینک دریافت نسخه جدید',
                    'name'          => self::KEY_PREFIX . 'update_url',
                    'type'          => 'url',
                    'placeholder'   => 'https://tamir-online.com',
                ],

                // ══════════════════════════════════════════
                //  Tab 3: Feature Flags
                // ══════════════════════════════════════════
                [
                    'key'   => 'field_tab_features',
                    'type'  => 'tab',
                    'label' => '⚙️ قابلیت‌ها',
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'feat_new_orders',
                    'label'         => 'ثبت سفارش جدید',
                    'name'          => self::KEY_PREFIX . 'feat_new_orders',
                    'type'          => 'true_false',
                    'default_value' => 1,
                    'ui'            => 1,
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'feat_cancel_orders',
                    'label'         => 'لغو سفارش',
                    'name'          => self::KEY_PREFIX . 'feat_cancel_orders',
                    'type'          => 'true_false',
                    'default_value' => 1,
                    'ui'            => 1,
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'feat_support_chat',
                    'label'         => 'چت پشتیبانی',
                    'name'          => self::KEY_PREFIX . 'feat_support_chat',
                    'type'          => 'true_false',
                    'default_value' => 1,
                    'ui'            => 1,
                ],

                // ══════════════════════════════════════════
                //  Tab 4: Security
                // ══════════════════════════════════════════
                [
                    'key'   => 'field_tab_security',
                    'type'  => 'tab',
                    'label' => '🛡️ امنیت',
                ],
                [
                    'key'             => 'field_' . self::KEY_PREFIX . 'force_reauth_at',
                    'label'           => 'اجبار به ورود مجدد همه کاربران',
                    'name'            => self::KEY_PREFIX . 'force_reauth_at',
                    'type'            => 'date_time_picker',
                    'display_format'  => 'Y/m/d H:i',
                    'return_format'   => 'U',
                    'first_day'       => 6,
                ],

                // ══════════════════════════════════════════
                //  Tab 5: Top Banner (اطلاعیه)
                // ══════════════════════════════════════════
                [
                    'key'   => 'field_tab_banner',
                    'type'  => 'tab',
                    'label' => '📢 بنر اطلاعیه',
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'banner_enabled',
                    'label'         => 'نمایش بنر اطلاعیه',
                    'name'          => self::KEY_PREFIX . 'banner_enabled',
                    'type'          => 'true_false',
                    'default_value' => 0,
                    'ui'            => 1,
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'banner_type',
                    'label'         => 'نوع',
                    'name'          => self::KEY_PREFIX . 'banner_type',
                    'type'          => 'select',
                    'choices'       => [
                        'info'    => 'اطلاع‌رسانی (آبی)',
                        'warning' => 'هشدار (زرد)',
                        'error'   => 'خطا (قرمز)',
                        'success' => 'موفقیت (سبز)',
                    ],
                    'default_value' => 'info',
                    'conditional_logic' => [
                        [['field' => 'field_' . self::KEY_PREFIX . 'banner_enabled', 'operator' => '==', 'value' => '1']],
                    ],
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'banner_text',
                    'label'         => 'متن',
                    'name'          => self::KEY_PREFIX . 'banner_text',
                    'type'          => 'textarea',
                    'rows'          => 2,
                    'maxlength'     => 200,
                    'conditional_logic' => [
                        [['field' => 'field_' . self::KEY_PREFIX . 'banner_enabled', 'operator' => '==', 'value' => '1']],
                    ],
                ],
                [
                    'key'           => 'field_' . self::KEY_PREFIX . 'banner_dismissible',
                    'label'         => 'قابل بستن؟',
                    'name'          => self::KEY_PREFIX . 'banner_dismissible',
                    'type'          => 'true_false',
                    'default_value' => 1,
                    'ui'            => 1,
                    'conditional_logic' => [
                        [['field' => 'field_' . self::KEY_PREFIX . 'banner_enabled', 'operator' => '==', 'value' => '1']],
                    ],
                ],

                // ══════════════════════════════════════════
                //  ⭐ Tab 6: Banners (چندتایی با placement)
                // ══════════════════════════════════════════
                [
                    'key'   => 'field_tab_banners',
                    'type'  => 'tab',
                    'label' => '🖼️ بنرهای اسلایدر',
                ],
                [
                    'key'          => 'field_' . self::KEY_PREFIX . 'banners',
                    'label'        => 'بنرهای اپ',
                    'name'         => self::KEY_PREFIX . 'banners',
                    'type'         => 'repeater',
                    'instructions' => 'بنرهای اپ در محل‌های مختلف. هر بنر یک تصویر، لینک و محل نمایش دارد. ابعاد پیشنهادی: 960×540 پیکسل (نسبت 16:9)، فرمت WebP یا JPG. ترتیب بنرها در داخل یک placement، از بالا به پایین است. توضیح اضافی (Caption) و عنوان فرعی (Accent) فقط در صفحه ورود (login) روی تصویر overlay می‌شوند.',
                    'min'          => 0,
                    'max'          => 30,
                    'layout'       => 'block',
                    'button_label' => '+ افزودن بنر جدید',
                    'sub_fields'   => [
                        [
                            'key'           => 'field_' . self::KEY_PREFIX . 'banner_image',
                            'label'         => 'تصویر',
                            'name'          => 'image',
                            'type'          => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'medium',
                            'mime_types'    => 'jpg,jpeg,png,webp',
                            'required'      => 1,
                            'wrapper'       => ['width' => '30'],
                        ],
                        [
                            'key'          => 'field_' . self::KEY_PREFIX . 'banner_title',
                            'label'        => 'عنوان (alt / تیتر)',
                            'name'         => 'title',
                            'type'         => 'text',
                            'instructions' => 'برای اسلایدر login: تیتر اصلی روی تصویر. برای بقیه: متن جایگزین (alt) تصویر.',
                            'placeholder'  => 'تعمیر آنلاین',
                            'maxlength'    => 100,
                            'wrapper'      => ['width' => '35'],
                        ],
                        [
                            'key'           => 'field_' . self::KEY_PREFIX . 'banner_link',
                            'label'         => 'لینک',
                            'name'          => 'link',
                            'type'          => 'text',
                            'instructions'  => 'آدرس داخلی مثل /orders/new (اسلایدر login نیازی به لینک ندارد)',
                            'placeholder'   => '/orders/new',
                            'wrapper'       => ['width' => '35'],
                        ],
                        [
                            'key'           => 'field_' . self::KEY_PREFIX . 'banner_placement',
                            'label'         => 'محل نمایش',
                            'name'          => 'placement',
                            'type'          => 'select',
                            'instructions'  => 'این بنر در کدام صفحه نمایش داده شود؟',
                            'choices'       => self::getPlacements(),
                            'default_value' => 'home_top',
                            'required'      => 1,
                            'wrapper'       => ['width' => '50'],
                        ],
                        [
                            'key'           => 'field_' . self::KEY_PREFIX . 'banner_order',
                            'label'         => 'ترتیب',
                            'name'          => 'order',
                            'type'          => 'number',
                            'instructions'  => 'عدد کوچکتر = اول نمایش',
                            'default_value' => 10,
                            'min'           => 0,
                            'max'           => 999,
                            'wrapper'       => ['width' => '25'],
                        ],
                        [
                            'key'           => 'field_' . self::KEY_PREFIX . 'banner_active',
                            'label'         => 'فعال',
                            'name'          => 'active',
                            'type'          => 'true_false',
                            'default_value' => 1,
                            'ui'            => 1,
                            'ui_on_text'    => 'فعال',
                            'ui_off_text'   => 'مخفی',
                            'wrapper'       => ['width' => '25'],
                        ],

                        // ══════════════════════════════════════════
                        // 🆕 فیلدهای overlay — فقط برای placement=login
                        //    در سایر placements پنهان می‌مانند
                        // ══════════════════════════════════════════
                        [
                            'key'           => 'field_' . self::KEY_PREFIX . 'banner_accent',
                            'label'         => 'تیتر فرعی (Accent)',
                            'name'          => 'accent',
                            'type'          => 'text',
                            'instructions'  => 'فقط در اسلایدر صفحه ورود — تیتر دوم رنگی روی تصویر.',
                            'placeholder'   => 'لوازم خانگی',
                            'maxlength'     => 60,
                            'wrapper'       => ['width' => '50'],
                            'conditional_logic' => [
                                [
                                    [
                                        'field'    => 'field_' . self::KEY_PREFIX . 'banner_placement',
                                        'operator' => '==',
                                        'value'    => 'login',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'key'           => 'field_' . self::KEY_PREFIX . 'banner_caption',
                            'label'         => 'توضیح (Caption)',
                            'name'          => 'caption',
                            'type'          => 'textarea',
                            'instructions'  => 'فقط در اسلایدر صفحه ورود — توضیح زیر تیتر روی تصویر.',
                            'placeholder'   => 'از بین بهترین تعمیرکاران مجاز، آنلاین سفارش بده.',
                            'rows'          => 2,
                            'maxlength'     => 200,
                            'wrapper'       => ['width' => '50'],
                            'conditional_logic' => [
                                [
                                    [
                                        'field'    => 'field_' . self::KEY_PREFIX . 'banner_placement',
                                        'operator' => '==',
                                        'value'    => 'login',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            'location' => [
                [
                    [
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => self::PAGE_SLUG,
                    ],
                ],
            ],

            'menu_order'      => 0,
            'position'        => 'normal',
            'style'           => 'default',
            'label_placement' => 'top',
            'active'          => true,
        ]);
    }

    public static function showAcfMissingNotice(): void
    {
?>
        <div class="notice notice-error">
            <p>
                <strong>تعمیرآنلاین:</strong>
                ماژول «کنترل اپ» نیاز به ACF Pro دارد.
            </p>
        </div>
<?php
    }
}
