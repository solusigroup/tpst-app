<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ritase;

echo "--- ALL TICKETS STARTING WITH RT-202604- --- \n";
$rs = Ritase::withoutGlobalScopes()->where('nomor_tiket', 'like', 'RT-202604-%')->get();
foreach ($rs as $r) {
    echo "ID: {$r->id} | Ticket: {$r->nomor_tiket} | Status: {$r->status} | Invoice: " . ($r->invoice_id ?? 'NULL') . " | Tenant: {$r->tenant_id}\n";
}
