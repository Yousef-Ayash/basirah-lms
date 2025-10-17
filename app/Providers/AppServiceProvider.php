<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if ($this->app->runningInConsole()) {
            // only run in non-production by default (remove the env check if you want it in prod)
            if (app()->environment('production')) {
                return;
            }

            // Are we running vendor:publish (or composer calling artisan vendor:publish)?
            $argv = $_SERVER['argv'] ?? [];
            $isPublish = in_array('vendor:publish', $argv, true) || collect($argv)->contains(function ($v) {
                return str_contains($v, 'vendor:publish');
            });

            // also run defensively on composer install/update where --no-scripts isn't used
            if ($isPublish) {
                $this->cleanupDuplicatePackageMigrations([
                    'create_personal_access_tokens_table',
                    'add_two_factor_columns_to_users_table',
                    // add any other migration file slugs you want to protect
                ]);
            }
        }
    }
    protected function cleanupDuplicatePackageMigrations(array $slugs)
    {
        $migrationsPath = database_path('migrations');

        foreach ($slugs as $slug) {
            $pattern = $migrationsPath . DIRECTORY_SEPARATOR . "*{$slug}.php";
            $files = glob($pattern);

            if (!$files || count($files) <= 1) {
                continue; // nothing to do
            }

            // sort by filemtime ascending => keep the oldest; remove others
            usort($files, function ($a, $b) {
                return filemtime($a) <=> filemtime($b);
            });

            // keep the first (oldest), delete the rest
            $keep = array_shift($files);
            foreach ($files as $fileToDelete) {
                try {
                    unlink($fileToDelete);
                    // Log instead of echo so logs show up in artisan output and storage/logs
                    Log::info("[migration-cleaner] deleted duplicate migration: {$fileToDelete}, kept {$keep}");
                } catch (\Throwable $e) {
                    Log::warning("[migration-cleaner] failed to delete {$fileToDelete}: " . $e->getMessage());
                }
            }
        }
    }
}
