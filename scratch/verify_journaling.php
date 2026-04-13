<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use App\Models\Klien;
use App\Models\Coa;
use App\Models\JurnalHeader;
use Illuminate\Support\Facades\DB;

// Use a real tenant or mock one
$tenantId = 1;

// 1. Prepare COAs
$piutangDLH = Coa::where('tenant_id', $tenantId)->where('kode_akun', 'like', '1103%')->first();
$piutangSwasta = Coa::where('tenant_id', $tenantId)->where('kode_akun', 'like', '1104%')->first();
$bank = Coa::where('tenant_id', $tenantId)->where('kode_akun', '1102')->first();
$kas = Coa::where('tenant_id', $tenantId)->where('kode_akun', '1101')->first();

if (!$piutangDLH || !$piutangSwasta || !$bank || !$kas) {
    echo "Missing COAs for testing. Please check tenant $tenantId.\n";
    exit(1);
}

// 2. Test Case: DLH Client, Sent status, Bank payment
$klienDLH = Klien::where('jenis', 'DLH')->first();
if ($klienDLH) {
    echo "Testing DLH Klien...\n";
    $invoice = Invoice::create([
        'tenant_id' => $tenantId,
        'klien_id' => $klienDLH->id,
        'nomor_invoice' => 'TEST-DLH-001-' . time(),
        'tanggal_invoice' => now(),
        'tanggal_jatuh_tempo' => now()->addDays(14),
        'periode_bulan' => '04',
        'periode_tahun' => '2026',
        'total_tagihan' => 1000000,
        'uang_muka' => 200000,
        'status' => 'Sent',
        'coa_pembayaran_id' => $bank->id,
    ]);

    $jurnal = JurnalHeader::where('referensi_type', Invoice::class)->where('referensi_id', $invoice->id)->first();
    if ($jurnal) {
        $details = $jurnal->jurnalDetails;
        echo "Journal for Sent Invoice created.\n";
        foreach ($details as $d) {
            echo "  - Account: {$d->coa->nama_akun} ({$d->coa->kode_akun}) Debit: {$d->debit} Kredit: {$d->kredit}\n";
        }
    } else {
        echo "FAILED: Journal not created for Sent Invoice.\n";
    }

    // 3. Update to Paid
    echo "Updating to Paid...\n";
    $invoice->update(['status' => 'Paid']);
    $jurnal->refresh();
    $details = $jurnal->jurnalDetails()->get();
    echo "Journal for Paid Invoice updated.\n";
    foreach ($details as $d) {
        echo "  - Account: {$d->coa->nama_akun} ({$d->coa->kode_akun}) Debit: {$d->debit} Kredit: {$d->kredit}\n";
    }
}

// 4. Test Case: Swasta Client, Sent status, Kas payment
$klienSwasta = Klien::where('jenis', 'Swasta')->first();
if ($klienSwasta) {
    echo "\nTesting Swasta Klien with Kas...\n";
    $invoice2 = Invoice::create([
        'tenant_id' => $tenantId,
        'klien_id' => $klienSwasta->id,
        'nomor_invoice' => 'TEST-SWASTA-001-' . time(),
        'tanggal_invoice' => now(),
        'tanggal_jatuh_tempo' => now()->addDays(14),
        'periode_bulan' => '04',
        'periode_tahun' => '2026',
        'total_tagihan' => 500000,
        'uang_muka' => 100000,
        'status' => 'Sent',
        'coa_pembayaran_id' => $kas->id,
    ]);

    $jurnal2 = JurnalHeader::where('referensi_type', Invoice::class)->where('referensi_id', $invoice2->id)->first();
    if ($jurnal2) {
        $details2 = $jurnal2->jurnalDetails;
        echo "Journal for Sent (Kas) created.\n";
        foreach ($details2 as $d) {
            echo "  - Account: {$d->coa->nama_akun} ({$d->coa->kode_akun}) Debit: {$d->debit} Kredit: {$d->kredit}\n";
        }
    }
}
