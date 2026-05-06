<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BukuPembantu;

$all = BukuPembantu::withoutGlobalScopes()->get();
echo "Total entries: " . $all->count() . "\n";
foreach ($all as $entry) {
    $outstanding = $entry->jumlah - $entry->terbayar;
    if ($outstanding <= 0 && $entry->status == 'pending') {
        echo "MISMATCH - ID: {$entry->id}, Jumlah: {$entry->jumlah}, Terbayar: {$entry->terbayar}, Status: {$entry->status}, Klien: " . ($entry->contactable->nama_klien ?? 'N/A') . "\n";
    }
}

