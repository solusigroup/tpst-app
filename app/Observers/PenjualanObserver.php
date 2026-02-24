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
        try {
            DB::transaction(function () use ($penjualan) {
                // Get the chart of accounts
                // Debit Account: Piutang Usaha (Receivable) - typically 1200
                $debitCoa = Coa::where('tenant_id', $penjualan->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('nama_akun', 'like', '%Piutang%')
                    ->first();

                // If no Piutang account, try Kas
                if (!$debitCoa) {
                    $debitCoa = Coa::where('tenant_id', $penjualan->tenant_id)
                        ->where('tipe', 'Asset')
                        ->where('nama_akun', 'like', '%Kas%')
                        ->first();
                }

                // Credit Account: Pendapatan Penjualan - typically 4200
                $creditCoa = Coa::where('tenant_id', $penjualan->tenant_id)
                    ->where('tipe', 'Revenue')
                    ->where('nama_akun', 'like', '%Penjualan%')
                    ->first();

                // Only proceed if both accounts exist
                if (!$debitCoa || !$creditCoa) {
                    return;
                }

                // Create journal header
                $jurnalHeader = JurnalHeader::create([
                    'tenant_id' => $penjualan->tenant_id,
                    'tanggal' => $penjualan->tanggal,
                    'nomor_referensi' => 'SAL-' . $penjualan->id,
                    'deskripsi' => "Penjualan {$penjualan->jenis_produk} seberat {$penjualan->berat_kg}kg kepada {$penjualan->klien->nama_klien}",
                ]);

                // Create debit entry (Piutang/Kas)
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $debitCoa->id,
                    'debit' => $penjualan->total_harga,
                    'kredit' => 0,
                ]);

                // Create credit entry (Pendapatan Penjualan)
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $creditCoa->id,
                    'debit' => 0,
                    'kredit' => $penjualan->total_harga,
                ]);
            });
        } catch (\Exception $e) {
            // Log the error but don't fail the penjualan creation
            \Log::error('Failed to create journal entry for penjualan', [
                'penjualan_id' => $penjualan->id,
                'error' => $e->getMessage(),
            ]);
        }
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
        // Optional: Handle deletion of related journal entries
    }
}
