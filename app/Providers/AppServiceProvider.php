<?php

namespace App\Providers;

use App\Models\Penjualan;
use App\Models\Ritase;
use App\Observers\PenjualanObserver;
use App\Observers\RitaseObserver;
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
        // Register Eloquent Observers for automatic accounting
        Ritase::observe(RitaseObserver::class);
        Penjualan::observe(PenjualanObserver::class);
    }
}
