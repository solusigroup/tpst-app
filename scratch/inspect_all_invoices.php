<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use App\Models\Ritase;
use App\Models\Penjualan;

$invoices = Invoice::withoutGlobalScopes()->get();
echo "Total Invoices: " . $invoices->count() . "\n\n";

foreach ($invoices as $inv) {
    $ritaseCount = Ritase::withoutGlobalScopes()->where('invoice_id', $inv->id)->count();
    $penjualanCount = Penjualan::withoutGlobalScopes()->where('invoice_id', $inv->id)->count();
    
    echo "ID: {$inv->id} | Klien: {$inv->klien->nama_klien} (ID: {$inv->klien_id}) | Nomor: {$inv->nomor_invoice} | Periode: {$inv->periode_bulan}-{$inv->periode_tahun} | Status: {$inv->status} | Total: Rp " . number_format($inv->total_tagihan, 2) . " | Ritase Count: {$ritaseCount} | Penjualan Count: {$penjualanCount}\n";
}
