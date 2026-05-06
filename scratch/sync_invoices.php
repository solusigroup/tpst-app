<?php

use App\Models\Invoice;
use App\Observers\InvoiceObserver;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Mass Synchronization for Invoices...\n";

$invoices = Invoice::whereNotIn('status', ['Draft', 'Canceled'])->get();
$count = $invoices->count();
echo "Found $count invoices to sync.\n";

$observer = new InvoiceObserver();

foreach ($invoices as $index => $invoice) {
    try {
        echo "[" . ($index + 1) . "/$count] Syncing Invoice: {$invoice->nomor_invoice}... ";
        
        // Trigger the saved logic to recreate journals
        $observer->saved($invoice);
        
        echo "OK\n";
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}

echo "Synchronization completed.\n";
