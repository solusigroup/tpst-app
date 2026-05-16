<?php

use App\Models\WageCalculation;
use App\Models\EmployeeOutput;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    echo "Starting reset process...\n";

    // 1. Delete all wage calculations
    // We use truncate if possible, or delete for safety with multi-tenancy scopes
    $deletedCalculations = WageCalculation::withoutGlobalScopes()->delete();
    echo "- Deleted $deletedCalculations wage calculation records.\n";

    // 2. Reset paid_quantity in employee_outputs
    $updatedOutputs = EmployeeOutput::withoutGlobalScopes()->update(['paid_quantity' => 0]);
    echo "- Reset paid_quantity for $updatedOutputs production records.\n";

    DB::commit();
    echo "\nSUCCESS: All wage data has been reset. You can now start fresh calculations.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "\nERROR: Reset failed! " . $e->getMessage() . "\n";
}
