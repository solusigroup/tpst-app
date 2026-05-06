<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;

echo "Fixing mismatched invoices...\n";
foreach (Invoice::with('ritase', 'penjualan')->get() as $inv) {
    $calculated = $inv->ritase->sum('biaya_tipping') + $inv->penjualan->sum('total_harga');
    if (abs($inv->total_tagihan - $calculated) > 0.01) {
        echo "Updating Invoice #{$inv->id}: {$inv->total_tagihan} -> {$calculated}\n";
        $inv->update(['total_tagihan' => $calculated]);
    }
}
echo "Done!\n";
