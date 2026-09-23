<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ritase;
use App\Models\MasterAsalSampah;
use Illuminate\Support\Facades\DB;

// Known typos to fix in historical ritase
$corrections = [
    'SMA N 1 Lmongan' => 'SMA 1 Lamongan',
    'SMP 2 Lamonga' => 'SMP 2 Lamongan',
    'Pt. Ever age valves' => 'PT Ever Age Valves',
    'Kebet residece' => 'Kebet Residence',
    'KUD Tani Mulyo' => 'KUD Tani Mulya',
    'Dsn. Turi' => 'Dusun Turi',
    'sampah dsn. turi' => 'Dusun Turi',
    'desa mlaten' => 'Dusun Mlaten',
    'Dsn mlaten' => 'Dusun Mlaten',
    'mlaten' => 'Dusun Mlaten',
    'pt bulyet' => 'PT Buildyed',
    'PT Buildyet' => 'PT Buildyed',
    'Pt. Buildyet' => 'PT Buildyed',
    'PT. Buildyed' => 'PT Buildyed',
    'Buildyed' => 'PT Buildyed',
    'desa mbalun' => 'Dusun Balun',
    'Dusn Balun' => 'Dusun Balun',
    'Dusun Mbalun' => 'Dusun Balun',
    'Balun' => 'Dusun Balun',
    'Kebet' => 'Dusun Kebet',
    'Desa Karang Langit' => 'Desa Karanglangit',
    'Dusun Karanglangit' => 'Desa Karanglangit',
    'Desa Ringin Anom Lopang' => 'Desa Ringin Anom',
    'Dusun Ringin Anom' => 'Desa Ringin Anom',
    'Ringin anom' => 'Desa Ringin Anom',
    'Desa Tambakploso' => 'Desa Tambak Ploso',
    'Tambak Ploso' => 'Desa Tambak Ploso',
    'Tambakploso' => 'Desa Tambak Ploso',
    'Perum Ababil' => 'Perumahan Ababil',
    'Perum Insani' => 'Perumahan Insani Made',
    'Perum Insani Made' => 'Perumahan Insani Made',
    'Peenyisir' => 'Penyisir',
    'Peny' => 'Penyisir',
    'Tim Penyisir' => 'Penyisir',
    'pt star food' => 'PT Star Food',
    'pasar burung' => 'Pasar Burung',
    'pasar' => 'Pasar',
    'Lapangan gajah mada' => 'Lapangan Gajah Mada',
    'Wringin Anom Lapang' => 'Wringin Anom Lapangan',
];

echo "1. Cleaning up known typos in ritase table...\n";
foreach ($corrections as $typo => $fixed) {
    $updated = Ritase::withoutGlobalScopes()->where('jenis_sampah', $typo)->update(['jenis_sampah' => $fixed]);
    if ($updated > 0) {
        echo " - Updated '$typo' -> '$fixed' ($updated rows)\n";
    }
}

// Normalize multiple spaces and trim across all records
$allRitase = Ritase::withoutGlobalScopes()->whereNotNull('jenis_sampah')->get();
foreach ($allRitase as $r) {
    $clean = preg_replace('/\s+/', ' ', trim($r->jenis_sampah));
    if ($clean !== $r->jenis_sampah) {
        $r->update(['jenis_sampah' => $clean]);
    }
}

echo "\n2. Populating master_asal_sampah from cleaned ritase records...\n";
$distincts = Ritase::withoutGlobalScopes()
    ->selectRaw('tenant_id, klien_id, jenis_sampah')
    ->whereNotNull('jenis_sampah')
    ->where('jenis_sampah', '!=', '')
    ->whereNotNull('klien_id')
    ->groupBy('tenant_id', 'klien_id', 'jenis_sampah')
    ->get();

$inserted = 0;
foreach ($distincts as $d) {
    $name = preg_replace('/\s+/', ' ', trim($d->jenis_sampah));
    $entry = MasterAsalSampah::withoutGlobalScopes()->firstOrCreate([
        'tenant_id' => $d->tenant_id,
        'klien_id' => $d->klien_id,
        'nama_asal_sampah' => $name,
    ], [
        'is_active' => true,
    ]);
    if ($entry->wasRecentlyCreated) {
        $inserted++;
    }
}

echo "Done! Inserted $inserted unique master Asal Sampah records.\n";
echo "Total in master_asal_sampah: " . MasterAsalSampah::withoutGlobalScopes()->count() . "\n";
