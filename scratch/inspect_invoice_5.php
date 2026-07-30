<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use App\Models\Ritase;

$invoice = Invoice::withoutGlobalScopes()->find(5);
if (!$invoice) {
    echo "Invoice 5 not found.\n";
    exit;
}

echo "Invoice 5 Details:\n";
echo "ID: {$invoice->id} | Klien ID: {$invoice->klien_id} | Total Tagihan: Rp " . number_format($invoice->total_tagihan, 2) . " | Status: {$invoice->status}\n";

$allRitase = Ritase::withoutGlobalScopes()->where('invoice_id', 5)->get();
echo "Total associated Ritase records (regardless of scope/approval): " . $allRitase->count() . "\n";

$approvedRitase = Ritase::withoutGlobalScopes()->where('invoice_id', 5)->where('is_approved', 1)->get();
echo "Total approved Ritase records: " . $approvedRitase->count() . "\n";

$unapprovedRitase = Ritase::withoutGlobalScopes()->where('invoice_id', 5)->where('is_approved', 0)->get();
echo "Total unapproved Ritase records: " . $unapprovedRitase->count() . "\n";

foreach ($allRitase->take(10) as $r) {
    echo "   -> Ritase ID: {$r->id} | Tiket: {$r->nomor_tiket} | Netto: {$r->berat_netto} kg | Tipping: Rp " . number_format($r->biaya_tipping, 2) . " | Approved: " . ($r->is_approved ? 'Yes' : 'No') . " | Tenant ID: {$r->tenant_id}\n";
}
