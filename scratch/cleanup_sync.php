<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ritase;
use App\Models\Invoice;
use App\Models\Klien;
use Illuminate\Support\Facades\DB;

DB::transaction(function() {
    echo "1. Normalizing Invoice Periods (removing leading zeros from months)...\n";
    $allInvoices = Invoice::all();
    foreach ($allInvoices as $inv) {
        $normalizedMonth = (string)(int)$inv->periode_bulan;
        if ($inv->periode_bulan !== $normalizedMonth) {
            $inv->update(['periode_bulan' => $normalizedMonth]);
            echo "   Updated Invoice #{$inv->id}: '{$inv->periode_bulan}' -> '{$normalizedMonth}'\n";
        }
    }

    echo "2. Setting status to 'selesai' for all invoiced ritase...\n";
    $updatedRitase = Ritase::whereNotNull('invoice_id')->update(['status' => 'selesai']);
    echo "   Updated {$updatedRitase} ritase records.\n";

    echo "3. Consolidating DLH Draft Invoices for April 2026...\n";
    $masterDLH = Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first();
    if ($masterDLH) {
        // Find all DLH Drafts for April 2026
        $dlhDrafts = Invoice::where('klien_id', $masterDLH->id)
            ->where('periode_bulan', '4')
            ->where('periode_tahun', '2026')
            ->where('status', 'Draft')
            ->get();
            
        if ($dlhDrafts->count() > 1) {
            $master = $dlhDrafts->first();
            $others = $dlhDrafts->slice(1);
            foreach ($others as $other) {
                Ritase::where('invoice_id', $other->id)->update(['invoice_id' => $master->id]);
                \App\Models\Penjualan::where('invoice_id', $other->id)->update(['invoice_id' => $master->id]);
                $other->delete();
                echo "   Merged Invoice #{$other->id} into #{$master->id}\n";
            }
            
            // Recalculate master
            $totalRitase = Ritase::where('invoice_id', $master->id)->sum('biaya_tipping');
            $totalPenjualan = \App\Models\Penjualan::where('invoice_id', $master->id)->sum('total_harga');
            $master->update(['total_tagihan' => $totalRitase + $totalPenjualan]);
            echo "   Recalculated Master Invoice #{$master->id}: Total Rp " . number_format($master->total_tagihan, 0, ',', '.') . "\n";
        } else {
            echo "   No duplicate DLH drafts found for April 2026.\n";
        }
    }
});
echo "Cleanup completed!\n";
