<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orphans = \App\Models\BukuPembantu::whereDoesntHave('jurnalHeader')->get();
$count = 0;
foreach ($orphans as $orphan) {
    $orphan->delete();
    $count++;
}

echo "Cleaned up $count orphaned BukuPembantu records.\n";
