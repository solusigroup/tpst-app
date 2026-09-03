<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RitaseController;
use App\Http\Controllers\Admin\KlienController;
use App\Http\Controllers\Admin\ArmadaController;
use App\Http\Controllers\Admin\HasilPilahanController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Admin\CoaController;
use App\Http\Controllers\Admin\JurnalController;
use App\Http\Controllers\Admin\BankReconciliationController;
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
use App\Http\Controllers\Admin\RitaseDlhController;
use App\Http\Controllers\Admin\AiAssistantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Debug / Dev Routes (Super Admin Only)
    Route::middleware([\App\Http\Middleware\SuperAdminMiddleware::class])->group(function () {
        Route::get('/test500', function() {
            return response()->json(['status' => 'OK', 'message' => 'The code was successfully pulled!']);
        });
        Route::get('/test500-controller', [\App\Http\Controllers\Admin\Test500Controller::class, 'index']);
        Route::get('/logs-debug', function() {
            $logPath = storage_path('logs/laravel.log');
            if (!file_exists($logPath)) {
                return response('Log file does not exist.', 404);
            }
            $content = file_get_contents($logPath);
            return response(substr($content, -20000), 200)->header('Content-Type', 'text/plain');
        });
        Route::get('/invoice-debug', function() {
            try {
                $invoices = \App\Models\Invoice::with('klien')->orderByDesc('tanggal_invoice')->paginate(15);
                return view('admin.invoice.index', compact('invoices'))->render();
            } catch (\Throwable $e) {
                return response('<pre style="color:red; font-size:14px;"><strong>ERROR:</strong> ' . htmlspecialchars($e->getMessage()) . "\n<strong>FILE:</strong> " . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . "\n\n<strong>STACK TRACE:</strong>\n" . htmlspecialchars($e->getTraceAsString()) . '</pre>', 200)->header('Content-Type', 'text/html');
            }
        });
    });

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Operasional
    Route::get('ritase/export-rekap', [RitaseController::class, 'exportRekap'])->name('ritase.export-rekap');
    Route::get('ritase/asal-sampah', [RitaseController::class, 'asalSampahByKlien'])->name('ritase.asal-sampah');
    Route::get('ritase/bulk-approve', fn() => redirect()->route('admin.ritase.index'));
    Route::post('ritase/bulk-approve', [RitaseController::class, 'bulkApprove'])->name('ritase.bulk-approve');
    Route::post('ritase/{ritase}/approve', [RitaseController::class, 'approve'])->name('ritase.approve');
    Route::resource('ritase', RitaseController::class);

    // Ritase DLH (Disetujui & Dibayar)
    Route::get('ritase-dlh/approved', [RitaseDlhController::class, 'approved'])->name('ritase-dlh.approved');
    Route::get('ritase-dlh/paid', [RitaseDlhController::class, 'paid'])->name('ritase-dlh.paid');
    Route::get('ritase-dlh/export-excel', [RitaseDlhController::class, 'exportExcel'])->name('ritase-dlh.export-excel');
    Route::get('ritase-dlh/export-pdf', [RitaseDlhController::class, 'exportPdf'])->name('ritase-dlh.export-pdf');

    Route::get('klien/export-excel', [KlienController::class, 'exportExcel'])->name('klien.export-excel');
    Route::get('klien/print', [KlienController::class, 'print'])->name('klien.print');
    Route::resource('klien', KlienController::class);
    Route::resource('armada', ArmadaController::class);
    Route::resource('hasil-pilahan', HasilPilahanController::class)->parameters(['hasil-pilahan' => 'hasilPilahan']);
    Route::resource('penjualan', PenjualanController::class);
    Route::resource('machines', \App\Http\Controllers\Admin\MachineController::class);
    Route::resource('machine-logs', \App\Http\Controllers\Admin\MachineLogController::class);
    Route::post('pengangkutan-residu/bulk-pembayaran', [\App\Http\Controllers\Admin\PengangkutanResiduController::class, 'bulkPembayaran'])->name('pengangkutan-residu.bulk-pembayaran');
    Route::resource('pengangkutan-residu', \App\Http\Controllers\Admin\PengangkutanResiduController::class);

    // Keuangan
    Route::resource('coa', CoaController::class);
    Route::resource('vendor', \App\Http\Controllers\Admin\VendorController::class);
    Route::post('jurnal/purge-selected', [JurnalController::class, 'purgeSelected'])->name('jurnal.purge-selected');
    Route::resource('jurnal', JurnalController::class);
    Route::post('jurnal/{jurnal}/post', [JurnalController::class, 'post'])->name('jurnal.post');
    Route::post('jurnal/{jurnal}/unpost', [JurnalController::class, 'unpost'])->name('jurnal.unpost');
    Route::post('jurnal/{jurnal}/purge', [JurnalController::class, 'purge'])->name('jurnal.purge');
    Route::post('jurnal-template', [JurnalController::class, 'storeTemplate'])->name('jurnal-template.store');
    Route::delete('jurnal-template/{jurnalTemplate}', [JurnalController::class, 'destroyTemplate'])->name('jurnal-template.destroy');
    Route::resource('jurnal-kas', JurnalKasController::class)->parameters(['jurnal-kas' => 'jurnalKas']);
    Route::get('rekonsiliasi-bank', [BankReconciliationController::class, 'index'])->name('rekonsiliasi-bank.index');
    Route::post('rekonsiliasi-bank/proses', [BankReconciliationController::class, 'proses'])->name('rekonsiliasi-bank.proses');
    Route::post('rekonsiliasi-bank/export-excel', [BankReconciliationController::class, 'exportExcel'])->name('rekonsiliasi-bank.export-excel');
    Route::get('rekonsiliasi-bank/export-excel', [BankReconciliationController::class, 'exportExcel'])->name('rekonsiliasi-bank.export-excel-get');
    Route::get('transfer-kas', [JurnalKasController::class, 'transfer'])->name('transfer-kas.create');
    Route::post('transfer-kas', [JurnalKasController::class, 'storeTransfer'])->name('transfer-kas.store');
    Route::get('invoice-items/pending', [InvoiceItemController::class, 'getPendingItems'])->name('invoice-items.pending');
    Route::get('invoice/generate-monthly-dlh', function () {
        return redirect()->route('admin.invoice.index')->with('info', 'Silakan gunakan tombol "Rekap Invoice DLH Bulanan" untuk membuat tagihan.');
    });
    Route::post('invoice/generate-monthly-dlh', [InvoiceAdminController::class, 'generateMonthlyDlh'])->name('invoice.generate-monthly-dlh');
    Route::get('invoice/preview-monthly-dlh', [InvoiceAdminController::class, 'previewMonthlyDlh'])->name('invoice.preview-monthly-dlh');
    Route::post('invoice/merge-drafts', [InvoiceAdminController::class, 'mergeDrafts'])->name('invoice.merge-drafts');
    Route::post('invoice/{invoice}/sync-dlh', [InvoiceAdminController::class, 'syncDlhItems'])->name('invoice.sync-dlh');
    Route::post('invoice/{invoice}/recalculate', [InvoiceAdminController::class, 'recalculate'])->name('invoice.recalculate');
    Route::post('invoice/{invoice}/send-wa', [InvoiceAdminController::class, 'sendWhatsappReminder'])->name('invoice.send-wa');
    Route::get('invoice/swasta-lunas', [InvoiceAdminController::class, 'swastaLunas'])->name('invoice.swasta-lunas');
    Route::post('invoice/rebuild-all-journals', [InvoiceAdminController::class, 'rebuildAllJournals'])->name('invoice.rebuild-all-journals');
    Route::post('invoice/{invoice}/rebuild-journal', [InvoiceAdminController::class, 'rebuildJournal'])->name('invoice.rebuild-journal');
    Route::post('invoice/purge-selected', [InvoiceAdminController::class, 'purgeSelected'])->name('invoice.purge-selected');
    Route::resource('invoice', InvoiceAdminController::class);
    Route::post('invoice/{invoice}/purge', [InvoiceAdminController::class, 'purge'])->name('invoice.purge');

    // Buku Pembantu
    Route::get('buku-pembantu/piutang', [\App\Http\Controllers\Admin\BukuPembantuController::class, 'piutang'])->name('buku-pembantu.piutang');
    Route::get('buku-pembantu/utang', [\App\Http\Controllers\Admin\BukuPembantuController::class, 'utang'])->name('buku-pembantu.utang');
    Route::post('buku-pembantu/sync-status', [\App\Http\Controllers\Admin\BukuPembantuController::class, 'syncStatus'])->name('buku-pembantu.sync-status');

    // Tracing Transaksi & Audit Trail
    Route::get('tracing', [\App\Http\Controllers\Admin\TracingController::class, 'index'])->name('tracing.index');
    Route::get('tracing/detail/{type}/{id}', [\App\Http\Controllers\Admin\TracingController::class, 'show'])->name('tracing.show');
    Route::get('tracing/audit-check', [\App\Http\Controllers\Admin\TracingController::class, 'auditCheck'])->name('tracing.audit');
    Route::post('tracing/sync', [\App\Http\Controllers\Admin\TracingController::class, 'syncDiscrepancies'])->name('tracing.sync');

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
        Route::get('buku-kas', [LaporanController::class, 'bukuKas'])->name('buku-kas');
        Route::get('buku-bank', [LaporanController::class, 'bukuBank'])->name('buku-bank');
    });

    // Laporan Operasional
    Route::prefix('laporan-operasional')->name('laporan-operasional.')->group(function () {
        Route::get('ritase', [LaporanController::class, 'laporanRitase'])->name('ritase');
        Route::get('rekap-ritase', [LaporanController::class, 'rekapRitase'])->name('rekap-ritase');
        Route::get('rekap-ritase-2', [LaporanController::class, 'rekapRitase2'])->name('rekap-ritase-2');
        Route::get('penjualan', [LaporanController::class, 'laporanPenjualan'])->name('penjualan');
        Route::get('penjualan-per-klien', [LaporanController::class, 'penjualanPerKlien'])->name('penjualan-per-klien');
        Route::get('penjualan-hasil-pilahan-per-offtaker-per-invoice', [LaporanController::class, 'penjualanPerOfftakerPerInvoice'])->name('penjualan.per-offtaker-per-invoice');
        Route::get('hasil-pilahan', [LaporanController::class, 'laporanHasilPilahan'])->name('hasil-pilahan');
        Route::get('kartu-stok-item', [LaporanController::class, 'kartuStokItem'])->name('kartu-stok-item');
        Route::get('residu', [LaporanController::class, 'laporanResidu'])->name('residu');
        Route::get('kehadiran', [LaporanController::class, 'laporanKehadiran'])->name('kehadiran');
        Route::get('upah', [LaporanController::class, 'laporanUpah'])->name('upah');
        Route::get('upah/borongan', [LaporanController::class, 'laporanUpah'])->name('upah.borongan')->defaults('skema', 'borongan');
        Route::get('upah/bulanan', [LaporanController::class, 'laporanUpah'])->name('upah.bulanan')->defaults('skema', 'bulanan');
        Route::get('upah/harian', [LaporanController::class, 'laporanUpah'])->name('upah.harian')->defaults('skema', 'harian');
        Route::get('invoice/per-klien', [LaporanController::class, 'invoicePerKlien'])->name('invoice.per-klien');
        Route::get('invoice/per-status', [LaporanController::class, 'invoicePerStatus'])->name('invoice.per-status');
        Route::get('invoice/per-jenis', [LaporanController::class, 'invoicePerJenis'])->name('invoice.per-jenis');
        Route::get('validasi-tipping-fee', [LaporanController::class, 'validasiTippingFee'])->name('validasi-tipping-fee');
        Route::get('ritase-rerata-bulanan', [LaporanController::class, 'ritaseRerataBulanan'])->name('ritase-rerata-bulanan');
        Route::get('residu-rerata-bulanan', [LaporanController::class, 'residuRerataBulanan'])->name('residu-rerata-bulanan');
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
        
        Route::resource('wage-calculation', WageCalculationController::class);
        
        Route::resource('employee', EmployeeController::class);
        Route::match(['post', 'patch'], 'wage-calculation/{wageCalculation}/approve', [WageCalculationController::class, 'approve'])->name('wage-calculation.approve');
        Route::match(['post', 'patch'], 'wage-calculation/{wageCalculation}/pay', [WageCalculationController::class, 'pay'])->name('wage-calculation.pay');
        Route::post('wage-calculation/{wageCalculation}/recalculate', [WageCalculationController::class, 'recalculate'])->name('wage-calculation.recalculate');
        Route::get('wage-calculation/{wageCalculation}/export-slip', [WageCalculationController::class, 'exportSlip'])->name('wage-calculation.export-slip');
    });

    // Statistik Komparatif
    Route::prefix('statistik-komparatif')->name('statistik-komparatif.')->group(function () {
        Route::get('ritase-residu', [\App\Http\Controllers\Admin\StatistikKomparatifController::class, 'ritaseResidu'])->name('ritase-residu');
        Route::get('klien', [\App\Http\Controllers\Admin\StatistikKomparatifController::class, 'klien'])->name('klien');
        Route::get('keuangan', [\App\Http\Controllers\Admin\StatistikKomparatifController::class, 'keuangan'])->name('keuangan');
        Route::get('produksi-penjualan', [\App\Http\Controllers\Admin\StatistikKomparatifController::class, 'produksiPenjualan'])->name('produksi-penjualan');
        Route::get('tonase-sumber', [\App\Http\Controllers\Admin\StatistikKomparatifController::class, 'tonasePerSumber'])->name('tonase-sumber');
        Route::get('tonase-sumber/export-pdf', [\App\Http\Controllers\Admin\StatistikKomparatifController::class, 'exportTonasePdf'])->name('tonase-sumber.export-pdf');
        Route::get('tonase-sumber/export-excel', [\App\Http\Controllers\Admin\StatistikKomparatifController::class, 'exportTonaseExcel'])->name('tonase-sumber.export-excel');
        Route::get('hasil-pilahan', [\App\Http\Controllers\Admin\StatistikKomparatifController::class, 'hasilPilahan'])->name('hasil-pilahan');
    });

    // AI Assistant
    Route::prefix('ai-assistant')->name('ai-assistant.')->group(function () {
        Route::post('chat', [AiAssistantController::class, 'chat'])->name('chat');
        Route::get('history', [AiAssistantController::class, 'history'])->name('history');
        Route::delete('history', [AiAssistantController::class, 'clearHistory'])->name('history.clear');
        Route::post('new-session', [AiAssistantController::class, 'newSession'])->name('new-session');
    });
});
