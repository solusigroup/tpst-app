<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

use App\Models\HasilPilahan;
use App\Models\Penjualan;
use App\Models\Ritase;
use App\Models\Invoice;
use App\Models\JurnalDetail;
use App\Observers\HasilPilahanObserver;
use App\Observers\InvoiceObserver;
use App\Observers\PenjualanObserver;
use App\Observers\RitaseObserver;
use App\Observers\JurnalDetailObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class,
            \App\Http\Responses\LogoutResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Register Eloquent Observers for automatic accounting
        Ritase::observe(RitaseObserver::class);
        Penjualan::observe(PenjualanObserver::class);
        Invoice::observe(InvoiceObserver::class);
        JurnalDetail::observe(JurnalDetailObserver::class);
        HasilPilahan::observe(HasilPilahanObserver::class);

        // Force HTTPS in production (Fixes issues with Cloudflare Flexible SSL)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
