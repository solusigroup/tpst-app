<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BukuPembantu;
use App\Models\JurnalHeader;

class LedgerCleanupSeeder extends Seeder
{
    public function run()
    {
        // 1. Delete JurnalHeaders where the reference (Ritase, Penjualan, Invoice) is missing
        $orphanedHeaders = JurnalHeader::whereNotNull('referensi_type')
            ->get()
            ->filter(function($header) {
                return $header->referensi === null;
            });
        
        $countHeaders = $orphanedHeaders->count();
        foreach($orphanedHeaders as $h) {
            $h->delete(); // Cascades to JurnalDetail and BukuPembantu via model events
        }

        // 2. Delete BukuPembantu where JurnalHeader is missing
        $orphansBP = BukuPembantu::whereDoesntHave('jurnalHeader')->delete();

        echo "Cleanup complete:\n";
        echo "- Deleted $countHeaders orphaned JurnalHeaders (and their details/ledger entries).\n";
        echo "- Deleted $orphansBP orphaned BukuPembantu entries.\n";
    }
}
