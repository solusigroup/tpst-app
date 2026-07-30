<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ritase;

$ritases = Ritase::withoutGlobalScopes()
    ->whereYear('waktu_masuk', 2026)
    ->whereMonth('waktu_masuk', 4)
    ->whereHas('klien', function ($q) {
        $q->where('jenis', 'DLH');
    })
    ->get();

echo "Total DLH Ritase in April 2026: " . $ritases->count() . "\n";
echo "Total Netto: " . $ritases->sum('berat_netto') . " kg\n";
echo "Total Tipping Fee in DB: Rp " . number_format($ritases->sum('biaya_tipping'), 2) . "\n";
echo "Expected Tipping Fee (80k/ton): Rp " . number_format(($ritases->sum('berat_netto') / 1000) * 80000, 2) . "\n\n";

$groupedByInvoice = $ritases->groupBy('invoice_id');
foreach ($groupedByInvoice as $invId => $items) {
    echo "Invoice ID: " . ($invId ?: 'NULL') . " | Count: " . $items->count() . " | Netto: " . $items->sum('berat_netto') . " kg | Tipping: Rp " . number_format($items->sum('biaya_tipping'), 2) . "\n";
}
