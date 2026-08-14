<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoices = App\Models\Invoice::where('status', 'Paid')->with('jurnalHeaders')->get();
echo "Total paid invoices found: " . $invoices->count() . PHP_EOL;

foreach ($invoices as $inv) {
    echo "----------------------------------------" . PHP_EOL;
    echo "Invoice ID: {$inv->id} | No: {$inv->nomor_invoice} | Status: {$inv->status} | UpdatedAt: {$inv->updated_at}" . PHP_EOL;
    
    // Check JurnalHeaders directly linked to invoice
    foreach ($inv->jurnalHeaders as $jh) {
        echo "  [JH #{$jh->id}] Tanggal: {$jh->tanggal} | Desk: {$jh->deskripsi}" . PHP_EOL;
        
        $bps = App\Models\BukuPembantu::where('jurnal_header_id', $jh->id)->get();
        foreach ($bps as $bp) {
            echo "    [BP #{$bp->id}] Status: {$bp->status} | SettledBy JH ID: {$bp->settled_by_jurnal_header_id}" . PHP_EOL;
            if ($bp->settled_by_jurnal_header_id) {
                $settledJh = App\Models\JurnalHeader::find($bp->settled_by_jurnal_header_id);
                echo "      -> Settled JH Tanggal: " . ($settledJh ? $settledJh->tanggal->format('Y-m-d') : 'N/A') . PHP_EOL;
            }
        }
    }
}
