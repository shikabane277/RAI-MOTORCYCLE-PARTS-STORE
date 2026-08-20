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
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');

            try {
                if (!Schema::hasTable('orders')) {
                    Artisan::call('migrate', ['--force' => true]);
                    Log::info('AppServiceProvider: Auto-migrations executed successfully.');
                }
            } catch (\Throwable $e) {
                Log::warning('AppServiceProvider: Auto-migration attempt skipped: ' . $e->getMessage());
            }
        }

        // Guarantee every product has at least 1 stock variant
        try {
            if (Schema::hasTable('products') && Schema::hasTable('product_variants')) {
                \App\Models\Product::doesntHave('variants')->get()->each(function ($product) {
                    \App\Models\ProductVariant::create([
                        'product_id'          => $product->id,
                        'variant_sku'         => 'RAI-' . strtoupper(\Illuminate\Support\Str::slug(substr($product->name, 0, 10))) . '-' . $product->id,
                        'color'               => 'Standard',
                        'material'            => 'Standard',
                        'pack_qty'            => 1,
                        'price'               => $product->base_price ?: 100,
                        'stock_qty'           => 50,
                        'low_stock_threshold' => 5,
                        'is_active'           => true,
                    ]);
                });
            }
        } catch (\Throwable $e) {
            // Ignore during initial migration bootstrap
        }

        // Share active categories dynamically across all views
        try {
            if (Schema::hasTable('categories')) {
                \Illuminate\Support\Facades\View::composer('*', function ($view) {
                    $navCategories = \App\Models\Category::where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get();
                    $view->with('navCategories', $navCategories);
                });
            }
        } catch (\Throwable $e) {
            // Ignore during initial migration
        }
    }
}
