<?php

use App\Models\User;
use App\Models\EmployeeOutput;
use App\Models\Penjualan;
use App\Models\WageCalculation;
use Carbon\Carbon;

$user = User::withoutGlobalScopes()->where('name', 'like', '%samsul%')->first();
if (!$user) {
    die("User Samsul not found.\n");
}

echo "Found User: " . $user->name . " (ID: " . $user->id . ")\n";

$targetDate = '2026-04-30';

// 1. Check production on 30 April
$outputs = EmployeeOutput::withoutGlobalScopes()
    ->where('user_id', $user->id)
    ->whereDate('output_date', $targetDate)
    ->get();

if ($outputs->isEmpty()) {
    echo "No production records found for $user->name on $targetDate.\n";
    
    // Check surrounding dates
    $any = EmployeeOutput::withoutGlobalScopes()
        ->where('user_id', $user->id)
        ->orderBy('output_date', 'desc')
        ->first();
    if ($any) {
        echo "Latest production found on: " . $any->output_date->toDateString() . "\n";
    }
}

foreach ($outputs as $out) {
    echo "- " . ($out->wasteCategory->name ?? 'Unknown') . ": " . $out->quantity . " kg (Paid: " . $out->paid_quantity . ")\n";
}

// 2. Check Sales for those categories
foreach ($outputs as $out) {
    $totalSold = Penjualan::withoutGlobalScopes()
        ->where('waste_category_id', $out->waste_category_id)
        ->where('tenant_id', $user->tenant_id)
        ->whereDate('tanggal', '<=', $targetDate)
        ->sum('berat_kg');
    
    $totalPaid = EmployeeOutput::withoutGlobalScopes()
        ->where('waste_category_id', $out->waste_category_id)
        ->where('tenant_id', $user->tenant_id)
        ->sum('paid_quantity');

    echo "\nCategory Details for: " . ($out->wasteCategory->name ?? 'Unknown') . "\n";
    echo "  Global Sold (up to $targetDate): $totalSold kg\n";
    echo "  Global Paid (previously): $totalPaid kg\n";
    echo "  Available to Pay: " . max(0, $totalSold - $totalPaid) . " kg\n";
}

// 3. Trigger Calculation for the week containing 30 April
// Sunday, 2026-04-26 to Saturday, 2026-05-02
$weekStart = Carbon::parse('2026-04-26'); 
echo "\nRunning calculation for period starting: " . $weekStart->toDateString() . "\n";

$calc = WageCalculation::calculateForEmployee($user->id, $weekStart, $user->tenant_id);

echo "\nFINAL CALCULATION RESULT:\n";
echo "Total Wage: Rp " . number_format($calc->total_wage, 0, ',', '.') . "\n";
echo "Total Quantity: " . number_format($calc->total_quantity, 2) . " kg\n";

if (!empty($calc->details)) {
    echo "\nBreakdown Details:\n";
    foreach ($calc->details as $detail) {
        echo "- " . $detail['date'] . " | " . $detail['category'] . " | " . $detail['quantity_paid'] . " kg @ Rp " . number_format($detail['rate'], 0) . " = Rp " . number_format($detail['subtotal'], 0) . "\n";
    }
} else {
    echo "\nNo items were payable (possibly zero sales for these items yet).\n";
}
