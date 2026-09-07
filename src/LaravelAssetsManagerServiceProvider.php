<?php
namespace NovinVision\LaravelAssetsManager;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LaravelAssetsManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/assets-manager.php', 'assets-manager');

        $this->publishes([
            __DIR__ . '/../config/assets-manager.php' => config_path('assets-manager.php'),
        ], 'assets-manager');

        $this->commands([
            \NovinVision\LaravelAssetsManager\Commands\ClearCache::class,
        ]);

        if (config('assets-manager.merge')) {
            $path = config('assets-manager.path');
            if(!is_dir(public_path($path))){
                mkdir(public_path($path));
            }

            if (!is_dir(public_path("{$path}/css"))) {
                mkdir(public_path("{$path}/css"));
            }

            if (!is_dir(public_path("{$path}/js"))) {
                mkdir(public_path("{$path}/js"));
            }
        }
    }
}
