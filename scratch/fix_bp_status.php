<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BukuPembantu;

$affected = 0;
BukuPembantu::withoutGlobalScopes()
    ->where('status', 'pending')
    ->whereColumn('terbayar', '>=', 'jumlah')
    ->each(function($entry) use (&$affected) {
        $entry->status = 'lunas';
        $entry->save();
        $affected++;
        $namaKlien = $entry->contactable ? ($entry->contactable->nama_klien ?? 'N/A') : 'N/A';
        echo "Fixed ID {$entry->id}: {$namaKlien} - Jumlah: {$entry->jumlah}, Terbayar: {$entry->terbayar}\n";
    });

echo "\nDone. Fixed $affected entries.\n";
