<?php

namespace App\Observers;

use App\Models\WageCalculation;
use App\Models\EmployeeOutput;

class WageCalculationObserver
{
    /**
     * Handle the WageCalculation "deleted" event.
     */
    public function deleted(WageCalculation $wageCalculation): void
    {
        if ($wageCalculation->details) {
            foreach ($wageCalculation->details as $item) {
                if (isset($item['employee_output_id'])) {
                    $output = EmployeeOutput::find($item['employee_output_id']);
                    if ($output) {
                        $output->decrement('paid_quantity', $item['quantity_paid']);
                    }
                }
            }
        }
    }
}
