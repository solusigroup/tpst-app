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
                    $jurnalHeader = JurnalHeader::create([
                        'tenant_id' => $invoice->tenant_id,
                        'tanggal' => $invoice->tanggal_invoice->toDateString(),
                        'referensi_type' => Invoice::class,
                        'referensi_id' => $invoice->id,
                        'deskripsi' => "Piutang Invoice {$invoice->nomor_invoice} - {$invoice->klien->nama_klien}",
                    ]);
                }

                // --- Account lookup ---
                
                // 1. Dynamic Piutang Selection
                $piutangCode = '1103'; // Default to DLH
                if ($invoice->klien->jenis === 'Swasta' || $invoice->klien->jenis === 'Offtaker') {
                    $piutangCode = '1104';
                }

                $piutangCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('kode_akun', 'like', $piutangCode . '%')
                    ->first() ?: Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('nama_akun', 'like', '%Piutang%')
                    ->first();

                // 2. Revenue lookup
                // 2a. Tipping/Service Revenue Coa
                $tippingCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Revenue')
                    ->where(function($q) {
                        $q->where('nama_akun', 'like', '%Layanan%')
                          ->orWhere('nama_akun', 'like', '%Tipping%');
                    })
                    ->first();

                // 2b. Product Sale Revenue Coa
                $penjualanCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Revenue')
                    ->where(function($q) {
                        $q->where('nama_akun', 'like', '%Penjualan%')
                          ->orWhere('nama_akun', 'like', '%Material%');
                    })
                    ->first();

                // Fallback for revenue if specific ones not found
                $defaultRevenueCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Revenue')
                    ->orderBy('kode_akun')
                    ->first();

                // 3. Payment Account lookup (User choice or default Bank)
                $paymentCoa = null;
                if ($invoice->coa_pembayaran_id) {
                    $paymentCoa = Coa::find($invoice->coa_pembayaran_id);
                }

                if (!$paymentCoa) {
                    $paymentCoa = Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('tipe', 'Asset')
                        ->where('kode_akun', '1102') // Bank
                        ->first() ?: Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('tipe', 'Asset')
                        ->where('nama_akun', 'like', '%Bank%')
                        ->first();
                }

                if (!$piutangCoa || (!$tippingCoa && !$penjualanCoa && !$defaultRevenueCoa)) {
                    return;
                }

                // --- Recreate details ---
                $jurnalHeader->jurnalDetails()->get()->each->delete();

                $uangMuka = (float) ($invoice->uang_muka ?? 0);
                $totalTagihan = (float) $invoice->total_tagihan;
                $totalTipping = (float) $invoice->ritase()->sum('biaya_tipping');
                $totalPenjualan = (float) $invoice->penjualan()->sum('total_harga');

                if ($invoice->status === 'Paid') {
                    // IF PAID: Entire amount goes to Payment Account (Kas/Bank)
                    if ($totalTagihan > 0 && $paymentCoa) {
                        $jurnalHeader->jurnalDetails()->create([
                            'coa_id' => $paymentCoa->id,
                            'debit' => $totalTagihan,
                            'kredit' => 0,
                            'contactable_type' => \App\Models\Klien::class,
                            'contactable_id' => $invoice->klien_id,
                        ]);
                    }
                } else {
                    // IF SENT: Split between Piutang and Uang Muka
                    $netPiutang = $totalTagihan - $uangMuka;

                    // Debit: Piutang (Net)
                    if ($netPiutang > 0) {
                        $jurnalHeader->jurnalDetails()->create([
                            'coa_id' => $piutangCoa->id,
                            'debit' => $netPiutang,
                            'kredit' => 0,
                            'contactable_type' => \App\Models\Klien::class,
                            'contactable_id' => $invoice->klien_id,
                        ]);
                    }

                    // Debit: Payment Account (DP)
                    if ($uangMuka > 0 && $paymentCoa) {
                        $jurnalHeader->jurnalDetails()->create([
                            'coa_id' => $paymentCoa->id,
                            'debit' => $uangMuka,
                            'kredit' => 0,
                            'contactable_type' => \App\Models\Klien::class,
                            'contactable_id' => $invoice->klien_id,
                        ]);
                    }
                }

                // Credit: Revenue (Split by Source)
                
                // 1. Tipping Revenue
                if ($totalTipping > 0) {
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $tippingCoa ? $tippingCoa->id : ($defaultRevenueCoa ? $defaultRevenueCoa->id : null),
                        'debit' => 0,
                        'kredit' => $totalTipping,
                    ]);
                }

                // 2. Sales Revenue
                if ($totalPenjualan > 0) {
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $penjualanCoa ? $penjualanCoa->id : ($defaultRevenueCoa ? $defaultRevenueCoa->id : null),
                        'debit' => 0,
                        'kredit' => $totalPenjualan,
                    ]);
                }

                // 3. Any remaining difference (if rounding or manual adjustments)
                $capturedRevenue = $totalTipping + $totalPenjualan;
                $difference = $totalTagihan - $capturedRevenue;
                if (abs($difference) > 0.01) {
                     $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $defaultRevenueCoa ? $defaultRevenueCoa->id : null,
                        'debit' => 0,
                        'kredit' => $difference,
                    ]);
                }

                // Sync manual status transitions to ledger
                $bp = \App\Models\BukuPembantu::where('jurnal_header_id', $jurnalHeader->id)->first();
                if ($bp) {
                    if ($invoice->status === 'Paid') {
                        $bp->update([
                            'status' => 'lunas',
                            'terbayar' => $totalTagihan
                        ]);
                    } else {
                        $bp->update([
                            'status' => 'pending',
                            'terbayar' => $uangMuka
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
