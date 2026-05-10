<?php

namespace Tamir\Rest\Controllers;

use Rehub\Rest\Controller;
use Rehub\Rest\Response;
use WP_REST_Request;

/**
 * LocationController
 *
 * لیست استان‌ها و شهرهای ثبت‌شده در taxonomy وردپرس
 * سینک‌شده با پنل ادمین — هر تغییر در ادمین فوری در API منعکس می‌شود
 *
 * GET /tamir/v1/locations/states       — لیست استان‌ها (taxonomy: state)
 * GET /tamir/v1/locations/cities       — لیست شهرها (taxonomy: city)
 * GET /tamir/v1/locations/cities?state_id=X  — فیلتر شهر بر اساس استان (اگر hierarchical)
 */
final class LocationController extends Controller
{
    public function authorize(WP_REST_Request $r): bool
    {
        return true; // public endpoint
    }

    public function rules(): array
    {
        return [];
    }
    public function messages(): array
    {
        return [];
    }

    // ══════════════════════════════════════════════════════════
    //  GET /tamir/v1/locations/states
    //  لیست استان‌ها از taxonomy 'state'
    // ══════════════════════════════════════════════════════════
    public function states(WP_REST_Request $r)
    {
        $terms = get_terms([
            'taxonomy'   => 'state',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'number'     => 0, // همه
        ]);

        if (is_wp_error($terms)) {
            // taxonomy ثبت نشده یا خالی است — fallback به داده‌های پیش‌فرض ایران
            return Response::ok([
                'success' => true,
                'data'    => $this->defaultStates(),
                'source'  => 'default',
            ]);
        }

        if (empty($terms)) {
            // taxonomy خالی است — seed داده‌های پیش‌فرض و برگردان
            $defaults = $this->defaultStates();
            $this->seedTaxonomy('state', $defaults);
            return Response::ok([
                'success' => true,
                'data'    => $defaults,
                'source'  => 'seeded',
            ]);
        }

        $data = array_map(fn(\WP_Term $t) => [
            'id'   => $t->term_id,
            'name' => $t->name,
            'slug' => $t->slug,
        ], $terms);

        return Response::ok([
            'success' => true,
            'data'    => array_values($data),
            'source'  => 'taxonomy',
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  GET /tamir/v1/locations/cities?state_id=X
    //  لیست شهرها از taxonomy 'city'
    // ══════════════════════════════════════════════════════════
    public function cities(WP_REST_Request $r)
    {
        $stateId = absint($r->get_param('state_id') ?? 0);

        $args = [
            'taxonomy'   => 'city',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'number'     => 0,
        ];

        // فیلتر بر اساس استان (اگر taxonomy city را به state وصل کرده‌اید)
        if ($stateId > 0) {
            $args['meta_query'] = [
                ['key' => 'state_id', 'value' => $stateId, 'compare' => '='],
            ];
        }

        $terms = get_terms($args);

        if (is_wp_error($terms) || empty($terms)) {
            $defaults = $this->defaultCities();
            if (is_wp_error($terms)) {
                $this->seedTaxonomy('city', $defaults);
            }
            // فیلتر پیش‌فرض
            if ($stateId > 0) {
                $defaults = array_values(array_filter(
                    $defaults,
                    fn($c) => ($c['state_id'] ?? 0) === $stateId
                ));
            }
            return Response::ok([
                'success' => true,
                'data'    => $defaults ?: $this->defaultCities(),
                'source'  => is_wp_error($terms) ? 'seeded' : 'default',
            ]);
        }

        $data = array_map(fn(\WP_Term $t) => [
            'id'       => $t->term_id,
            'name'     => $t->name,
            'slug'     => $t->slug,
            'state_id' => (int) get_term_meta($t->term_id, 'state_id', true),
        ], $terms);

        return Response::ok([
            'success' => true,
            'data'    => array_values($data),
            'source'  => 'taxonomy',
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  Private — Seed taxonomy از داده‌های پیش‌فرض
    // ══════════════════════════════════════════════════════════
    private function seedTaxonomy(string $taxonomy, array $items): void
    {
        if (! taxonomy_exists($taxonomy)) return;

        foreach ($items as $item) {
            $existing = get_term_by('slug', $item['slug'], $taxonomy);
            if (! $existing) {
                $result = wp_insert_term($item['name'], $taxonomy, ['slug' => $item['slug']]);
                if (! is_wp_error($result) && isset($item['state_id'])) {
                    update_term_meta($result['term_id'], 'state_id', $item['state_id']);
                }
            }
        }
    }

    // ══════════════════════════════════════════════════════════
    //  Default Data — استان‌های ایران
    // ══════════════════════════════════════════════════════════
    private function defaultStates(): array
    {
        return [
            ['id' => 1,  'name' => 'تهران',           'slug' => 'tehran'],
            ['id' => 2,  'name' => 'البرز',            'slug' => 'alborz'],
            ['id' => 3,  'name' => 'اصفهان',           'slug' => 'isfahan'],
            ['id' => 4,  'name' => 'فارس',             'slug' => 'fars'],
            ['id' => 5,  'name' => 'خراسان رضوی',     'slug' => 'khorasan-razavi'],
            ['id' => 6,  'name' => 'آذربایجان شرقی',  'slug' => 'east-azerbaijan'],
            ['id' => 7,  'name' => 'آذربایجان غربی',  'slug' => 'west-azerbaijan'],
            ['id' => 8,  'name' => 'مازندران',         'slug' => 'mazandaran'],
            ['id' => 9,  'name' => 'گیلان',            'slug' => 'Gilan'],
            ['id' => 10, 'name' => 'خوزستان',          'slug' => 'khuzestan'],
            ['id' => 11, 'name' => 'کرمان',            'slug' => 'kerman'],
            ['id' => 12, 'name' => 'سیستان و بلوچستان', 'slug' => 'sistan-baluchestan'],
            ['id' => 13, 'name' => 'هرمزگان',          'slug' => 'hormozgan'],
            ['id' => 14, 'name' => 'لرستان',           'slug' => 'lorestan'],
            ['id' => 15, 'name' => 'کرمانشاه',         'slug' => 'kermanshah'],
            ['id' => 16, 'name' => 'گلستان',           'slug' => 'golestan'],
            ['id' => 17, 'name' => 'همدان',            'slug' => 'hamadan'],
            ['id' => 18, 'name' => 'قم',               'slug' => 'qom'],
            ['id' => 19, 'name' => 'قزوین',            'slug' => 'qazvin'],
            ['id' => 20, 'name' => 'زنجان',            'slug' => 'zanjan'],
            ['id' => 21, 'name' => 'سمنان',            'slug' => 'semnan'],
            ['id' => 22, 'name' => 'یزد',              'slug' => 'yazd'],
            ['id' => 23, 'name' => 'خراسان شمالی',    'slug' => 'north-khorasan'],
            ['id' => 24, 'name' => 'خراسان جنوبی',    'slug' => 'south-khorasan'],
            ['id' => 25, 'name' => 'بوشهر',            'slug' => 'bushehr'],
            ['id' => 26, 'name' => 'چهارمحال و بختیاری', 'slug' => 'chaharmahal'],
            ['id' => 27, 'name' => 'کهگیلویه و بویراحمد', 'slug' => 'kohgiluyeh'],
            ['id' => 28, 'name' => 'اردبیل',           'slug' => 'ardabil'],
            ['id' => 29, 'name' => 'مرکزی',            'slug' => 'markazi'],
            ['id' => 30, 'name' => 'ایلام',            'slug' => 'ilam'],
            ['id' => 31, 'name' => 'کردستان',          'slug' => 'kurdistan'],
        ];
    }

    // ══════════════════════════════════════════════════════════
    //  Default Data — شهرهای اصلی (بدون state_id — برای فریم‌ورک ساده)
    // ══════════════════════════════════════════════════════════
    private function defaultCities(): array
    {
        return [
            // تهران (state_id: 1)
            ['id' => 1,  'name' => 'تهران',         'slug' => 'tehran-city',    'state_id' => 1],
            ['id' => 2,  'name' => 'شهریار',        'slug' => 'shahriar',       'state_id' => 1],
            ['id' => 3,  'name' => 'ری',             'slug' => 'rey',            'state_id' => 1],
            ['id' => 4,  'name' => 'اسلامشهر',      'slug' => 'islamshahr',     'state_id' => 1],
            ['id' => 5,  'name' => 'شمیرانات',      'slug' => 'shemiranat',     'state_id' => 1],
            ['id' => 6,  'name' => 'ورامین',        'slug' => 'varamin',        'state_id' => 1],
            // البرز (state_id: 2)
            ['id' => 7,  'name' => 'کرج',            'slug' => 'karaj',          'state_id' => 2],
            ['id' => 8,  'name' => 'فردیس',         'slug' => 'fardis',         'state_id' => 2],
            ['id' => 9,  'name' => 'نظرآباد',       'slug' => 'nazarabad',      'state_id' => 2],
            // اصفهان (state_id: 3)
            ['id' => 10, 'name' => 'اصفهان',        'slug' => 'isfahan-city',   'state_id' => 3],
            ['id' => 11, 'name' => 'کاشان',         'slug' => 'kashan',         'state_id' => 3],
            ['id' => 12, 'name' => 'خمینی‌شهر',    'slug' => 'khomeyni-shahr', 'state_id' => 3],
            // فارس (state_id: 4)
            ['id' => 13, 'name' => 'شیراز',         'slug' => 'shiraz',         'state_id' => 4],
            ['id' => 14, 'name' => 'مرودشت',        'slug' => 'marvdasht',      'state_id' => 4],
            // خراسان رضوی (state_id: 5)
            ['id' => 15, 'name' => 'مشهد',          'slug' => 'mashhad',        'state_id' => 5],
            ['id' => 16, 'name' => 'نیشابور',       'slug' => 'nishapur',       'state_id' => 5],
            // آذربایجان شرقی (state_id: 6)
            ['id' => 17, 'name' => 'تبریز',         'slug' => 'tabriz',         'state_id' => 6],
            ['id' => 18, 'name' => 'مراغه',         'slug' => 'maragheh',       'state_id' => 6],
            // مازندران (state_id: 8)
            ['id' => 19, 'name' => 'ساری',          'slug' => 'sari',           'state_id' => 8],
            ['id' => 20, 'name' => 'آمل',           'slug' => 'amol',           'state_id' => 8],
            ['id' => 21, 'name' => 'بابل',          'slug' => 'babol',          'state_id' => 8],
            // گیلان (state_id: 9)
            ['id' => 22, 'name' => 'رشت',           'slug' => 'rasht',          'state_id' => 9],
            ['id' => 23, 'name' => 'انزلی',         'slug' => 'anzali',         'state_id' => 9],
            // خوزستان (state_id: 10)
            ['id' => 24, 'name' => 'اهواز',         'slug' => 'ahvaz',          'state_id' => 10],
            ['id' => 25, 'name' => 'آبادان',        'slug' => 'abadan',         'state_id' => 10],
            // قم (state_id: 18)
            ['id' => 26, 'name' => 'قم',            'slug' => 'qom-city',       'state_id' => 18],
            // کرمانشاه (state_id: 15)
            ['id' => 27, 'name' => 'کرمانشاه',     'slug' => 'kermanshah-city', 'state_id' => 15],
        ];
    }
}
