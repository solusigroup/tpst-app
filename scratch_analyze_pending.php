<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ritase;
use App\Models\Klien;

$kliens = Klien::all();
echo "Client Summary (Ritase):\n";
echo str_pad("Client Name", 30) . " | " . str_pad("Approved", 10) . " | " . str_pad("Unapproved", 10) . " | " . str_pad("Linked to Inv", 15) . "\n";
echo str_repeat("-", 75) . "\n";

foreach ($kliens as $k) {
    $approved = Ritase::where('klien_id', $k->id)->where('is_approved', 1)->whereNull('invoice_id')->count();
    $unapproved = Ritase::where('klien_id', $k->id)->where('is_approved', 0)->whereNull('invoice_id')->count();
    $linked = Ritase::where('klien_id', $k->id)->whereNotNull('invoice_id')->count();
    
    if ($approved > 0 || $unapproved > 0 || $linked > 0) {
        echo str_pad($k->nama_klien, 30) . " | " . str_pad($approved, 10) . " | " . str_pad($unapproved, 10) . " | " . str_pad($linked, 15) . "\n";
    }
}
