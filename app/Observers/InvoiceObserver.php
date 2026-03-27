<?php

namespace App\Observers;

use App\Models\Coa;
use App\Models\Invoice;
use App\Models\JurnalHeader;
use Illuminate\Support\Facades\DB;

class InvoiceObserver
{
    /**
     * Handle the Invoice "saved" event.
     */
    public function saved(Invoice $invoice): void
    {
        // 1. If status is Draft or Canceled, delete any existing journal for this invoice
        // This ensures the ledger remains clean if an invoice is retracted
        if (in_array($invoice->status, ['Draft', 'Canceled'])) {
            JurnalHeader::where('referensi_type', Invoice::class)
                ->where('referensi_id', $invoice->id)
                ->get()->each->delete();
            return;
        }

        // 2. Only generate journal if status is Sent or Paid. 
        if (!in_array($invoice->status, ['Sent', 'Paid'])) {
            return;
        }

        try {
            DB::transaction(function () use ($invoice) {
                // Check if a JurnalHeader already exists for this Invoice
                $jurnalHeader = JurnalHeader::where('tenant_id', $invoice->tenant_id)
                    ->where('referensi_type', Invoice::class)
                    ->where('referensi_id', $invoice->id)
                    ->first();

                if (!$jurnalHeader) {
                    // Account lookup
                    $piutangCoa = Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('tipe', 'Asset')
                        ->where('kode_akun', 'like', '1103%') // Standardizing on the Piutang codes we used earlier
                        ->first();

                    if (!$piutangCoa) {
                        $piutangCoa = Coa::where('tenant_id', $invoice->tenant_id)
                            ->where('tipe', 'Asset')
                            ->where('nama_akun', 'like', '%Piutang%')
                            ->first();
                    }

                    $revenueCoa = Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('tipe', 'Revenue')
                        ->where('nama_akun', 'like', '%Layanan%')
                        ->first() ?: Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('tipe', 'Revenue')
                        ->where('nama_akun', 'like', '%Tipping%')
                        ->first() ?: Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('tipe', 'Revenue')
                        ->first();

                    if (!$piutangCoa || !$revenueCoa) {
                        return;
                    }

                    $jurnalHeader = JurnalHeader::create([
                        'tenant_id' => $invoice->tenant_id,
                        'tanggal' => $invoice->tanggal_invoice->toDateString(),
                        'referensi_type' => Invoice::class,
                        'referensi_id' => $invoice->id,
                        'deskripsi' => "Piutang Invoice {$invoice->nomor_invoice} - {$invoice->klien->nama_klien}",
                    ]);

                    // Debit: Piutang
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $piutangCoa->id,
                        'debit' => $invoice->total_tagihan,
                        'kredit' => 0,
                        'contactable_type' => \App\Models\Klien::class,
                        'contactable_id' => $invoice->klien_id,
                    ]);

                    // Credit: Revenue
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $revenueCoa->id,
                        'debit' => 0,
                        'kredit' => $invoice->total_tagihan,
                    ]);
                } else {
                    // Update existing journal if amount changed
                    // Loop through details to trigger observers (BukuPembantu update)
                    foreach ($jurnalHeader->jurnalDetails as $detail) {
                        if ($detail->debit > 0 && (float)$detail->debit != (float)$invoice->total_tagihan) {
                            $detail->update(['debit' => $invoice->total_tagihan]);
                        } elseif ($detail->kredit > 0 && (float)$detail->kredit != (float)$invoice->total_tagihan) {
                            $detail->update(['kredit' => $invoice->total_tagihan]);
                        }
                    }
                }

                // Sync manual status transitions to ledger
                $bp = \App\Models\BukuPembantu::where('jurnal_header_id', $jurnalHeader->id)->first();
                if ($bp) {
                    if ($invoice->status === 'Paid' && $bp->status !== 'lunas') {
                        $bp->update([
                            'status' => 'lunas',
                            'terbayar' => $invoice->total_tagihan
                        ]);
                    } elseif ($invoice->status === 'Sent' && $bp->status === 'lunas' && is_null($bp->settled_by_jurnal_header_id)) {
                        // Revert to pending ONLY if it was manually settled (no real payment journal linked)
                        $bp->update([
                            'status' => 'pending',
                            'terbayar' => 0
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('Failed to create/update journal for invoice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        JurnalHeader::where('referensi_type', Invoice::class)
            ->where('referensi_id', $invoice->id)
            ->get()->each->delete();
    }
}
