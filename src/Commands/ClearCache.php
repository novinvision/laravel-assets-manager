<?php

namespace NovinVision\LaravelAssetsManager\Commands;

use Illuminate\Console\Command;

class ClearCache extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'assets:clear {--dry-run} {--skip-js} {--skip-css}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear assets manager minify and merge cache.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(!$this->option('skip-js')) {
            $this->deleteFiles('js', !!$this->option('dry-run'));
        }

        if(!$this->option('skip-css')) {
            $this->deleteFiles('css', !!$this->option('dry-run'));
        }
    }

    protected function deleteFiles($path, $dryRun = false): string
    {
        $target = public_path(rtrim(config('assets-manager.path'), '/') . "/" . $path);

        if (!is_dir($target)) {
            throw new \RuntimeException("path not found {$target}");
        }

        $allowedPrefix = public_path();
        if (!str_starts_with($target, $allowedPrefix)) {
            throw new \RuntimeException("invalid public path {$target}. only this url allowed: {$allowedPrefix}");
        }

        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($target, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($it as $item) {
            /** @var \SplFileInfo $item */
            if ($item->isFile()) {
                $filePath = $item->getRealPath();
                if ($dryRun) {
                    $this->info($filePath);
                } else {
                    if (@unlink($filePath)) {
                        $this->info("{$filePath} deleted");
                    } else {
                        $this->error("Error on {$filePath} delete");
                    }
                }
            }
        }

        return $target;
    }
}
