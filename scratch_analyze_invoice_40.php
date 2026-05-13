<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Invoice;
use App\Models\Klien;
use App\Models\Ritase;

$invoice = Invoice::with(['klien', 'ritase'])->find(40);

if (!$invoice) {
    echo "Invoice 40 not found\n";
    exit;
}

echo "Invoice ID: " . $invoice->id . "\n";
echo "Klien: " . $invoice->klien->nama_klien . "\n";
echo "Jenis Tarif: " . $invoice->klien->jenis_tarif . "\n";
echo "Besaran Tarif: " . $invoice->klien->besaran_tarif . "\n";

foreach ($invoice->ritase as $ritase) {
    echo "Ritase ID: " . $ritase->id . "\n";
    echo "Berat Netto: " . $ritase->berat_netto . "\n";
    echo "Biaya Tipping: " . $ritase->biaya_tipping . "\n";
}
