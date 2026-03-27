<?php

namespace App\Observers;

use App\Models\Coa;
use App\Models\JurnalHeader;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class PenjualanObserver
{
    /**
     * Handle the Penjualan "created" event.
     * 
     * Automatically creates journal entries when a penjualan is created.
     * Debit: Piutang Usaha / Kas
     * Credit: Pendapatan Penjualan
     */
    public function created(Penjualan $penjualan): void
    {
        // Moved to InvoiceObserver for consolidated journaling
    }

    /**
     * Handle the Penjualan "updated" event.
     */
    public function updated(Penjualan $penjualan): void
    {
        // Optional: Handle updates if total_harga changes
    }

    /**
     * Handle the Penjualan "deleted" event.
     */
    public function deleted(Penjualan $penjualan): void
    {
        JurnalHeader::where('referensi_type', Penjualan::class)
            ->where('referensi_id', $penjualan->id)
            ->get()->each->delete();

        // Update parent invoice total
        if ($penjualan->invoice_id) {
            $invoice = \App\Models\Invoice::find($penjualan->invoice_id);
            if ($invoice) {
                $totalRitase = $invoice->ritase()->sum('biaya_tipping');
                $totalPenjualan = $invoice->penjualan()->sum('total_harga');
                $invoice->update(['total_tagihan' => $totalRitase + $totalPenjualan]);
            }
        }
    }
}
