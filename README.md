# Laravel Assets Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/novinvision/laravel-assets-manager.svg)](https://packagist.org/packages/novinvision/laravel-assets-manager)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

**Laravel Assets Manager** یک پکیج برای مدیریت، گروه‌بندی و ترکیب فایل‌های CSS و JavaScript در پروژه‌های Laravel است.

با استفاده از این پکیج می‌توانید فایل‌های CSS و JavaScript مورد نیاز هر صفحه را در Controller یا Blade Component مشخص کنید و سپس فایل‌های مورد نیاز را در View نهایی بارگذاری کنید.

در صورت فعال بودن قابلیت Merge، فایل‌های مربوط به هر گروه نیز می‌توانند با استفاده از [MatthiasMullie Minify](https://github.com/matthiasmullie/minify) ترکیب و Minify شوند.

---

## نصب

پکیج را با Composer نصب کنید:

```bash
composer require novinvision/laravel-assets-manager
```

سرویس‌پروایدر پکیج به صورت خودکار توسط Laravel شناسایی می‌شود و نیازی به ثبت دستی Provider نیست.

---

## انتشار Configuration

برای انتشار فایل تنظیمات پکیج، دستور زیر را اجرا کنید:

```bash
php artisan vendor:publish --tag=assets-manager
```

پس از اجرای دستور، فایل زیر در پروژه شما ایجاد خواهد شد:

```text
config/assets-manager.php
```

از این فایل می‌توانید تنظیمات مربوط به مدیریت Assetها را تغییر دهید.

> پیشنهاد می‌شود در صورت نیاز به تغییر تنظیمات، ابتدا فایل Configuration را Publish کرده و سپس تنظیمات مورد نظر را در `config/assets-manager.php` انجام دهید.

---

## Configuration

فایل تنظیمات پکیج در مسیر زیر قرار دارد:

```text
config/assets-manager.php
```

پکیج تنظیمات را با کلید زیر از Configuration دریافت می‌کند:

```php
config('assets-manager')
```

دو تنظیم اصلی که توسط سرویس‌پروایدر استفاده می‌شوند عبارت‌اند از:

### فعال کردن Merge

تنظیم:

```php
'merge' => true,
```

با فعال بودن این گزینه، پکیج پوشه‌های مورد نیاز برای فایل‌های Merge شده را در مسیر عمومی پروژه ایجاد می‌کند.

ساختار پوشه‌ها به صورت زیر خواهد بود:

```text
public/
└── ...
    ├── css/
    └── js/
```

مسیر اصلی این ساختار از مقدار زیر در Configuration خوانده می‌شود:

```php
'path' => 'assets',
```

بنابراین اگر مقدار `path` برابر `assets` باشد، ساختار نهایی به صورت زیر خواهد بود:

```text
public/assets/
├── css/
└── js/
```

پکیج در زمان اجرای Service Provider بررسی می‌کند که این پوشه‌ها وجود داشته باشند و در صورت نبودن، آن‌ها را ایجاد می‌کند.

---

## اضافه کردن فایل JavaScript

برای اضافه کردن یک فایل JavaScript می‌توانید از تابع زیر استفاده کنید:

```php
assets_add_js(public_path('dist/js/test.js'));
```

برای مثال، در یک Controller:

```php
public function index()
{
    assets_add_js(public_path('dist/js/test.js'));

    return view('test.index');
}
```

یا می‌توانید این تابع را مستقیماً در یک Blade Component استفاده کنید.

---

## اضافه کردن فایل CSS

برای اضافه کردن فایل CSS از تابع زیر استفاده کنید:

```php
assets_add_css(public_path('dist/css/test.css'));
```

برای مثال:

```php
assets_add_css(public_path('dist/css/test.css'));
```

---

## گروه‌بندی فایل‌ها

هر دو تابع `assets_add_js` و `assets_add_css` امکان دریافت یک پارامتر دوم را دارند.

پارامتر دوم برای **گروه‌بندی فایل‌ها** استفاده می‌شود.

برای مثال:

```php
assets_add_css(
    public_path('dist/css/bootstrap.css'),
    'bootstrap'
);
```

و:

```php
assets_add_js(
    public_path('dist/js/bootstrap.js'),
    'bootstrap'
);
```

در این مثال، فایل‌ها در گروه `bootstrap` قرار می‌گیرند.

همچنین می‌توانید فایل‌های دیگری را به همین گروه اضافه کنید:

```php
assets_add_css(
    public_path('dist/css/bootstrap-rtl.css'),
    'bootstrap'
);

assets_add_css(
    public_path('dist/css/theme.css'),
    'bootstrap'
);
```

به این ترتیب چند فایل مختلف می‌توانند متعلق به یک گروه باشند.

### مثال کامل

فرض کنید در یک صفحه به فایل‌های زیر نیاز دارید:

```text
dist/css/bootstrap.css
dist/css/theme.css
dist/js/bootstrap.js
dist/js/app.js
```

می‌توانید آن‌ها را به شکل زیر ثبت کنید:

```php
assets_add_css(
    public_path('dist/css/bootstrap.css'),
    'bootstrap'
);

assets_add_css(
    public_path('dist/css/theme.css'),
    'theme'
);

assets_add_js(
    public_path('dist/js/bootstrap.js'),
    'bootstrap'
);

assets_add_js(
    public_path('dist/js/app.js'),
    'app'
);
```

گروه‌بندی باعث می‌شود فایل‌ها بر اساس کاربرد یا بخش مورد نظر مدیریت شوند.

---

## استفاده در Blade

پس از اینکه فایل‌های مورد نیاز صفحه را با `assets_add_js` و `assets_add_css` ثبت کردید، کافی است در فایل Blade آن‌ها را خروجی بگیرید.

### خروجی CSS

در بخش `<head>` فایل Blade:

```blade
{!! assets_css() !!}
```

برای مثال:

```blade
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">

    <title>My Page</title>

    {!! assets_css() !!}
</head>
<body>

    ...

</body>
</html>
```

### خروجی JavaScript

معمولاً قبل از بسته شدن تگ `body`:

```blade
{!! assets_js() !!}
```

برای مثال:

```blade
<body>

    ...

    {!! assets_js() !!}
</body>
```

---

## استفاده در Controller

یکی از کاربردهای اصلی پکیج این است که Assetهای مورد نیاز هر صفحه را در Controller مشخص کنید.

برای مثال:

```php
public function index()
{
    assets_add_css(
        public_path('dist/css/bootstrap.css'),
        'bootstrap'
    );

    assets_add_css(
        public_path('dist/css/users.css'),
        'users'
    );

    assets_add_js(
        public_path('dist/js/bootstrap.js'),
        'bootstrap'
    );

    assets_add_js(
        public_path('dist/js/users.js'),
        'users'
    );

    return view('users.index');
}
```

سپس در Layout اصلی:

```blade
<head>
    {!! assets_css() !!}
</head>

<body>

    @yield('content')

    {!! assets_js() !!}
</body>
```

در نتیجه، View مربوط به صفحه فقط Assetهای مورد نیاز خودش را ثبت می‌کند و Layout وظیفه خروجی گرفتن از Assetها را بر عهده دارد.

---

## استفاده در Blade Component

می‌توانید Assetهای مربوط به یک Component را مستقیماً داخل Blade Component نیز ثبت کنید.

برای مثال:

```blade
@php
    assets_add_css(
        public_path('dist/css/date-picker.css'),
        'date-picker'
    );

    assets_add_js(
        public_path('dist/js/date-picker.js'),
        'date-picker'
    );
@endphp

<div class="date-picker">
    ...
</div>
```

سپس در Layout:

```blade
{!! assets_css() !!}

...

{!! assets_js() !!}
```

این روش برای Componentهایی که Asset اختصاصی دارند بسیار کاربردی است؛ زیرا Asset مربوط به Component فقط زمانی به Asset Manager معرفی می‌شود که آن Component در صفحه استفاده شده باشد.

---

## Merge و Minify

در صورت فعال بودن گزینه `merge` در Configuration:

```php
'merge' => true,
```

پکیج می‌تواند فایل‌های ثبت‌شده را مدیریت کرده و فایل‌های مربوط به Assetها را در مسیر تعیین‌شده قرار دهد.

کتابخانه مورد استفاده برای Minify و ترکیب فایل‌ها:

[matthiasmullie/minify](https://github.com/matthiasmullie/minify)

است.

مسیر خروجی از مقدار `path` در Configuration تعیین می‌شود.

برای مثال:

```php
'path' => 'assets',
```

ساختار فایل‌های عمومی به شکل زیر خواهد بود:

```text
public/
└── assets/
    ├── css/
    └── js/
```

---

## پاک‌سازی Cache

پکیج دارای یک Artisan Command برای پاک‌سازی فایل‌های Cache شده Asset Manager است.

در صورت نیاز می‌توانید Command مربوط به پاک‌سازی Cache را از Artisan اجرا کنید.

برای مشاهده Commandهای در دسترس پکیج:

```bash
php artisan list
```

و سپس Command مربوط به `assets-manager` را پیدا کنید.

---

## نمونه استفاده کامل

یک نمونه ساده از استفاده پکیج در یک پروژه:

### Controller

```php
<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        assets_add_css(
            public_path('dist/css/bootstrap.css'),
            'bootstrap'
        );

        assets_add_js(
            public_path('dist/js/bootstrap.js'),
            'bootstrap'
        );

        assets_add_css(
            public_path('dist/css/dashboard.css'),
            'dashboard'
        );

        assets_add_js(
            public_path('dist/js/dashboard.js'),
            'dashboard'
        );

        return view('dashboard');
    }
}
```

### Layout

```blade
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>

    <meta charset="UTF-8">

    <title>Dashboard</title>

    {!! assets_css() !!}

</head>
<body>

    @yield('content')

    {!! assets_js() !!}

</body>
</html>
```

### View

```blade
@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>Dashboard</h1>
    </div>

@endsection
```

در این حالت Controller فایل‌های مورد نیاز صفحه را مشخص می‌کند و Layout بدون نیاز به دانستن جزئیات Assetهای هر صفحه، خروجی CSS و JavaScript را ایجاد می‌کند.

---

## API

### `assets_add_js`

ثبت یک فایل JavaScript:

```php
assets_add_js(string $path, ?string $group = null);
```

مثال:

```php
assets_add_js(
    public_path('dist/js/app.js'),
    'app'
);
```

---

### `assets_add_css`

ثبت یک فایل CSS:

```php
assets_add_css(string $path, ?string $group = null);
```

مثال:

```php
assets_add_css(
    public_path('dist/css/app.css'),
    'app'
);
```

---

### `assets_css`

دریافت خروجی فایل‌های CSS ثبت‌شده:

```blade
{!! assets_css() !!}
```

---

### `assets_js`

دریافت خروجی فایل‌های JavaScript ثبت‌شده:

```blade
{!! assets_js() !!}
```

---

## چرا از `public_path()` استفاده می‌شود؟

توابع Asset Manager مسیر واقعی فایل را دریافت می‌کنند. به همین دلیل برای فایل‌هایی که داخل `public` قرار دارند، استفاده از `public_path()` پیشنهاد می‌شود.

برای مثال:

```php
assets_add_js(
    public_path('dist/js/app.js')
);
```

که در یک Laravel Project معمولی به مسیری مشابه زیر اشاره خواهد کرد:

```text
/path/to/project/public/dist/js/app.js
```

این موضوع به Asset Manager اجازه می‌دهد فایل را در سمت سرور پیدا کرده و در صورت نیاز آن را پردازش یا Merge کند.

---

## License

این پکیج تحت لایسنس MIT منتشر شده است.

برای اطلاعات بیشتر فایل [LICENSE](LICENSE) را مشاهده کنید.

---

## Author

Developed by **NovinVision Team**

Email: novinvision.com@gmail.com

Website: [novinvision.com](https://novinvision.com)