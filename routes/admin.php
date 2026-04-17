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
use App\Http\Controllers\Admin\InvoiceItemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CompanySettingsController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\EmployeeOutputController;
use App\Http\Controllers\Admin\WasteCategoryController;
use App\Http\Controllers\Admin\WageRateController;
use App\Http\Controllers\Admin\WageCalculationController;
use App\Http\Controllers\Admin\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/test500', function() {
        return response()->json(['status' => 'OK', 'message' => 'The code was successfully pulled!']);
    });
    Route::get('/test500-controller', [\App\Http\Controllers\Admin\Test500Controller::class, 'index']);
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Operasional
    Route::get('ritase/export-rekap', [RitaseController::class, 'exportRekap'])->name('ritase.export-rekap');
    Route::resource('ritase', RitaseController::class);
    Route::post('ritase/{ritase}/approve', [RitaseController::class, 'approve'])->name('ritase.approve');
    Route::post('ritase/{ritase}/post', [RitaseController::class, 'post'])->name('ritase.post');
    Route::resource('klien', KlienController::class);
    Route::resource('armada', ArmadaController::class);
    Route::resource('hasil-pilahan', HasilPilahanController::class)->parameters(['hasil-pilahan' => 'hasilPilahan']);
    Route::resource('penjualan', PenjualanController::class);
    Route::resource('machines', \App\Http\Controllers\Admin\MachineController::class);
    Route::resource('machine-logs', \App\Http\Controllers\Admin\MachineLogController::class);
    Route::resource('pengangkutan-residu', \App\Http\Controllers\Admin\PengangkutanResiduController::class);

    // Keuangan
    Route::resource('coa', CoaController::class);
    Route::resource('vendor', \App\Http\Controllers\Admin\VendorController::class);
    Route::resource('jurnal', JurnalController::class);
    Route::post('jurnal/{jurnal}/post', [JurnalController::class, 'post'])->name('jurnal.post');
    Route::post('jurnal/{jurnal}/unpost', [JurnalController::class, 'unpost'])->name('jurnal.unpost');
    Route::resource('jurnal-kas', JurnalKasController::class)->parameters(['jurnal-kas' => 'jurnalKas']);
    Route::get('invoice-items/pending', [InvoiceItemController::class, 'getPendingItems'])->name('invoice-items.pending');
    Route::post('invoice/merge-drafts', [InvoiceAdminController::class, 'mergeDrafts'])->name('invoice.merge-drafts');
    Route::resource('invoice', InvoiceAdminController::class);

    // Buku Pembantu
    Route::get('buku-pembantu/piutang', [\App\Http\Controllers\Admin\BukuPembantuController::class, 'piutang'])->name('buku-pembantu.piutang');
    Route::get('buku-pembantu/utang', [\App\Http\Controllers\Admin\BukuPembantuController::class, 'utang'])->name('buku-pembantu.utang');

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
        Route::get('residu', [LaporanController::class, 'laporanResidu'])->name('residu');
        Route::get('kehadiran', [LaporanController::class, 'laporanKehadiran'])->name('kehadiran');
        Route::get('upah', [LaporanController::class, 'laporanUpah'])->name('upah');
    });

    // HRD
    Route::prefix('hrd')->name('hrd.')->group(function () {
        Route::resource('attendance', AttendanceController::class);
        Route::post('attendance/{user}/check-in', [AttendanceController::class, 'quickCheckIn'])->name('attendance.check-in');
        Route::post('attendance/{user}/check-out', [AttendanceController::class, 'quickCheckOut'])->name('attendance.check-out');
        
        Route::resource('output', EmployeeOutputController::class);
        
        Route::resource('waste-category', WasteCategoryController::class);
        
        Route::resource('wage-rate', WageRateController::class);
        
        Route::get('wage-calculation/export-rekap', [WageCalculationController::class, 'exportRekap'])->name('wage-calculation.export-rekap');
        Route::post('wage-calculation/calculate', [WageCalculationController::class, 'calculate'])->name('wage-calculation.calculate');
        
        Route::resource('wage-calculation', WageCalculationController::class)->only(['index', 'show']);
        
        Route::resource('employee', EmployeeController::class);
        Route::match(['post', 'patch'], 'wage-calculation/{wageCalculation}/approve', [WageCalculationController::class, 'approve'])->name('wage-calculation.approve');
        Route::match(['post', 'patch'], 'wage-calculation/{wageCalculation}/pay', [WageCalculationController::class, 'pay'])->name('wage-calculation.pay');
        Route::get('wage-calculation/{wageCalculation}/export-slip', [WageCalculationController::class, 'exportSlip'])->name('wage-calculation.export-slip');
    });
});
