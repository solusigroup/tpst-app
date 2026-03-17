<?php

namespace App\Services;

use App\Models\EmployeeOutput;
use App\Models\WageCalculation;
use Carbon\Carbon;

class WageCalculationService
{
    public function calculateWeeklyWages(int $tenantId, Carbon $weekStart, ?int $userId = null): int
    {
        $query = EmployeeOutput::where('tenant_id', $tenantId);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $weekEnd = $weekStart->copy()->addDays(6);
        
        $outputs = $query->whereBetween('output_date', [
            $weekStart->format('Y-m-d'),
            $weekEnd->format('Y-m-d')
        ])->get();

        $userIds = $outputs->pluck('user_id')->unique();
        $count = 0;

        foreach ($userIds as $uid) {
            WageCalculation::calculateForEmployee($uid, $weekStart, $tenantId);
            $count++;
        }

        return $count;
    }

    public function getEmployeeWeeklyWage(int $userId, Carbon $weekStart, int $tenantId): WageCalculation
    {
        return WageCalculation::calculateForEmployee($userId, $weekStart, $tenantId);
    }

    public function approveWages(array $wageIds): int
    {
        return WageCalculation::whereIn('id', $wageIds)
            ->update(['status' => 'approved']);
    }

    public function payWages(array $wageIds, Carbon $paidDate): int
    {
        return WageCalculation::whereIn('id', $wageIds)
            ->update([
                'status' => 'paid',
                'paid_date' => $paidDate,
            ]);
    }

    public function getWageSummary(int $tenantId, Carbon $startDate, Carbon $endDate)
    {
        return WageCalculation::where('tenant_id', $tenantId)
            ->whereBetween('week_start', [$startDate, $endDate])
            ->with('user')
            ->get()
            ->groupBy('status');
    }
}
