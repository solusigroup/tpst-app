<?php

use App\Models\User;
use App\Models\EmployeeOutput;
use App\Models\WageCalculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$user = User::withoutGlobalScopes()->where('name', 'like', '%samsul%')->first();
if (!$user) die("Samsul not found\n");

echo "Adjusting data for: " . $user->name . " (ID: $user->id)\n";

// 1. Mark as PAID: everything before 30 April
$paid1 = EmployeeOutput::withoutGlobalScopes()
    ->where('user_id', $user->id)
    ->where('output_date', '<', '2026-04-30')
    ->update(['paid_quantity' => DB::raw('quantity')]);
echo "- Marked $paid1 records before April 30 as PAID.\n";

// 2. Mark as PAID: everything between 1 May and 10 May
$paid2 = EmployeeOutput::withoutGlobalScopes()
    ->where('user_id', $user->id)
    ->whereBetween('output_date', ['2026-05-01', '2026-05-10'])
    ->update(['paid_quantity' => DB::raw('quantity')]);
echo "- Marked $paid2 records between May 1 and May 10 as PAID.\n";

// 3. Mark as UNPAID: 30 April and 11-16 May
$unpaid = EmployeeOutput::withoutGlobalScopes()
    ->where('user_id', $user->id)
    ->where(function($q) {
        $q->whereDate('output_date', '2026-04-30')
          ->orWhere('output_date', '>=', '2026-05-11');
    })
    ->update(['paid_quantity' => 0]);
echo "- Reset $unpaid records (April 30 and May 11+) to UNPAID.\n";

echo "Data adjusted. Now calculating fresh wage for current week...\n";

// 4. Run calculation for current week (ending May 16)
$weekStart = Carbon::parse('2026-05-10');
$calc = WageCalculation::calculateForEmployee($user->id, $weekStart, $user->tenant_id);

echo "\nFINAL CALCULATION RESULT FOR SAMSUL WISNU:\n";
echo "Period: " . $calc->week_start->toDateString() . " - " . $calc->week_end->toDateString() . "\n";
echo "Total Wage: Rp " . number_format($calc->total_wage, 0, ',', '.') . "\n";
echo "Total Quantity: " . number_format($calc->total_quantity, 2) . " kg\n";

if (!empty($calc->details)) {
    echo "\nPaid Items Breakdown:\n";
    foreach ($calc->details as $detail) {
        echo "- " . $detail['date'] . " | " . $detail['category'] . " | " . $detail['quantity_paid'] . " kg | Rp " . number_format($detail['subtotal'], 0) . "\n";
    }
} else {
    echo "\nNo items were payable yet (awaiting sales coverage).\n";
}
