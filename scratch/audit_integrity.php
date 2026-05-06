<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ritase;
use App\Models\Invoice;
use App\Models\Klien;
use App\Models\Armada;

echo "--- INTEGRITY AUDIT --- \n";

// 1. Orphaned Ritase (missing Armada)
$orphanArmada = Ritase::whereNotNull('armada_id')->whereNotExists(function($q) {
    $q->select(DB::raw(1))->from('armada')->whereColumn('armada.id', 'ritase.armada_id');
})->count();
echo "Ritase with invalid armada_id: $orphanArmada\n";

// 2. Orphaned Ritase (missing Klien)
$orphanKlien = Ritase::whereNotNull('klien_id')->whereNotExists(function($q) {
    $q->select(DB::raw(1))->from('klien')->whereColumn('klien.id', 'ritase.klien_id');
})->count();
echo "Ritase with invalid klien_id: $orphanKlien\n";

// 3. Orphaned Ritase (missing Invoice)
$orphanInvoice = Ritase::whereNotNull('invoice_id')->whereNotExists(function($q) {
    $q->select(DB::raw(1))->from('invoices')->whereColumn('invoices.id', 'ritase.invoice_id');
})->count();
echo "Ritase with invalid invoice_id: $orphanInvoice\n";

// 4. Invoices with missing Klien
$orphanInvKlien = Invoice::whereNotNull('klien_id')->whereNotExists(function($q) {
    $q->select(DB::raw(1))->from('klien')->whereColumn('klien.id', 'invoices.klien_id');
})->count();
echo "Invoices with invalid klien_id: $orphanInvKlien\n";

// 5. Check if total_tagihan matches sum of items
$mismatchInvoices = 0;
foreach (Invoice::with('ritase', 'penjualan')->get() as $inv) {
    $calculated = $inv->ritase->sum('biaya_tipping') + $inv->penjualan->sum('total_harga');
    if (abs($inv->total_tagihan - $calculated) > 0.01) {
        $mismatchInvoices++;
    }
}
echo "Invoices with total mismatch: $mismatchInvoices\n";

echo "\n--- SYSTEM STATUS ---\n";
echo "Storage link exists: " . (is_dir(public_path('storage')) ? "YES" : "NO") . "\n";
echo "Log file writable: " . (is_writable(storage_path('logs')) ? "YES" : "NO") . "\n";

