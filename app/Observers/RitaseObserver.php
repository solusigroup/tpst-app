<?php

namespace App\Observers;

use App\Models\Coa;
use App\Models\JurnalHeader;
use App\Models\Ritase;
use Illuminate\Support\Facades\DB;

class RitaseObserver
{
    /**
     * Handle the Ritase "created" event.
     * 
     * Automatically creates journal entries when a ritase is created with biaya_tipping > 0.
     * Debit: Piutang Usaha / Kas
     * Credit: Pendapatan Tipping
     */
    public function created(Ritase $ritase): void
    {
        // Moved to InvoiceObserver for consolidated journaling
    }

    /**
     * Handle the Ritase "updated" event.
     */
    public function updated(Ritase $ritase): void
    {
        // Optional: Handle updates if biaya_tipping changes
    }

    /**
     * Handle the Ritase "deleted" event.
     */
    public function deleted(Ritase $ritase): void
    {
        JurnalHeader::where('referensi_type', Ritase::class)
            ->where('referensi_id', $ritase->id)
            ->get()->each->delete();

        // Update parent invoice total
        if ($ritase->invoice_id) {
            $invoice = \App\Models\Invoice::find($ritase->invoice_id);
            if ($invoice) {
                $totalRitase = $invoice->ritase()->sum('biaya_tipping');
                $totalPenjualan = $invoice->penjualan()->sum('total_harga');
                $invoice->update(['total_tagihan' => $totalRitase + $totalPenjualan]);
            }
        }
    }
}
