<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ritase;

$total = Ritase::count();
$approved = Ritase::where('is_approved', 1)->count();
$unapproved = Ritase::where('is_approved', 0)->count();

echo "Total Ritase: $total\n";
echo "Approved: $approved\n";
echo "Unapproved: $unapproved\n";

$pending_and_approved = Ritase::where('is_approved', 1)->whereNull('invoice_id')->count();
echo "Approved & Pending (Not yet in Invoice): $pending_and_approved\n";
