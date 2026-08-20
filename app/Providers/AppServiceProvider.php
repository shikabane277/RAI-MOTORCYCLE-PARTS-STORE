<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
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
        // Auto-run database migrations in production if database tables are missing
        if ($this->app->environment('production')) {
            try {
                if (!Schema::hasTable('orders')) {
                    Artisan::call('migrate', ['--force' => true]);
                    Log::info('AppServiceProvider: Auto-migrations executed successfully.');
                }
            } catch (\Throwable $e) {
                Log::warning('AppServiceProvider: Auto-migration attempt skipped: ' . $e->getMessage());
            }
        }
    }
}
