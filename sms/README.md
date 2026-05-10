# Rehub SMS Service

سرویس مدیریت پیامک و OTP برای تم Rehub — پشتیبانی از **Kavenegar** و **MrOTP** (USSD / SMS / Voice)

---

## فهرست مطالب

- [معرفی](#معرفی)
- [ساختار فایل‌ها](#ساختار-فایلها)
- [نصب و راه‌اندازی](#نصب-و-راهاندازی)
- [تنظیمات پنل ادمین](#تنظیمات-پنل-ادمین)
- [استفاده — برای مصرف‌کننده سرویس](#استفاده--برای-مصرفکننده-سرویس)
  - [ارسال SMS ساده](#ارسال-sms-ساده)
  - [ارسال OTP](#ارسال-otp)
  - [تأیید OTP](#تأیید-otp)
  - [OTP از طریق USSD](#otp-از-طریق-ussd)
  - [OTP از طریق Voice](#otp-از-طریق-voice)
  - [ارسال SMS پترن (MrOTP)](#ارسال-sms-پترن-mrotp)
  - [بررسی موجودی حساب](#بررسی-موجودی-حساب)
  - [دریافت کد USSD](#دریافت-کد-ussd)
- [Value Objects — خروجی متدها](#value-objects--خروجی-متدها)
  - [SendResult](#sendresult)
  - [OtpResult](#otpresult)
  - [VerifyResult](#verifyresult)
- [مدیریت خطاها](#مدیریت-خطاها)
  - [انواع Exception](#انواع-exception)
  - [کدهای خطای MrOTP](#کدهای-خطای-mrotp)
- [مقایسه پروایدرها](#مقایسه-پروایدرها)
- [گزینه‌های WordPress](#گزینههای-wordpress)
- [توسعه — افزودن پروایدر جدید](#توسعه--افزودن-پروایدر-جدید)
- [سازگاری با کد قدیمی](#سازگاری-با-کد-قدیمی)
- [تست در پنل ادمین](#تست-در-پنل-ادمین)

---

## معرفی

این سرویس یک لایه انتزاعی روی پروایدرهای پیامک فراهم می‌کند. تمام عملیات از طریق یک Facade واحد (`Sms::getInstance()`) در دسترس است و تغییر پروایدر نیازی به تغییر کد ندارد.

**ویژگی‌های اصلی:**
- پشتیبانی از دو پروایدر **Kavenegar** و **MrOTP** به صورت همزمان
- تشخیص خودکار بهترین پروایدر برای OTP (MrOTP اولویت دارد چون server-side verify دارد)
- خروجی typed با Value Objects (نه آرایه خام)
- Exception های معنادار به جای بررسی دستی کدهای خطا
- HTTP layer یکپارچه با timeout و error handling
- Singleton با امکان reset (مناسب برای تست)
- قابل توسعه با پروایدر دلخواه از طریق WordPress action hook

---

## ساختار فایل‌ها

```
core/services/Sms/
│
├── sms.php                          ← entry point — اینجا require کنید
├── Sms.php                          ← Facade / Manager اصلی
├── AbstractSmsProvider.php          ← کلاس پایه با HTTP helper مشترک
│
├── Contracts/
│   ├── SmsProviderInterface.php     ← قرارداد تمام پروایدرها
│   └── Results.php                  ← Value Objects: SendResult, OtpResult, VerifyResult
│
├── Exceptions/
│   └── SmsExceptions.php            ← Exception های typed
│
├── Kavenegar/
│   └── KavenegarProvider.php        ← پروایدر Kavenegar
│
├── MrOtp/
│   └── MrOtpProvider.php            ← پروایدر MrOTP (USSD/SMS/Voice)
│
└── admin-sms.php                    ← صفحات ادمین WordPress
```

---

## نصب و راه‌اندازی

### ۱. اضافه کردن به functions.php

```php
require_once get_template_directory() . '/core/services/Sms/sms.php';
```

فقط همین یک خط کافیست — تمام فایل‌ها داخل `sms.php` بارگذاری می‌شوند.

### ۲. وارد کردن API Key از پنل ادمین

به مسیر **WordPress Admin → Settings → مدیریت SMS** بروید و:

- برای Kavenegar: API Key خود را از [app.kavenegar.com](https://app.kavenegar.com) وارد کنید
- برای MrOTP: API Key خود را از [my.mrotp.ir](https://my.mrotp.ir) وارد کنید

### ۳. پروایدر پیش‌فرض را انتخاب کنید

در صفحه تنظیمات، `پروایدر پیش‌فرض` را مشخص کنید. برای سیستم OTP، حتی اگر پروایدر پیش‌فرض Kavenegar باشد، MrOTP اولویت خواهد داشت (چون verify سمت سرور دارد).

---

## تنظیمات پنل ادمین

| مسیر | توضیح |
|------|-------|
| **Settings → مدیریت SMS** | تنظیم API Key ها، زمان اعتبار OTP، تعداد رقم |
| **Tools → تست SMS** | ارسال SMS آزمایشی، تست OTP، بررسی موجودی |

### گزینه‌های قابل تنظیم

| گزینه | پیش‌فرض | توضیح |
|-------|---------|-------|
| پروایدر پیش‌فرض | `kavenegar` | پروایدر مورد استفاده برای SMS ساده |
| مدت اعتبار OTP | `5` دقیقه | بین ۱ تا ۱۴۴۰ دقیقه |
| تعداد رقم OTP تصادفی | `6` | بین ۳ تا ۸ رقم |
| شناسه قالب پترن | — | شناسه عددی قالب تأیید‌شده در MrOTP |
| نام قالب Kavenegar | — | نام قالب verifyLookup در Kavenegar |

---

## استفاده — برای مصرف‌کننده سرویس

همیشه با `getInstance()` شروع کنید:

```php
use Rehub\Core\Services\Sms\Sms;

$sms = Sms::getInstance();
```

---

### ارسال SMS ساده

```php
$sms = Sms::getInstance();

// ارسال به یک شماره
$results = $sms->send('09121112233', 'پیام آزمایشی');

// ارسال به چند شماره
$results = $sms->send(['09121112233', '09351112233'], 'پیام گروهی');

// با شماره فرستنده مشخص
$results = $sms->send('09121112233', 'پیام شما', '2000660110');

// بررسی نتیجه
foreach ($results as $result) {
    if ($result->success) {
        echo "ارسال شد — ID: {$result->messageId}";
    } else {
        echo "خطا: {$result->statusMessage} (کد {$result->statusCode})";
    }
}
```

> **توجه:** برای MrOTP، ارسال SMS ساده نیاز به `patternId` دارد (در تنظیمات ادمین). در صورت نیاز به SMS بدون قالب از Kavenegar استفاده کنید.

---

### ارسال OTP

سرویس به صورت خودکار از بهترین پروایدر موجود استفاده می‌کند — اگر MrOTP کانفیگ شده باشد اولویت دارد.

```php
$sms = Sms::getInstance();

// ساده‌ترین حالت — کد تصادفی، روش پیش‌فرض (معمولاً SMS)
$result = $sms->sendOtp('09121112233');

// با روش مشخص
$result = $sms->sendOtp('09121112233', '', '', 'SMS');
$result = $sms->sendOtp('09121112233', '', '', 'USSD');
$result = $sms->sendOtp('09121112233', '', '', 'VOICE');

// با کد دلخواه (به جای تصادفی)
$result = $sms->sendOtp('09121112233', '48291', '', 'SMS');

// بررسی نتیجه
if ($result->success) {
    // اگر MrOTP استفاده شده، OTP تولید‌شده در $result->otp است
    echo "کد OTP: {$result->otp}";
    
    // اگر از USSD استفاده شده، کد شماره‌گیری نیز موجود است
    if ($result->ussdCode) {
        echo "کد USSD: {$result->ussdCode}";
    }
} else {
    echo "خطا [{$result->errorCode}]: {$result->message}";
}
```

---

### تأیید OTP

```php
$sms = Sms::getInstance();

// کدی که کاربر وارد کرده
$userInput = '48291';

$result = $sms->verifyOtp('09121112233', $userInput);

if ($result->accepted) {
    // ✅ کد معتبر است — کاربر را لاگین کنید
    echo "تأیید شد برای: {$result->mobile}";
} else {
    // ❌ کد نامعتبر یا منقضی شده
    echo "خطا [{$result->errorCode}]: {$result->message}";
}
```

> **مهم:** `verifyOtp` فقط با MrOTP کار می‌کند. Kavenegar server-side verify ندارد — باید کد را خودتان در دیتابیس ذخیره و مقایسه کنید.

---

### OTP از طریق USSD

در این روش، کاربر کدی را شماره‌گیری می‌کند تا OTP خود را ببیند:

```php
$sms = Sms::getInstance();

$result = $sms->sendUssdOtp('09121112233');

if ($result->success) {
    // کد USSD برای نمایش به کاربر
    echo "برای دریافت کد یکبارمصرف، شماره {$result->ussdCode} را شماره‌گیری کنید";
    // مثال: *6655*100200300#
}
```

---

### OTP از طریق Voice

```php
$sms = Sms::getInstance();

$result = $sms->sendVoiceOtp('09121112233');

if ($result->success) {
    echo "کد OTP از طریق تماس صوتی ارسال شد.";
    // کد در $result->otp موجود است
}
```

---

### ارسال SMS پترن (MrOTP)

برای SMS‌های اطلاع‌رسانی با متن پویا، از قالب‌های آماده MrOTP استفاده کنید:

```php
// فرض کنید قالب این است:
// "{0} {1} گرامی، سفارش شما با کد {2} تحویل پست شد."

use Rehub\Core\Services\Sms\MrOtp\MrOtpProvider;

/** @var MrOtpProvider $mrOtp */
$mrOtp = Sms::getInstance()->provider('mrotp');

// پارامترها با ; جدا می‌شوند، ترتیب مهم است
$mrOtp->sendPatternSms(
    '09121112233',  // شماره گیرنده
    42,             // شناسه قالب (از پنل MrOTP)
    'علی;رضایی;98765432'  // {0}=علی  {1}=رضایی  {2}=98765432
);
```

**محدودیت‌ها:**
- حداکثر ۱۰ پارامتر
- هر پارامتر حداکثر ۱۵ کاراکتر (خطای کد `-21`)
- قالب باید توسط مدیر MrOTP تأیید شده باشد

---

### بررسی موجودی حساب

```php
$sms = Sms::getInstance();

// موجودی پروایدر پیش‌فرض
$credit = $sms->getCredit();
echo number_format($credit) . ' ریال';

// موجودی پروایدر مشخص
$kavCredit = $sms->getCredit('kavenegar');
$mrCredit  = $sms->getCredit('mrotp');
```

> سیستم به صورت خودکار وقتی موجودی MrOTP زیر ۱۰۰,۰۰۰ ریال برسد، یک پیامک هشدار ارسال می‌کند.

---

### دریافت کد USSD

```php
$sms = Sms::getInstance();

$ussd = $sms->getUssdCode();
// مثال: *6655*100200300#

// نمایش در صفحه ورود:
echo "برای دریافت کد یکبارمصرف: <strong>{$ussd}</strong> را شماره‌گیری کنید";
```

---

### استفاده مستقیم از پروایدر خاص

```php
use Rehub\Core\Services\Sms\MrOtp\MrOtpProvider;
use Rehub\Core\Services\Sms\Kavenegar\KavenegarProvider;

$sms = Sms::getInstance();

// دسترسی مستقیم به MrOTP
/** @var MrOtpProvider $mrOtp */
$mrOtp = $sms->provider('mrotp');

// ثبت OTP با تنظیمات دقیق
$result = $mrOtp->setOtp('09121112233', '48291', 10, MrOtpProvider::TYPE_USSD);
$result = $mrOtp->setRandomOtp('09121112233', 6, 5, MrOtpProvider::TYPE_SMS);
$ussd   = $mrOtp->getUssdCode();

// دسترسی مستقیم به Kavenegar
/** @var KavenegarProvider $kav */
$kav = $sms->provider('kavenegar');

$statuses = $kav->getStatus(['msg-id-123', 'msg-id-456']);
$kav->cancel('msg-id-123');

// ارسال bulk
$kav->sendArray(
    ['09121112233', '09351112233'],
    ['پیام اول', 'پیام دوم'],
    ['2000660110', '2000660110']
);
```

---

## Value Objects — خروجی متدها

تمام متدها به جای آرایه خام، Value Object برمی‌گردانند.

### SendResult

خروجی `send()` — یک آرایه از `SendResult`:

```php
$result->success        // bool   — آیا ارسال موفق بود؟
$result->messageId      // string — شناسه پیام برای پیگیری
$result->receptor       // string — شماره گیرنده
$result->statusCode     // int    — کد وضعیت پروایدر
$result->statusMessage  // string — توضیح وضعیت
```

### OtpResult

خروجی `sendOtp()`, `sendUssdOtp()`, `sendVoiceOtp()`, `setOtp()`, `setRandomOtp()`:

```php
$result->success    // bool   — آیا OTP ثبت/ارسال شد؟
$result->otp        // string — کد OTP (خالی اگر پروایدر نداد)
$result->ussdCode   // string — کد USSD برای شماره‌گیری، مثلاً *6655*100200300#
$result->errorCode  // int    — کد خطا (0 = موفق)
$result->message    // string — پیام توضیحی
$result->provider   // string — نام پروایدر ('mrotp' یا 'kavenegar')
```

### VerifyResult

خروجی `verifyOtp()`:

```php
$result->accepted   // bool   — آیا OTP معتبر است؟ (true = تأیید شد)
$result->mobile     // string — شماره موبایل
$result->otp        // string — کد OTP تأیید‌شده
$result->errorCode  // int    — کد خطا (0 = موفق)
$result->message    // string — پیام توضیحی
$result->provider   // string — نام پروایدر
```

---

## مدیریت خطاها

### انواع Exception

همه Exception ها از `SmsException` ارث می‌برند. برای catch کردن همه خطاهای SMS:

```php
use Rehub\Core\Services\Sms\Exceptions\SmsException;
use Rehub\Core\Services\Sms\Exceptions\OtpException;
use Rehub\Core\Services\Sms\Exceptions\ProviderNotFoundException;
use Rehub\Core\Services\Sms\Exceptions\ProviderRequestException;
use Rehub\Core\Services\Sms\Exceptions\InvalidApiKeyException;
use Rehub\Core\Services\Sms\Exceptions\InsufficientCreditException;

try {
    $result = $sms->sendOtp('09121112233');
} catch (OtpException $e) {
    // خطای خاص OTP از پروایدر
    $code    = $e->getProviderCode(); // کد عددی خطای پروایدر
    $message = $e->getMessage();
    error_log("OTP failed [{$code}]: {$message}");

} catch (ProviderRequestException $e) {
    // خطای HTTP یا شبکه
    error_log("HTTP error from {$e->getProvider()}: {$e->getMessage()}");

} catch (ProviderNotFoundException $e) {
    // پروایدر درخواست‌شده ثبت نشده
    error_log($e->getMessage());

} catch (SmsException $e) {
    // هر خطای دیگر SMS
    error_log("SMS error: {$e->getMessage()}");
}
```

**ساده‌ترین رویکرد** — اگر نیازی به تفکیک خطاها ندارید:

```php
try {
    $result = $sms->sendOtp('09121112233');
    if (!$result->success) {
        // خطای نرم — OTP ثبت نشد اما exception نداد
        echo $result->message;
    }
} catch (SmsException $e) {
    // خطای سخت — exception
    echo 'سرویس پیامک موقتاً در دسترس نیست.';
}
```

### کدهای خطای MrOTP

| کد | توضیح | راه‌حل |
|----|-------|--------|
| `-1` | API Key نامعتبر | کلید را در تنظیمات بررسی کنید |
| `-2` | شماره موبایل نامعتبر | فرمت `09xxxxxxxxx` استفاده کنید |
| `-3` | OTP نامعتبر | بین ۳ تا ۸ رقم عددی باشد |
| `-4` | زمان اعتبار نامعتبر | بین ۱ تا ۱۴۴۰ دقیقه |
| `-6` | سرویس غیرفعال | با پشتیبانی MrOTP تماس بگیرید |
| `-7` | موجودی ناکافی | حساب MrOTP را شارژ کنید |
| `-10` | خطای نامشخص | لاگ را بررسی کنید |
| `-11` | IP مغایرت دارد | IP سرور را در پنل MrOTP ثبت کنید |
| `-12` | OTP نامعتبر یا منقضی | کاربر باید کد جدید درخواست دهد |
| `-13` | خطای احراز هویت | API Key را تجدید کنید |
| `-14` | کاربر اشتراک‌زدایی کرده | کاربر باید `12` را به سرشماره SMS کند |
| `-16` | طول OTP نامعتبر | بین ۳ تا ۸ رقم |
| `-17` | قالب پیدا نشد | شناسه patternId را بررسی کنید |
| `-18` | خطا در متن قالب یا پارامترها | تعداد و طول پارامترها را چک کنید |
| `-19` | قالب تأیید نشده | از پنل MrOTP قالب را تأیید کنید |
| `-20` | سرویس موقتاً قطع | مجدداً تلاش کنید |
| `-21` | پارامتر بیش از ۱۵ کاراکتر | طول پارامترها را کاهش دهید |
| `-22` | پاسخ اپراتور SMS دریافت نشد | مجدداً تلاش کنید |
| `-23` | پاسخ اپراتور Voice دریافت نشد | مجدداً تلاش کنید |

---

## مقایسه پروایدرها

| قابلیت | Kavenegar | MrOTP |
|---------|-----------|-------|
| SMS ساده | ✅ | ✅ (نیاز به patternId) |
| SMS گروهی (bulk) | ✅ | ⚠️ یک به یک |
| OTP از طریق SMS | ✅ | ✅ |
| OTP از طریق USSD | ❌ | ✅ |
| OTP از طریق Voice | ❌ | ✅ |
| Server-side Verify | ❌ | ✅ |
| SMS پترن/قالب | ✅ | ✅ |
| بررسی موجودی | ✅ | ✅ |
| دریافت وضعیت پیام | ✅ | ❌ |
| لغو پیام زمان‌بندی‌شده | ✅ | ❌ |

**توصیه:** برای سیستم احراز هویت (Login/Register) از **MrOTP** استفاده کنید. برای SMS‌های اطلاع‌رسانی و بازاریابی از **Kavenegar**.

---

## گزینه‌های WordPress

تمام تنظیمات در جدول `wp_options` ذخیره می‌شوند:

| کلید گزینه | نوع | پیش‌فرض | توضیح |
|------------|-----|---------|-------|
| `rehub_sms_active_provider` | string | `kavenegar` | پروایدر پیش‌فرض |
| `rehub_sms_api_key` | string | — | API Key کاونگار |
| `rehub_sms_default_sender` | string | — | شماره فرستنده پیش‌فرض کاونگار |
| `rehub_sms_otp_template` | string | — | نام قالب OTP در کاونگار |
| `rehub_mrotp_api_key` | string | — | API Key مروتی‌پی |
| `rehub_mrotp_valid_time` | int | `5` | مدت اعتبار OTP (دقیقه) |
| `rehub_mrotp_otp_length` | int | `6` | تعداد رقم OTP تصادفی |
| `rehub_mrotp_pattern_id` | int | `0` | شناسه قالب پترن |
| `rehub_mrotp_default_type` | string | `USSD` | روش پیش‌فرض OTP |

---

## توسعه — افزودن پروایدر جدید

### ۱. ساخت کلاس پروایدر

```php
namespace Rehub\Core\Services\Sms\MyProvider;

use Rehub\Core\Services\Sms\AbstractSmsProvider;
use Rehub\Core\Services\Sms\Contracts\{OtpResult, SendResult, VerifyResult};

final class MyProvider extends AbstractSmsProvider
{
    public function getName(): string
    {
        return 'myprovider';
    }

    public function supportsOtp(): bool
    {
        return true; // یا false
    }

    public function send(string|array $receptor, string $message, ?string $sender = null): array
    {
        $receptors = is_array($receptor) ? $receptor : [$receptor];
        $results   = [];

        foreach ($receptors as $mobile) {
            $this->assertValidMobile($mobile); // از کلاس پایه

            // فراخوانی API پروایدر با httpPost() از کلاس پایه
            $body = $this->httpPost('https://api.myprovider.com/send', [
                'apikey'  => $this->apiKey,
                'to'      => $mobile,
                'message' => $message,
            ]);

            $results[] = ($body['status'] === 'ok')
                ? SendResult::ok($body['id'], $mobile)
                : SendResult::fail($mobile, $body['code'], $body['error']);
        }

        return $results;
    }

    public function sendOtp(string $mobile, string $token, string $template, string $type = 'sms'): OtpResult
    {
        // پیاده‌سازی OTP پروایدر شما
        // ...
        return OtpResult::ok($token, '', $this->getName());
    }

    public function verifyOtp(string $mobile, string $otp): VerifyResult
    {
        // اگر پروایدر verify ندارد:
        throw new \LogicException('MyProvider does not support server-side OTP verification.');
    }

    public function getCredit(): float
    {
        $body = $this->httpPost('https://api.myprovider.com/credit', [
            'apikey' => $this->apiKey,
        ]);
        return (float) ($body['balance'] ?? 0.0);
    }
}
```

### ۲. ثبت پروایدر از طریق Action Hook

```php
// در functions.php یا یک plugin
add_action('rehub_sms_register_providers', function (\Rehub\Core\Services\Sms\Sms $sms) {
    $apiKey = get_option('my_provider_api_key', '');
    if ($apiKey) {
        $sms->register('myprovider', new \Rehub\Core\Services\Sms\MyProvider\MyProvider($apiKey));
    }
});
```

### ۳. استفاده

```php
$sms = Sms::getInstance();

// تغییر پروایدر پیش‌فرض در runtime
$sms->setDefaultProvider('myprovider');

// یا مستقیم
$sms->provider('myprovider')->send('09121112233', 'پیام');
```

### متدهای کلاس پایه `AbstractSmsProvider`

| متد | دسترسی | توضیح |
|-----|--------|-------|
| `httpPost(url, body, timeout)` | `protected` | POST request با JSON decode |
| `httpGet(url, query, timeout)` | `protected` | GET request با query string |
| `assertValidMobile(mobile)` | `protected` | اعتبارسنجی فرمت `09xxxxxxxxx` |
| `$this->apiKey` | `protected` | کلید API |

---

## سازگاری با کد قدیمی

کدهایی که از کلاس قدیمی استفاده می‌کنند بدون تغییر کار می‌کنند:

```php
// کد قدیمی — همچنان کار می‌کند
use Rehub\Core\Services\Sms\Kavenegar\Kavenegar;
$kav = new Kavenegar();
$kav->verifyLookup('09121112233', '12345', 'otp_template');
```

این از طریق `class_alias` در `sms.php` پشتیبانی می‌شود. در نسخه‌های آینده این alias حذف خواهد شد — **توصیه می‌شود کدهای قدیمی را به API جدید مهاجرت دهید.**

**نمونه مهاجرت:**

```php
// ❌ قدیمی
$kav = new Kavenegar();
$res = $kav->verifyLookup($mobile, $token, $template);

// ✅ جدید
$result = Sms::getInstance()->sendOtp($mobile, $token, $template, 'sms');
if ($result->success) { /* ... */ }
```

---

## تست در پنل ادمین

به مسیر **WordPress Admin → Tools → تست SMS** بروید:

| نوع تست | توضیح |
|---------|-------|
| **SMS ساده** | یک پیامک دلخواه به شماره مورد نظر ارسال می‌کند |
| **ارسال OTP** | OTP می‌فرستد و کد تولیدشده را نمایش می‌دهد |
| **تأیید OTP** | کد وارد‌شده را با MrOTP verify می‌کند |
| **بررسی موجودی** | موجودی هر دو پروایدر را نمایش می‌دهد |

---

## یادداشت‌های مهم

**درباره OTP و USSD:**
در روش USSD، کد OTP در سرور MrOTP ذخیره می‌شود و کاربر با شماره‌گیری کد USSD آن را مشاهده می‌کند. شما نیازی به ذخیره‌سازی کد در دیتابیس خودتان ندارید — فقط `verifyOtp()` را صدا بزنید.

**درباره singleton reset:**
بعد از تغییر API Key در پنل ادمین، singleton به صورت خودکار reset می‌شود تا کلید جدید لود شود. اگر در کد خودتان option را تغییر دادید، باید `Sms::resetInstance()` را صدا بزنید.

**درباره logging:**
در حالت `WP_DEBUG = true`، خطاهای HTTP در `wp-content/debug.log` ثبت می‌شوند. برای محیط production از یک logger مناسب استفاده کنید.
