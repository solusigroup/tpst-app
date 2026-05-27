<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $q = \App\Models\Invoice::selectRaw('klien_id, COUNT(id) as jumlah_invoice, SUM(total_tagihan) as total_tagihan, SUM(uang_muka) as total_dibayar, SUM(total_tagihan - uang_muka) as sisa_tagihan')->groupBy('klien_id')->get();
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
