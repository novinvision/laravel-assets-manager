<?php

namespace NovinVision\LaravelAssetsManager;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use NovinVision\IRTax\Core\TaxInvoice;
use NovinVision\IRTax\Core\TaxInvoiceReceipt;
use NovinVision\IRTax\Drivers\IRTaxDriverConfig;

class AssetsManager
{

    public static array $css = [];

    public static array $styles = [];

    public static array $js = [];

    public function __construct()
    {
    }

    public static function addCss($filename, string $collection = 'main'): void
    {
        self::$css[$collection][] = $filename;
    }

    public static function addStyle($filename, string $collection = 'main'): void
    {
        self::$styles[$collection][] = $filename;
    }

    public static function addJs($filename, string $collection = 'main', int $priority = 0): void
    {
        if($priority){
            self::$js[$collection][$priority] = $filename;
        }else{
            self::$js[$collection][] = $filename;
        }
    }

    public static function renderCss(): string
    {
        $output = '';
        $outputFiles = [];
        foreach (self::$css as $collection => $files) {
            $styles = collect(self::$styles[$collection] ?? [])->map(function ($style) {
                return strip_tags($style);
            });

            $files = collect(self::$css[$collection])->map(fn($name) => Str::of($name)
                ->beforeLast('?')
                ->replaceMatches('#//[^/]+:\d+#', '')
                ->toString())->unique()->values();
            if (empty($files)) return '';

            $outputFiles[$collection] = $files;
            if (config('assets-manager.merge')) {
                $outputName = md5(json_encode($files->map(function ($file) {
                        return filemtime($file);
                    })) . json_encode($styles));

                $path = config('assets-manager.path');
                $outputPath = public_path("{$path}/css/{$collection}-{$outputName}.css");

                if (!file_exists($outputPath)) {
                    $combined = $styles->implode(' ');
                    foreach ($files as $file) {
                        $combined .= file_get_contents($file) . " ";
                    }
                    file_put_contents($outputPath, $combined);
                }
                $outputFiles[$collection] = [$outputPath];
            }

            if (config('assets-manager.minify')) {
                foreach ($outputFiles[$collection] as $key => $file) {
                    $minifier = new CSS($file);
                    $outputFiles[$collection][$key] = Str::beforeLast($file, '.css') . '.min.css';
                    $minifier->minify($outputFiles[$collection][$key]);
                }
            }
        }

        if (config('assets-manager.preload_fonts')) {
            $allowedPatterns = config('assets-manager.preload_fonts');
            foreach (assets_css_fonts() as $font) {
                $fontPath = Str::of($font)
                    ->replace([public_path(), '\\', '//'], ['', '/'])
                    ->replaceMatches('#//[^/]+:\d+#', '')->toString();
                $fileName = Str::of(basename($fontPath))->beforeLast('?');

                if (is_array($allowedPatterns)) {
                    foreach ((array)$allowedPatterns as $pattern) {
                        if (Str::is($pattern, $fileName)) {
                            $output .= sprintf(config('assets-manager.font_preload_tag'), asset($fontPath)) . PHP_EOL;
                            break;
                        }
                    }
                } else if (!is_array($allowedPatterns)) {
                    $output .= sprintf(config('assets-manager.font_preload_tag'), asset($fontPath)) . PHP_EOL;
                }
            }
        }

        foreach ($outputFiles as $collection => $files) {
            $tagFormat = config("assets-manager.style_tag");
            foreach ($files as $file){
                $output .= sprintf(($tagFormat[$collection] ?? $tagFormat[array_key_first($tagFormat)] ?? $tagFormat), asset(str_replace([public_path(), '\\', '//'], ['', '/'], $file))) . PHP_EOL;
            }
        }

        return html_entity_decode($output);
    }

    public static function renderJs(): string
    {
        $output = '';
        $outputFiles = [];


        foreach (self::$js as $collection => $files) {
            $files = collect(self::$js[$collection])->sortKeysDesc()
                ->map(fn($name) => Str::of($name)->beforeLast('?')->replaceMatches('#//[^/]+:\d+#', '')->toString())->unique()->values();
            if (empty($files)) return '';

            if (config('assets-manager.merge')) {
                $outputName = md5(json_encode($files->map(function ($file) {
                    return filemtime($file);
                })));

                $path = config('assets-manager.path');
                $outputPath = public_path("{$path}/js/{$collection}-{$outputName}.js");

                if (!file_exists($outputPath)) {
                    $combined = '';
                    foreach ($files as $file) {
                        $combined .= file_get_contents($file) . " ";
                    }
                    file_put_contents($outputPath, $combined);
                }
                $outputFiles[$collection] = [$outputPath];
            }

            if (config('assets-manager.minify') && !empty($outputFiles[$collection])) {
                foreach ($outputFiles[$collection] as $key => $file) {
                    $minifier = new JS($file);
                    $outputFiles[$collection][$key] = Str::beforeLast($file, '.js') . '.min.js';
                    $minifier->minify($outputFiles[$collection][$key]);
                }
            }

            if(empty($outputFiles[$collection])){
                $outputFiles[$collection] = $files->toArray();
            }
        }

        foreach ($outputFiles as $collection => $files) {
            $tagFormat = config("assets-manager.script_tag");
            foreach ($files as $file){
                $output .= sprintf(($tagFormat[$collection] ?? $tagFormat[array_key_first($tagFormat)] ?? $tagFormat), asset(str_replace([public_path(), '\\', '//'], ['', '/'], $file))) . PHP_EOL;
            }
        }

        return html_entity_decode($output);
    }

}
