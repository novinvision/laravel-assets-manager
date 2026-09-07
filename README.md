# Laravel Assets Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/novinvision/laravel-assets-manager.svg)](https://packagist.org/packages/novinvision/laravel-assets-manager)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A Laravel package for managing, grouping, combining, and minifying CSS and JavaScript assets based on the current page.

**فارسی:** [README-fa.md](README-fa.md)

---

## Features

- Add CSS and JavaScript assets from Controllers or Blade Components
- Group assets using a custom group name
- Combine CSS and JavaScript files
- Minify assets using [MatthiasMullie Minify](https://github.com/matthiasmullie/minify)
- Automatically create the required public directories
- Laravel auto-discovery support
- Simple Blade helper functions

---

## Requirements

- PHP 8.1 or higher
- Laravel 10 or higher

---

## Installation

Install the package using Composer:

```bash
composer require novinvision/laravel-assets-manager
```

The service provider is automatically registered by Laravel's package auto-discovery, so no manual registration is required.

---

## Publish Configuration

To publish the package configuration file, run:

```bash
php artisan vendor:publish --tag=assets-manager
```

This will publish the configuration file to:

```text
config/assets-manager.php
```

You can then customize the package settings from this file.

---

## Configuration

After publishing the configuration file, you will have:

```text
config/assets-manager.php
```

The package uses the following configuration key:

```php
config('assets-manager')
```

### Merge Assets

The `merge` option controls whether assets should be combined and processed.

Example:

```php
'merge' => true,
```

When enabled, the package automatically creates the required directories inside the configured public path.

### Asset Path

The `path` option determines where the generated assets are stored inside the `public` directory.

For example:

```php
'path' => 'assets',
```

The package will create the following directory structure:

```text
public/
└── assets/
    ├── css/
    └── js/
```

If the configured path does not exist, the package creates it automatically.

---

## Adding JavaScript Assets

You can add a JavaScript file using the `assets_add_js()` helper:

```php
assets_add_js(public_path('dist/js/test.js'));
```

This can be used inside a Controller or a Blade Component.

For example:

```php
public function index()
{
    assets_add_js(
        public_path('dist/js/test.js')
    );

    return view('test.index');
}
```

---

## Adding CSS Assets

Use the `assets_add_css()` helper to register a CSS file:

```php
assets_add_css(public_path('dist/css/test.css'));
```

For example:

```php
assets_add_css(
    public_path('dist/css/test.css')
);
```

---

## Grouping Assets

Both `assets_add_js()` and `assets_add_css()` accept an optional second parameter.

The second parameter is used to **group assets**.

For example:

```php
assets_add_css(
    public_path('dist/css/bootstrap.css'),
    'bootstrap'
);
```

And:

```php
assets_add_js(
    public_path('dist/js/bootstrap.js'),
    'bootstrap'
);
```

Both files are now registered under the `bootstrap` group.

You can add multiple files to the same group:

```php
assets_add_css(
    public_path('dist/css/bootstrap.css'),
    'bootstrap'
);

assets_add_css(
    public_path('dist/css/bootstrap-rtl.css'),
    'bootstrap'
);

assets_add_js(
    public_path('dist/js/bootstrap.js'),
    'bootstrap'
);
```

The group name can be any value that makes sense for your application.

For example:

```php
'bootstrap'
'admin'
'dashboard'
'auth'
'editor'
'datepicker'
```

---

## Using Assets in Blade

After registering your assets, you can output them in your Blade layout using the following helper functions.

### CSS

Add the following to the `<head>` section:

```blade
{!! assets_css() !!}
```

Example:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>My Application</title>

    {!! assets_css() !!}
</head>
<body>

    ...

</body>
</html>
```

### JavaScript

Add the following before the closing `</body>` tag:

```blade
{!! assets_js() !!}
```

Example:

```blade
<body>

    ...

    {!! assets_js() !!}

</body>
```

---

## Using Assets in a Controller

You can register assets directly from a Controller.

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

Your main Blade layout can then handle the actual output:

```blade
<!DOCTYPE html>
<html lang="en">
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

This keeps page-specific asset registration inside the Controller while keeping the layout clean.

---

## Using Assets in Blade Components

Assets can also be registered directly inside a Blade Component.

For example:

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

If the Component is rendered on a page, its CSS and JavaScript assets will also be registered.

This is particularly useful for reusable Blade Components that have their own CSS or JavaScript dependencies.

---

## Complete Example

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
<html lang="en">
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

The Controller registers the assets required by the page, while the layout is responsible for rendering them.

---

## API

### `assets_add_js()`

Register a JavaScript asset:

```php
assets_add_js(string $path, ?string $group = null);
```

Example:

```php
assets_add_js(
    public_path('dist/js/app.js'),
    'app'
);
```

---

### `assets_add_css()`

Register a CSS asset:

```php
assets_add_css(string $path, ?string $group = null);
```

Example:

```php
assets_add_css(
    public_path('dist/css/app.css'),
    'app'
);
```

---

### `assets_css()`

Render all registered CSS assets:

```blade
{!! assets_css() !!}
```

---

### `assets_js()`

Render all registered JavaScript assets:

```blade
{!! assets_js() !!}
```

---

## Using `public_path()`

The asset registration helpers expect the actual filesystem path of the asset.

For files located inside Laravel's `public` directory, it is recommended to use Laravel's `public_path()` helper.

For example:

```php
assets_add_js(
    public_path('dist/js/app.js')
);
```

This resolves to a filesystem path similar to:

```text
/path/to/project/public/dist/js/app.js
```

This allows the package to locate and process the asset on the server when required.

---

## Cache

Laravel Assets Manager includes an Artisan command for clearing cached assets.

You can see the available commands provided by the package using:

```bash
php artisan list
```

Then look for the command related to `assets-manager`.

---

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## Author

Developed by **NovinVision Team**

Email: novinvision.com@gmail.com

Website: [novinvision.com](https://novinvision.com)

---

## Documentation

- 🇬🇧 **English:** `README.md`
- 🇮🇷 **فارسی:** [README-fa.md](README-fa.md)