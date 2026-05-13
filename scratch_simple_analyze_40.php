<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
// We don't need to handle the request, just use the app
use App\Models\Invoice;
$invoice = Invoice::with(['klien', 'ritase'])->find(40);
if ($invoice) {
    echo "Klien: " . $invoice->klien->nama_klien . "\n";
    echo "Jenis Tarif: " . $invoice->klien->jenis_tarif . "\n";
    echo "Besaran Tarif: " . $invoice->klien->besaran_tarif . "\n";
    foreach ($invoice->ritase as $r) {
        echo "Ritase ID: " . $r->id . ", Netto: " . $r->berat_netto . ", Tipping: " . $r->biaya_tipping . "\n";
    }
} else {
    echo "Invoice 40 not found\n";
}
