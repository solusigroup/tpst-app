<?php

namespace App\Observers;

use App\Models\Coa;
use App\Models\Invoice;
use App\Models\JurnalHeader;
use Illuminate\Support\Facades\DB;

class InvoiceObserver
{
    /**
     * Reentrancy guard — prevents infinite loops when JurnalDetailObserver
     * settles BukuPembantu and triggers Invoice::update(['status' => 'Paid']).
     */
    private static bool $processing = false;

    /**
     * Handle the Invoice "saved" event.
     *
     * Journal rules:
     *
     * STATUS SENT (Revenue Recognition):
     *   Swasta  (Ritase)    → Dr 1114 Piutang Jasa Swasta  / Cr 4103 Pendapatan Retribusi Swasta
     *   DLH     (Ritase)    → Dr 1113 Piutang Usaha DLH    / Cr 4101 Pendapatan Jasa Pengelolaan
     *   Offtaker(Penjualan) → Dr 1115 Piutang Penjualan    / Cr 4102 Pendapatan Penjualan Material
     *
     * STATUS PAID (Payment Receipt — second journal):
     *   All types → Dr Bank Jatim / Cr Piutang relevan
     */
    public function saved(Invoice $invoice): void
    {
        if (self::$processing) {
            return;
        }

        // 1. If status is Draft or Canceled, delete any existing journals
        if (in_array($invoice->status, ['Draft', 'Canceled'])) {
            self::$processing = true;
            try {
                JurnalHeader::where('referensi_type', Invoice::class)
                    ->where('referensi_id', $invoice->id)
                    ->get()->each->delete();

                if (!empty($invoice->nomor_invoice)) {
                    JurnalHeader::where('deskripsi', 'like', '%' . $invoice->nomor_invoice . '%')
                        ->get()->each->delete();
                }
            } finally {
                self::$processing = false;
            }
            return;
        }

        // 2. Only generate journal if status is Sent or Paid
        if (!in_array($invoice->status, ['Sent', 'Paid'])) {
            return;
        }

        self::$processing = true;

        try {
            DB::transaction(function () use ($invoice) {
                $klienJenis = $invoice->klien->jenis ?? 'DLH';

                // ========================================
                // ACCOUNT LOOKUPS
                // ========================================

                // --- Piutang COA (by client type) ---
                $piutangCategory = match ($klienJenis) {
                    'Swasta'   => 'piutang_swasta',
                    'Offtaker' => 'piutang_offtaker',
                    default    => 'piutang_dlh',
                };

                $piutangCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('kategori_buku_pembantu', $piutangCategory)
                    ->first();

                // Fallback: lookup by kode_akun
                if (!$piutangCoa) {
                    $piutangCode = match ($klienJenis) {
                        'Swasta'   => '1114',
                        'Offtaker' => '1115',
                        default    => '1113',
                    };
                    $piutangCoa = Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('kode_akun', $piutangCode)
                        ->first();
                }

                // --- Revenue COA for Ritase/Tipping (by client type) ---
                $revenueTippingCode = match ($klienJenis) {
                    'Swasta' => '4103', // Pendapatan Retribusi Swasta/Komersial
                    default  => '4101', // Pendapatan Jasa Pengelolaan (DLH)
                };
                $tippingRevenueCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('kode_akun', $revenueTippingCode)
                    ->first();

                // --- Revenue COA for Penjualan (always 4102) ---
                $penjualanRevenueCoa = Coa::where('tenant_id', $invoice->tenant_id)
                    ->where('kode_akun', '4102') // Pendapatan Penjualan Material Daur Ulang
                    ->first();

                // --- Bank Jatim COA (payment account) ---
                $bankCoa = null;
                if ($invoice->coa_pembayaran_id) {
                    $bankCoa = Coa::find($invoice->coa_pembayaran_id);
                }
                if (!$bankCoa) {
                    $bankCoa = Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('nama_akun', 'like', '%Bank Jatim%')
                        ->first();
                }
                if (!$bankCoa) {
                    // Ultimate fallback: any Bank account
                    $bankCoa = Coa::where('tenant_id', $invoice->tenant_id)
                        ->where('tipe', 'Asset')
                        ->where('nama_akun', 'like', '%Bank%')
                        ->first();
                }

                // Bail if essential accounts are missing
                if (!$piutangCoa) {
                    \Log::warning('InvoiceObserver: Piutang COA not found', [
                        'invoice_id' => $invoice->id,
                        'klien_jenis' => $klienJenis,
                        'kategori' => $piutangCategory,
                    ]);
                    return;
                }

                // ========================================
                // AMOUNTS
                // ========================================
                $totalTagihan   = (float) $invoice->total_tagihan;
                $uangMuka       = (float) ($invoice->uang_muka ?? 0);
                $netPiutang     = $totalTagihan - $uangMuka;
                $totalTipping   = (float) $invoice->ritase()->sum('biaya_tipping');
                $totalPenjualan = (float) $invoice->penjualan()->sum('total_harga');

                // ========================================
                // JOURNAL 1: Revenue Recognition (Piutang ↔ Pendapatan)
                // ========================================
                $revenueJournals = JurnalHeader::where('tenant_id', $invoice->tenant_id)
                    ->where('referensi_type', Invoice::class)
                    ->where('referensi_id', $invoice->id)
                    ->where('deskripsi', 'not like', '%Penerimaan Pembayaran%')
                    ->get();

                $revenueJurnal = $revenueJournals->first();
                if ($revenueJournals->count() > 1) {
                    $revenueJournals->slice(1)->each->delete();
                }

                if (!$revenueJurnal) {
                    $revenueJurnal = JurnalHeader::create([
                        'tenant_id'      => $invoice->tenant_id,
                        'tanggal'        => $invoice->tanggal_invoice->toDateString(),
                        'referensi_type' => Invoice::class,
                        'referensi_id'   => $invoice->id,
                        'deskripsi'      => "Piutang Invoice {$invoice->nomor_invoice} - {$invoice->klien->nama_klien}",
                    ]);
                } else {
                    $revenueJurnal->update([
                        'tanggal'   => $invoice->tanggal_invoice->toDateString(),
                        'deskripsi' => "Piutang Invoice {$invoice->nomor_invoice} - {$invoice->klien->nama_klien}",
                    ]);
                }

                // Always refresh details for Journal 1 to ensure accurate accounts
                $revenueJurnal->jurnalDetails()->get()->each->delete();

                // DEBIT: Piutang (net after DP)
                if ($netPiutang > 0) {
                    $revenueJurnal->jurnalDetails()->create([
                        'coa_id'           => $piutangCoa->id,
                        'debit'            => $netPiutang,
                        'kredit'           => 0,
                        'contactable_type' => \App\Models\Klien::class,
                        'contactable_id'   => $invoice->klien_id,
                    ]);
                }

                // DEBIT: Bank/Kas for Down Payment (if any)
                if ($uangMuka > 0 && $bankCoa) {
                    $revenueJurnal->jurnalDetails()->create([
                        'coa_id'           => $bankCoa->id,
                        'debit'            => $uangMuka,
                        'kredit'           => 0,
                        'contactable_type' => \App\Models\Klien::class,
                        'contactable_id'   => $invoice->klien_id,
                    ]);
                }

                // CREDIT: Revenue — split by source with type-specific accounts
                // Ritase → Tipping Revenue (4101 DLH / 4103 Swasta)
                if ($totalTipping > 0 && $tippingRevenueCoa) {
                    $revenueJurnal->jurnalDetails()->create([
                        'coa_id' => $tippingRevenueCoa->id,
                        'debit'  => 0,
                        'kredit' => $totalTipping,
                    ]);
                }

                // Penjualan → 4102 Pendapatan Penjualan Material
                if ($totalPenjualan > 0 && $penjualanRevenueCoa) {
                    $revenueJurnal->jurnalDetails()->create([
                        'coa_id' => $penjualanRevenueCoa->id,
                        'debit'  => 0,
                        'kredit' => $totalPenjualan,
                    ]);
                }

                // Remaining difference (fee bulanan, rounding, manual adjustments)
                $capturedRevenue = $totalTipping + $totalPenjualan;
                $difference = $totalTagihan - $capturedRevenue;
                if (abs($difference) > 0.01) {
                    $fallbackRevenue = $tippingRevenueCoa ?? $penjualanRevenueCoa;
                    if ($fallbackRevenue) {
                        $revenueJurnal->jurnalDetails()->create([
                            'coa_id' => $fallbackRevenue->id,
                            'debit'  => 0,
                            'kredit' => $difference,
                        ]);
                    }
                }

                // ========================================
                // JOURNAL 2: Payment Receipt (Bank ↔ Piutang) — PAID only
                // ========================================
                $paymentJournals = JurnalHeader::where('tenant_id', $invoice->tenant_id)
                    ->where('referensi_type', Invoice::class)
                    ->where('referensi_id', $invoice->id)
                    ->where('deskripsi', 'like', '%Penerimaan Pembayaran%')
                    ->get();

                if ($invoice->status === 'Paid') {
                    $paymentJurnal = $paymentJournals->first();
                    if ($paymentJournals->count() > 1) {
                        $paymentJournals->slice(1)->each->delete();
                    }

                    if (!$paymentJurnal) {
                        $paymentJurnal = JurnalHeader::create([
                            'tenant_id'      => $invoice->tenant_id,
                            'tanggal'        => now()->toDateString(),
                            'referensi_type' => Invoice::class,
                            'referensi_id'   => $invoice->id,
                            'deskripsi'      => "Penerimaan Pembayaran Invoice {$invoice->nomor_invoice} - {$invoice->klien->nama_klien}",
                        ]);
                    }

                    // Always refresh details for Journal 2
                    $paymentJurnal->jurnalDetails()->get()->each->delete();

                    if ($bankCoa && $netPiutang > 0) {
                        // DEBIT: Bank Jatim (amount = net piutang, after DP)
                        $paymentJurnal->jurnalDetails()->create([
                            'coa_id'           => $bankCoa->id,
                            'debit'            => $netPiutang,
                            'kredit'           => 0,
                            'contactable_type' => \App\Models\Klien::class,
                            'contactable_id'   => $invoice->klien_id,
                        ]);

                        // CREDIT: Piutang relevan (triggers JurnalDetailObserver settlement)
                        $paymentJurnal->jurnalDetails()->create([
                            'coa_id'           => $piutangCoa->id,
                            'debit'            => 0,
                            'kredit'           => $netPiutang,
                            'contactable_type' => \App\Models\Klien::class,
                            'contactable_id'   => $invoice->klien_id,
                        ]);
                    }
                } else {
                    // Status is 'Sent' — delete payment journals if exist (Paid→Sent reversal)
                    $paymentJournals->each->delete();
                }

                // ========================================
                // MANUAL BUKU PEMBANTU SYNC (safety net)
                // ========================================
                $bp = \App\Models\BukuPembantu::where('jurnal_header_id', $revenueJurnal->id)->first();
                if ($bp) {
                    if ($invoice->status === 'Paid') {
                        $bp->update([
                            'status'   => 'lunas',
                            'terbayar' => $netPiutang,
                            'settled_by_jurnal_header_id' => isset($paymentJurnal) ? $paymentJurnal->id : $bp->settled_by_jurnal_header_id,
                        ]);
                    } else {
                        $bp->update([
                            'status'   => 'pending',
                            'terbayar' => $uangMuka,
                            'settled_by_jurnal_header_id' => null,
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('Failed to create/update journal for invoice', [
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);
        } finally {
            self::$processing = false;
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
