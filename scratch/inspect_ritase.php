<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ritase;

// Let's inspect Ritase records where klien jenis is 'DLH' and see if there are records that don't match the formula
$ritases = Ritase::whereHas('klien', function ($q) {
    $q->where('jenis', 'DLH');
})->get();

$discrepancies = [];

foreach ($ritases as $r) {
    $expected = ($r->berat_netto / 1000) * 80000;
    $actual = $r->biaya_tipping;
    $diff = abs($actual - $expected);
    
    if ($diff > 0.01) {
        $discrepancies[] = [
            'id' => $r->id,
            'nomor_tiket' => $r->nomor_tiket,
            'klien_id' => $r->klien_id,
            'klien_name' => $r->klien->nama_klien,
            'klien_jenis_tarif' => $r->klien->jenis_tarif,
            'klien_besaran_tarif' => $r->klien->besaran_tarif,
            'berat_netto' => $r->berat_netto,
            'biaya_tipping' => $actual,
            'expected_tipping' => $expected,
            'diff' => $actual - $expected,
        ];
    }
}

echo "Total DLH Ritase: " . $ritases->count() . "\n";
echo "Total Discrepancies: " . count($discrepancies) . "\n\n";

if (count($discrepancies) > 0) {
    echo "Discrepant Records:\n";
    foreach (array_slice($discrepancies, 0, 20) as $d) {
        echo "ID: {$d['id']} | Tiket: {$d['nomor_tiket']} | Klien: {$d['klien_name']} (ID: {$d['klien_id']}) | Netto: {$d['berat_netto']} kg | Tipping: Rp " . number_format($d['biaya_tipping'], 2) . " | Expected: Rp " . number_format($d['expected_tipping'], 2) . " | Diff: Rp " . number_format($d['diff'], 2) . "\n";
    }
}
