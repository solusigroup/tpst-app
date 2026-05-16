<?php

use App\Models\User;
use App\Models\WageCalculation;
use Carbon\Carbon;

// Target cutoff
$cutoffDate = Carbon::parse('2026-05-11');
// In our logic, week_end = week_start + 6 days. 
// So to get May 11 as week_end, we use May 5 as week_start.
$weekStart = Carbon::parse('2026-05-05');

echo "Starting calculation for Cutoff: " . $cutoffDate->toDateString() . " (Period: " . $weekStart->toDateString() . " to " . $cutoffDate->toDateString() . ")\n";

$employees = User::role('karyawan')->get();
$count = 0;

foreach ($employees as $employee) {
    try {
        // We bypass global scopes if necessary, but calculateForEmployee handles tenant_id
        WageCalculation::calculateForEmployee($employee->id, $weekStart, $employee->tenant_id);
        echo "- Calculated for: " . $employee->name . "\n";
        $count++;
    } catch (\Exception $e) {
        echo "- ERROR for " . $employee->name . ": " . $e->getMessage() . "\n";
    }
}

echo "\nCOMPLETED: Calculated wages for $count employees up to May 11, 2026.\n";
