<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RitaseController;
use App\Http\Controllers\Admin\KlienController;
use App\Http\Controllers\Admin\ArmadaController;
use App\Http\Controllers\Admin\HasilPilahanController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Admin\CoaController;
use App\Http\Controllers\Admin\JurnalController;
use App\Http\Controllers\Admin\JurnalKasController;
use App\Http\Controllers\Admin\InvoiceAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CompanySettingsController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\LaporanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Operasional
    Route::resource('ritase', RitaseController::class);
    Route::post('ritase/{ritase}/post', [RitaseController::class, 'post'])->name('ritase.post');
    Route::resource('klien', KlienController::class);
    Route::resource('armada', ArmadaController::class);
    Route::resource('hasil-pilahan', HasilPilahanController::class)->parameters(['hasil-pilahan' => 'hasilPilahan']);
    Route::resource('penjualan', PenjualanController::class);

    // Keuangan
    Route::resource('coa', CoaController::class);
    Route::resource('jurnal', JurnalController::class);
    Route::post('jurnal/{jurnal}/post', [JurnalController::class, 'post'])->name('jurnal.post');
    Route::post('jurnal/{jurnal}/unpost', [JurnalController::class, 'unpost'])->name('jurnal.unpost');
    Route::resource('jurnal-kas', JurnalKasController::class)->parameters(['jurnal-kas' => 'jurnalKas']);
    Route::resource('invoice', InvoiceAdminController::class);

    // PENGATURAN
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('/company-settings', [CompanySettingsController::class, 'edit'])->name('company-settings');
    Route::put('/company-settings', [CompanySettingsController::class, 'update'])->name('company-settings.update');

    // ACTIVITY LOG
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    // Laporan Keuangan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
        Route::get('neraca-saldo', [LaporanController::class, 'neracaSaldo'])->name('neraca-saldo');
        Route::get('posisi-keuangan', [LaporanController::class, 'posisiKeuangan'])->name('posisi-keuangan');
        Route::get('arus-kas', [LaporanController::class, 'arusKas'])->name('arus-kas');
        Route::get('perubahan-ekuitas', [LaporanController::class, 'perubahanEkuitas'])->name('perubahan-ekuitas');
        Route::get('buku-besar', [LaporanController::class, 'bukuBesar'])->name('buku-besar');
    });

    // Laporan Operasional
    Route::prefix('laporan-operasional')->name('laporan-operasional.')->group(function () {
        Route::get('ritase', [LaporanController::class, 'laporanRitase'])->name('ritase');
        Route::get('penjualan', [LaporanController::class, 'laporanPenjualan'])->name('penjualan');
        Route::get('hasil-pilahan', [LaporanController::class, 'laporanHasilPilahan'])->name('hasil-pilahan');
    });
});
