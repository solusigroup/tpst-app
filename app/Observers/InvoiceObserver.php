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
                if (!$jurnalHeader) {
                    $jurnalHeader = JurnalHeader::create([
                        'tenant_id' => $invoice->tenant_id,
                        'tanggal' => $invoice->tanggal_invoice->toDateString(),
                        'referensi_type' => Invoice::class,
                        'referensi_id' => $invoice->id,
                        'deskripsi' => "Piutang Invoice {$invoice->nomor_invoice} - {$invoice->klien->nama_klien}",
                    ]);
                }

                // Account lookup
                $piutangCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('kode_akun', 'like', '1103%')
                    ->first() ?: Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('nama_akun', 'like', '%Piutang%')
                    ->first();

                $revenueCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Revenue')
                    ->where('nama_akun', 'like', '%Layanan%')
                    ->first() ?: Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Revenue')
                    ->where('nama_akun', 'like', '%Tipping%')
                    ->first() ?: Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Revenue')
                    ->first();

                $bankCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('kode_akun', '1102')
                    ->first() ?: Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('nama_akun', 'like', '%Bank%')
                    ->first();

                if (!$piutangCoa || !$revenueCoa) {
                    return;
                }

                // Delete existing details to recreate them (cleaner for multi-line journals)
                $jurnalHeader->jurnalDetails()->delete();

                $uangMuka = (float) ($invoice->uang_muka ?? 0);
                $totalTagihan = (float) $invoice->total_tagihan;
                $netPiutang = $totalTagihan - $uangMuka;

                // 1. Debit: Piutang (Net)
                if ($netPiutang > 0) {
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $piutangCoa->id,
                        'debit' => $netPiutang,
                        'kredit' => 0,
                        'contactable_type' => \App\Models\Klien::class,
                        'contactable_id' => $invoice->klien_id,
                    ]);
                }

                // 2. Debit: Bank (DP)
                if ($uangMuka > 0 && $bankCoa) {
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $bankCoa->id,
                        'debit' => $uangMuka,
                        'kredit' => 0,
                        'contactable_type' => \App\Models\Klien::class,
                        'contactable_id' => $invoice->klien_id,
                    ]);
                }

                // 3. Credit: Revenue (Gross)
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $revenueCoa->id,
                    'debit' => 0,
                    'kredit' => $totalTagihan,
                ]);

                // Sync manual status transitions to ledger
                $bp = \App\Models\BukuPembantu::where('jurnal_header_id', $jurnalHeader->id)->first();
                if ($bp) {
                    if ($invoice->status === 'Paid' && $bp->status !== 'lunas') {
                        $bp->update([
                            'status' => 'lunas',
                            'terbayar' => $netPiutang
                        ]);
                    } elseif ($invoice->status === 'Sent' && $bp->status === 'lunas' && is_null($bp->settled_by_jurnal_header_id)) {
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
