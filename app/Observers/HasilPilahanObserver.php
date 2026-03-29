<?php

namespace App\Observers;

use App\Models\EmployeeOutput;
use App\Models\HasilPilahan;
use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Log;

class HasilPilahanObserver
{
    /**
     * Handle the HasilPilahan "saved" event (covers both created & updated).
     *
     * Strategy: SUM – one EmployeeOutput per (tenant, user, waste_category, date).
     * Every time a HasilPilahan is saved we recalculate the aggregate.
     */
    public function saved(HasilPilahan $hasilPilahan): void
    {
        $this->syncEmployeeOutput($hasilPilahan);
    }

    /**
     * Handle the HasilPilahan "deleted" event.
     */
    public function deleted(HasilPilahan $hasilPilahan): void
    {
        $this->syncEmployeeOutput($hasilPilahan);
    }

    /**
     * Recalculate the aggregated EmployeeOutput for the given
     * (tenant_id, user_id, waste_category_id, tanggal) combination.
     */
    private function syncEmployeeOutput(HasilPilahan $hasilPilahan): void
    {
        // Guard: only sync if both FKs are present
        if (!$hasilPilahan->user_id || !$hasilPilahan->waste_category_id) {
            return;
        }

        try {
            $tenantId        = $hasilPilahan->tenant_id;
            $userId          = $hasilPilahan->user_id;
            $wasteCategoryId = $hasilPilahan->waste_category_id;
            $tanggal         = $hasilPilahan->tanggal;

            // SUM all HasilPilahan for the same (tenant, user, waste_category, date)
            $totalTonase = HasilPilahan::withoutGlobalScope(TenantScope::class)
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->where('waste_category_id', $wasteCategoryId)
                ->whereDate('tanggal', $tanggal)
                ->sum('tonase');

            if ($totalTonase > 0) {
                // Create or update the aggregated Employee Output record
                EmployeeOutput::updateOrCreate(
                    [
                        'tenant_id'         => $tenantId,
                        'user_id'           => $userId,
                        'waste_category_id' => $wasteCategoryId,
                        'output_date'       => $tanggal,
                    ],
                    [
                        'quantity' => $totalTonase,
                        'unit'     => 'kg',
                        'notes'    => 'Auto-sync dari Hasil Pilahan',
                    ]
                );
            } else {
                // No more HasilPilahan for this combo — delete the EmployeeOutput
                EmployeeOutput::where('tenant_id', $tenantId)
                    ->where('user_id', $userId)
                    ->where('waste_category_id', $wasteCategoryId)
                    ->whereDate('output_date', $tanggal)
                    ->delete();
            }
        } catch (\Exception $e) {
            Log::error('HasilPilahanObserver sync failed', [
                'hasil_pilahan_id' => $hasilPilahan->id,
                'error'            => $e->getMessage(),
            ]);
        }
    }
}
