<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Observers\ProductObserver;
use App\Observers\StockTransactionObserver;
use Illuminate\Support\ServiceProvider;

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
        Product::observe(ProductObserver::class);
        StockTransaction::observe(StockTransactionObserver::class);
    }
}
