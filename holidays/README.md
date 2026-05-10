# Tamir Holidays Module

ماژول مدیریت تعطیلات رسمی (تقویم جلالی) برای قالب Rehub.

## نصب

در فایل `functions.php` قالب خود، بعد از bootstrap کردن REST framework، این خط را اضافه کنید:

```php
require_once get_template_directory() . '/core/holidays/bootstrap.php';
```

**نکته مهم:** باید **بعد از** `core/rest/bootstrap.php` لود شود تا filter `rehub_rest_routes` در دسترس باشد.

## ساختار

```
core/holidays/
├─ bootstrap.php               # نقطه ورود
├─ HolidayPostType.php         # CPT: tamir_holiday
├─ HolidayRepository.php       # Query layer (reusable)
├─ HolidayMetaBox.php          # Admin UI + validation + date-picker جلالی
├─ HolidayAdminColumns.php     # ستون‌های لیست
├─ HolidayCacheBuster.php      # پاک‌سازی cache هنگام ویرایش/حذف
├─ HolidayRoutes.php           # ثبت route
└─ Controllers/
   └─ HolidaysController.php   # REST controller
```

## API

### GET `/wp-json/rehub/v1/tamir/holidays`

**Query params:**

- `from` (required) — `YYYY-MM-DD` (شمسی). مثال: `1405-01-01`
- `to` (required) — `YYYY-MM-DD` (شمسی). مثال: `1405-02-30`

**Response (200):**

```json
{
  "success": true,
  "data": [
    { "date": "1405-01-13", "title": "روز طبیعت" },
    { "date": "1405-02-03", "title": "شهادت امام علی (ع)" }
  ],
  "meta": {
    "from": "1405-01-01",
    "to": "1405-02-30",
    "count": 2,
    "version": 3
  }
}
```

**Error (422):**

```json
{
  "message": "Validation failed.",
  "data": { "errors": { "from": ["پارامتر from الزامی است."] } }
}
```

## مدیریت تعطیلات (Admin)

1. وارد وردپرس شو → منوی سمت راست → **تعطیلات رسمی**
2. **افزودن تعطیلی** → عنوان (مثلاً: «شهادت امام علی (ع)») + تاریخ شمسی (`1405-02-03`)
3. می‌تونی مستقیم تایپ کنی یا از دکمه «انتخاب از تقویم جلالی» استفاده کنی
4. ارقام فارسی/عربی خودکار به انگلیسی تبدیل می‌شن

## Cache

- Response ها ۱ ساعت cache می‌شن (header `X-Cache: HIT|MISS`)
- وقتی admin تعطیلی ویرایش/حذف کنه، cacheها به‌صورت خودکار پاک می‌شن
- در صورت استفاده از object-cache خارجی، `version` در `meta` قابل استفاده در کلاینت است

## اگر `rehub_rest_routes` در framework شما پشتیبانی نشده

در `core/rest/routes.php` دستی اضافه کن:

```php
'routes' => [
    // ... routes موجود
    [
        'namespace'  => 'rehub/v1',
        'route'      => '/tamir/holidays',
        'methods'    => 'GET',
        'controller' => \Tamir\Holidays\Controllers\HolidaysController::class,
        'action'     => 'index',
        'group'      => 'public',
        'middleware' => ['cache:3600'],
        'args'       => [
            'from' => ['sanitize_callback' => 'sanitize_text_field'],
            'to'   => ['sanitize_callback' => 'sanitize_text_field'],
        ],
    ],
],
```

## تست

```bash
# بدون پارامتر → 422
curl "https://yoursite.com/wp-json/rehub/v1/tamir/holidays"

# با بازه معتبر → 200
curl "https://yoursite.com/wp-json/rehub/v1/tamir/holidays?from=1405-01-01&to=1405-12-30"

# چک کردن cache header
curl -I "https://yoursite.com/wp-json/rehub/v1/tamir/holidays?from=1405-01-01&to=1405-01-30"
# → X-Cache: MISS (اولین بار) / HIT (دفعات بعد)
```

## اعتبارسنجی تاریخ شمسی

در هر دو سمت (PHP و JS admin) این قوانین چک می‌شن:

- سال: `1300..1500`
- ماه: `1..12`
- روز ماه‌های اول (۱–۶): تا ۳۱
- روز ماه‌های میانی (۷–۱۱): تا ۳۰
- روز اسفند: تا ۳۰ (کبیسه) / ۲۹ (عادی)
