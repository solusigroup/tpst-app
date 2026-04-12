<?php

namespace App\Observers;

use App\Models\Coa;
use App\Models\JurnalHeader;
use App\Models\PengangkutanResidu;
use Illuminate\Support\Facades\DB;

class PengangkutanResiduObserver
{
    /**
     * Handle the PengangkutanResidu "saved" event.
     */
    public function saved(PengangkutanResidu $residu): void
    {
        try {
            DB::transaction(function () use ($residu) {
                $jurnalHeader = $residu->jurnalHeader;

                if (!$jurnalHeader) {
                    // Account lookup
                    $bebanTpaCoa = Coa::where('tenant_id', $residu->tenant_id)
                        ->where('kode_akun', '5103')
                        ->first();

                    $utangCoa = Coa::where('tenant_id', $residu->tenant_id)
                        ->where('kode_akun', '2101') // Utang Usaha
                        ->first();

                    if (!$bebanTpaCoa || !$utangCoa) {
                        return;
                    }

                    $jurnalHeader = JurnalHeader::create([
                        'tenant_id' => $residu->tenant_id,
                        'tanggal' => $residu->tanggal->toDateString(),
                        'referensi_type' => PengangkutanResidu::class,
                        'referensi_id' => $residu->id,
                        'deskripsi' => "Biaya Tipping TPA Residu {$residu->nomor_tiket} - {$residu->armada->plat_nomor}",
                    ]);

                    // Link to residu
                    $residu->updateQuietly(['jurnal_header_id' => $jurnalHeader->id]);
                }

                // Refresh accounts in case they changed or to be safe
                $bebanTpaCoa = Coa::where('tenant_id', $residu->tenant_id)->where('kode_akun', '5103')->first();
                $utangCoa = Coa::where('tenant_id', $residu->tenant_id)->where('kode_akun', '2101')->first();

                if (!$bebanTpaCoa || !$utangCoa) return;

                // Delete and recreate details for multi-line support / simplicity
                $jurnalHeader->jurnalDetails->each->delete();

                $amount = (float) ($residu->biaya_retribusi ?? 0);

                if ($amount > 0) {
                    // Debit: Beban Tipping Fee
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $bebanTpaCoa->id,
                        'debit' => $amount,
                        'kredit' => 0,
                    ]);

                    // Credit: Utang Usaha
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $utangCoa->id,
                        'debit' => 0,
                        'kredit' => $amount,
                        'contactable_type' => \App\Models\Armada::class, // Optional: linking to armada
                        'contactable_id' => $residu->armada_id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            \Log::error('Failed to create journal for residue transport', [
                'residu_id' => $residu->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the PengangkutanResidu "deleted" event.
     */
    public function deleted(PengangkutanResidu $residu): void
    {
        if ($residu->jurnalHeader) {
            $residu->jurnalHeader->delete();
        }
    }
}
