<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Klien;

$kliens = Klien::where('jenis', 'DLH')->get(['id', 'nama_klien', 'jenis', 'jenis_tarif', 'besaran_tarif']);
foreach ($kliens as $klien) {
    echo "ID: {$klien->id} | Name: {$klien->nama_klien} | Jenis: {$klien->jenis} | Tarif: {$klien->jenis_tarif} | Besaran: " . ($klien->besaran_tarif ?? 'NULL') . "\n";
}
