<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Klien;
use App\Models\Ritase;
use App\Models\Invoice;

echo "=== CLIENTS INVOLVING DLH / DINAS ===\n";
$kliens = Klien::where('nama_klien', 'like', '%dinas%')
    ->orWhere('nama_klien', 'like', '%dlh%')
    ->orWhere('jenis', 'DLH')
    ->get();

foreach ($kliens as $klien) {
    echo "ID: {$klien->id} | Nama: {$klien->nama_klien} | Jenis: {$klien->jenis} | Jenis Tarif: {$klien->jenis_tarif} | Besaran: {$klien->besaran_tarif}\n";
    
    $ritaseCount = Ritase::where('klien_id', $klien->id)->count();
    $ritaseNettoSum = Ritase::where('klien_id', $klien->id)->sum('berat_netto');
    $ritaseTippingSum = Ritase::where('klien_id', $klien->id)->sum('biaya_tipping');
    echo "   -> Ritase count: $ritaseCount | Total Netto: $ritaseNettoSum kg | Total Tipping: Rp " . number_format($ritaseTippingSum, 2) . "\n";
    
    // Calculated expected tipping fee: (berat_netto / 1000) * 80000
    $expectedTipping = ($ritaseNettoSum / 1000) * 80000;
    $diff = $ritaseTippingSum - $expectedTipping;
    echo "   -> Expected (Netto Ton * 80,000): Rp " . number_format($expectedTipping, 2) . " | Diff: Rp " . number_format($diff, 2) . "\n\n";
}

echo "=== INVOICES INVOLVING DLH / DINAS ===\n";
$invoices = Invoice::whereIn('klien_id', $kliens->pluck('id'))->get();
foreach ($invoices as $inv) {
    echo "ID: {$inv->id} | Klien ID: {$inv->klien_id} | Periode: {$inv->periode_bulan}-{$inv->periode_tahun} | Total Tagihan: Rp " . number_format($inv->total_tagihan, 2) . " | Status: {$inv->status}\n";
    
    // Get ritase for this invoice
    $ritaseInInvoice = Ritase::where('invoice_id', $inv->id)->get();
    $ritaseCount = $ritaseInInvoice->count();
    $ritaseNettoSum = $ritaseInInvoice->sum('berat_netto');
    $ritaseTippingSum = $ritaseInInvoice->sum('biaya_tipping');
    
    echo "   -> Ritase in Invoice count: $ritaseCount | Total Netto: $ritaseNettoSum kg | Sum of biaya_tipping in ritase: Rp " . number_format($ritaseTippingSum, 2) . "\n";
    $expectedInvTipping = ($ritaseNettoSum / 1000) * 80000;
    echo "   -> Expected invoice tipping: Rp " . number_format($expectedInvTipping, 2) . " | Diff: Rp " . number_format($inv->total_tagihan - $expectedInvTipping, 2) . "\n\n";
}
