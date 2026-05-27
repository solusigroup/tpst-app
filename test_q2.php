<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $dari = '2026-01-01';
    $sampai = '2026-12-31';
    
    echo "1. Per Klien...\n";
    $q1 = \App\Models\Invoice::with('klien')
        ->whereDate('tanggal_invoice', '>=', $dari)
        ->whereDate('tanggal_invoice', '<=', $sampai)
        ->selectRaw('klien_id, COUNT(id) as jumlah_invoice, COALESCE(SUM(total_tagihan), 0) as total_tagihan, COALESCE(SUM(uang_muka), 0) as total_dibayar, COALESCE(SUM(total_tagihan - COALESCE(uang_muka, 0)), 0) as sisa_tagihan')
        ->groupBy('klien_id')
        ->get();
    echo "SUCCESS\n";
    
    echo "2. Per Status...\n";
    $q2 = \App\Models\Invoice::whereDate('tanggal_invoice', '>=', $dari)
        ->whereDate('tanggal_invoice', '<=', $sampai)
        ->selectRaw('status, COUNT(id) as jumlah_invoice, COALESCE(SUM(total_tagihan), 0) as total_tagihan, COALESCE(SUM(uang_muka), 0) as total_dibayar, COALESCE(SUM(total_tagihan - COALESCE(uang_muka, 0)), 0) as sisa_tagihan')
        ->groupBy('status')
        ->get();
    echo "SUCCESS\n";
    
    echo "3. Per Jenis...\n";
    $q3 = \App\Models\Invoice::join('klien', 'invoices.klien_id', '=', 'klien.id')
        ->whereDate('invoices.tanggal_invoice', '>=', $dari)
        ->whereDate('invoices.tanggal_invoice', '<=', $sampai)
        ->selectRaw('klien.jenis_tarif, COUNT(invoices.id) as jumlah_invoice, COALESCE(SUM(invoices.total_tagihan), 0) as total_tagihan, COALESCE(SUM(invoices.uang_muka), 0) as total_dibayar, COALESCE(SUM(invoices.total_tagihan - COALESCE(invoices.uang_muka, 0)), 0) as sisa_tagihan')
        ->groupBy('klien.jenis_tarif')
        ->get();
    echo "SUCCESS\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
