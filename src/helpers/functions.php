<?php

if (!function_exists('assets_css_files')) {
    function assets_css_files(): \Illuminate\Support\Collection
    {
        return collect(\NovinVision\LaravelAssetsManager\AssetsManager::$css)->unique()->values();
    }
}

if (!function_exists('assets_css_fonts')) {
    function assets_css_fonts(): array
    {
        $removeVersion = config('assets-manager.preload_fonts_remove_version');
        $output = collect();
        foreach (assets_css_files() as $collection => $files) {
            if (is_array($files)) foreach ($files as $file) {
                $file = Illuminate\Support\Str::of($file)->beforeLast('?')->replaceMatches('#//[^/]+:\d+#', '')->toString();
                preg_match_all('#/fonts/[^)\'" ]+\.(?:woff2?|ttf)(\?[^\s\'")]+)?#', file_get_contents($file), $fonts);
                foreach ($fonts[0] as $font) {
                    if ($removeVersion) {
                        $font = Str::of($font)->beforeLast('?')->toString();
                    }

                    if ($font) {
                        $output->add($font);
                    }
                }
            }
        }

        return $output->unique()->values()->toArray();
    }
}

if (!function_exists('assets_css')) {
    function assets_css(): string
    {
        return \NovinVision\LaravelAssetsManager\AssetsManager::renderCss();
    }
}

if (!function_exists('assets_add_css')) {
    function assets_add_css($file, string $collection = 'main'): void
    {
        \NovinVision\LaravelAssetsManager\AssetsManager::addCss($file, $collection);
    }
}

if (!function_exists('assets_add_style')) {
    function assets_add_style($file, string $collection = 'main'): void
    {
        \NovinVision\LaravelAssetsManager\AssetsManager::addStyle($file, $collection);
    }
}

if (!function_exists('assets_js')) {

    function assets_js(): string
    {
        return \NovinVision\LaravelAssetsManager\AssetsManager::renderJs();
    }
}

if (!function_exists('assets_add_js')) {
    function assets_add_js($file, string $collection = 'main', int $priority = 0): void
    {
        \NovinVision\LaravelAssetsManager\AssetsManager::addJs($file, $collection, $priority);
    }
}
