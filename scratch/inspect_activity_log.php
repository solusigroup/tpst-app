<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Activitylog\Models\Activity;

$logs = Activity::where('subject_type', 'like', '%Ritase%')
    ->orderBy('created_at', 'desc')
    ->limit(100)
    ->get();

echo "Total Ritase activity logs: " . $logs->count() . "\n\n";

foreach ($logs as $log) {
    $properties = $log->properties;
    $oldInvoice = $properties['old']['invoice_id'] ?? null;
    $newInvoice = $properties['attributes']['invoice_id'] ?? null;
    
    if ($oldInvoice !== null || $newInvoice !== null) {
        echo "Log ID: {$log->id} | Time: {$log->created_at} | Event: {$log->description} | Subject ID: {$log->subject_id} | Old Invoice ID: " . var_export($oldInvoice, true) . " | New Invoice ID: " . var_export($newInvoice, true) . "\n";
    }
}
