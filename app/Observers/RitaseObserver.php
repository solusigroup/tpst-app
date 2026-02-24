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
        // Only create journal if biaya_tipping > 0
        if ($ritase->biaya_tipping <= 0) {
            return;
        }

        try {
            DB::transaction(function () use ($ritase) {
                // Get the chart of accounts
                // Debit Account: Piutang Usaha (Receivable) - typically 1200
                $debitCoa = Coa::where('tenant_id', $ritase->tenant_id)
                    ->where('tipe', 'Asset')
                    ->where('nama_akun', 'like', '%Piutang%')
                    ->first();

                // If no Piutang account, try Kas
                if (!$debitCoa) {
                    $debitCoa = Coa::where('tenant_id', $ritase->tenant_id)
                        ->where('tipe', 'Asset')
                        ->where('nama_akun', 'like', '%Kas%')
                        ->first();
                }

                // Credit Account: Pendapatan Tipping - typically 4100
                $creditCoa = Coa::where('tenant_id', $ritase->tenant_id)
                    ->where('tipe', 'Revenue')
                    ->where('nama_akun', 'like', '%Tipping%')
                    ->first();

                // Only proceed if both accounts exist
                if (!$debitCoa || !$creditCoa) {
                    return;
                }

                // Create journal header
                $jurnalHeader = JurnalHeader::create([
                    'tenant_id' => $ritase->tenant_id,
                    'tanggal' => $ritase->waktu_masuk->toDateString(),
                    'nomor_referensi' => 'TIP-' . $ritase->nomor_tiket,
                    'deskripsi' => "Biaya Tipping untuk ritase {$ritase->nomor_tiket} dari {$ritase->klien->nama_klien}",
                ]);

                // Create debit entry (Piutang/Kas)
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $debitCoa->id,
                    'debit' => $ritase->biaya_tipping,
                    'kredit' => 0,
                ]);

                // Create credit entry (Pendapatan Tipping)
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $creditCoa->id,
                    'debit' => 0,
                    'kredit' => $ritase->biaya_tipping,
                ]);
            });
        } catch (\Exception $e) {
            // Log the error but don't fail the ritase creation
            \Log::error('Failed to create journal entry for ritase', [
                'ritase_id' => $ritase->id,
                'error' => $e->getMessage(),
            ]);
        }
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
        // Optional: Handle deletion of related journal entries
    }
}
