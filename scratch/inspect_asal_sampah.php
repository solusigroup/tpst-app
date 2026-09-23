<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = App\Models\Ritase::with('klien')
    ->selectRaw('klien_id, jenis_sampah, count(*) as count')
    ->whereNotNull('jenis_sampah')
    ->where('jenis_sampah', '!=', '')
    ->groupBy('klien_id', 'jenis_sampah')
    ->orderBy('klien_id')
    ->orderBy('jenis_sampah')
    ->get();

echo "Total unique client-source combinations: " . $items->count() . "\n";
foreach ($items as $i) {
    echo "Klien: [" . ($i->klien->nama_klien ?? $i->klien_id) . "] | Asal Sampah: " . $i->jenis_sampah . " (" . $i->count . " ritase)\n";
}
