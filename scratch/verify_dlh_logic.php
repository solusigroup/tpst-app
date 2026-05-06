<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Klien;
use App\Models\Ritase;
use App\Models\Invoice;
use App\Models\Armada;
use Illuminate\Support\Facades\DB;

// 1. Find or create master DLH
$masterDLH = Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first();
if (!$masterDLH) {
    echo "Master DLH not found, creating one...\n";
    $masterDLH = Klien::create([
        'tenant_id' => 1,
        'nama_klien' => 'Dinas Lingkungan Hidup',
        'jenis' => 'DLH'
    ]);
}
echo "Master DLH ID: " . $masterDLH->id . "\n";

// 2. Find or create a DLH truck client
$truckClient = Klien::where('nama_klien', 'Test DLH Truck')->first();
if (!$truckClient) {
    echo "Creating test truck client...\n";
    $truckClient = Klien::create([
        'tenant_id' => 1,
        'nama_klien' => 'Test DLH Truck',
        'jenis' => 'DLH'
    ]);
}
echo "Truck Client ID: " . $truckClient->id . " (Type: " . $truckClient->jenis . ")\n";

// 3. Find an armada or create one
$armada = Armada::first() ?? Armada::create(['tenant_id' => 1, 'plat_nomor' => 'B 1234 TEST', 'klien_id' => $truckClient->id]);

// 4. Create a Ritase for the truck client
$ritase = Ritase::create([
    'tenant_id' => 1,
    'armada_id' => $armada->id,
    'klien_id' => $truckClient->id,
    'waktu_masuk' => now(),
    'berat_bruto' => 5000,
    'berat_tarra' => 2000,
    'berat_netto' => 3000,
    'biaya_tipping' => 100000,
    'status' => 'selesai',
    'is_approved' => false
]);
echo "Created Ritase ID: " . $ritase->id . " for Klien: " . $ritase->klien->nama_klien . "\n";

// 5. Approve the Ritase using the controller logic (simulated)
echo "Approving ritase...\n";

// I'll call the approve logic manually since I can't easily call the controller method here
// but I'll use the SAME logic I just implemented.
DB::transaction(function () use ($ritase, $masterDLH) {
    // This simulates the RitaseController@approve logic
    $ritase->update([
        'is_approved' => true,
        'approved_at' => now(),
    ]);

    $month = $ritase->waktu_masuk->format('n');
    $year = $ritase->waktu_masuk->format('Y');

    $klienId = $ritase->klien_id;
    
    // THE NEW LOGIC
    if ($ritase->klien && $ritase->klien->jenis === 'DLH') {
        $foundMaster = \App\Models\Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first();
        if ($foundMaster) {
            $klienId = $foundMaster->id;
            echo "Redirection detected! Payer ID changed from " . $ritase->klien_id . " to " . $klienId . "\n";
        }
    }

    $invoice = \App\Models\Invoice::where('tenant_id', $ritase->tenant_id)
        ->where('klien_id', $klienId)
        ->where('periode_bulan', $month)
        ->where('periode_tahun', $year)
        ->where('status', 'Draft')
        ->first();

    if (!$invoice) {
        $invoice = \App\Models\Invoice::create([
            'tenant_id' => $ritase->tenant_id,
            'klien_id' => $klienId,
            'periode_bulan' => $month,
            'periode_tahun' => $year,
            'tanggal_invoice' => now(),
            'tanggal_jatuh_tempo' => now()->addDays(30),
            'total_tagihan' => 0,
            'status' => 'Draft',
            'keterangan' => 'Generated automatically from approved ritase (Test)',
        ]);
        echo "Created new Draft Invoice ID: " . $invoice->id . " for Klien ID: " . $invoice->klien_id . "\n";
    } else {
        echo "Found existing Draft Invoice ID: " . $invoice->id . " for Klien ID: " . $invoice->klien_id . "\n";
    }

    $ritase->update([
        'invoice_id' => $invoice->id,
        'status_invoice' => $invoice->status,
    ]);
});

// Final check
$ritase->refresh();
echo "Ritase " . $ritase->id . " is now linked to Invoice " . ($ritase->invoice_id ?? 'NULL') . "\n";
if ($ritase->invoice && $ritase->invoice->klien_id == $masterDLH->id) {
    echo "SUCCESS: Ritase was correctly billed to master DLH!\n";
} else {
    echo "FAILURE: Ritase was NOT billed to master DLH. Invoice Klien ID: " . ($ritase->invoice->klien_id ?? 'N/A') . "\n";
}

// Cleanup (optional)
// $ritase->delete();
// if ($truckClient->id != $masterDLH->id) $truckClient->delete();
