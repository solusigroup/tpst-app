<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Support\Carbon as IlluminateCarbon;
use App\Helpers\DateHelper;

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

        // Set locale Carbon and PHP to Indonesian
        Carbon::setLocale('id');
        IlluminateCarbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID', 'id_ID.utf8', 'id', 'indonesian');

        // Carbon Macros for convenient Indonesian formatting
        IlluminateCarbon::macro('formatIndo', function (string $format = 'd F Y') {
            return $this->translatedFormat($format);
        });

        IlluminateCarbon::macro('formatIndoWaktu', function (bool $withWib = true) {
            $formatted = $this->translatedFormat('d F Y H:i');
            return $withWib ? $formatted . ' WIB' : $formatted;
        });

        IlluminateCarbon::macro('formatIndoHari', function () {
            return $this->translatedFormat('l, d F Y');
        });

        IlluminateCarbon::macro('formatIndoSingkat', function () {
            return $this->format('d/m/Y');
        });

        // Blade Directives for Indonesian formatting
        Blade::directive('tgl', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::formatTanggal($expression); ?>";
        });

        Blade::directive('tglWaktu', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::formatTanggalWaktu($expression); ?>";
        });

        Blade::directive('tglHari', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::formatHariTanggal($expression); ?>";
        });

        Blade::directive('tglShort', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::formatSingkat($expression); ?>";
        });

        Blade::directive('rentangTgl', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::formatRentang($expression); ?>";
        });

        // Super admin bypasses all permission/ability checks
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Register Eloquent Observers for automatic accounting
        Ritase::observe(RitaseObserver::class);
        Penjualan::observe(PenjualanObserver::class);
        Invoice::observe(InvoiceObserver::class);
        JurnalDetail::observe(JurnalDetailObserver::class);
        HasilPilahan::observe(HasilPilahanObserver::class);
        \App\Models\PengangkutanResidu::observe(\App\Observers\PengangkutanResiduObserver::class);
        \App\Models\WageCalculation::observe(\App\Observers\WageCalculationObserver::class);

        // Force HTTPS in production (Fixes issues with Cloudflare Flexible SSL)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
