<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $user = App\Models\User::first();
    auth()->login($user);
    $req = request();
    $req->merge(['week_start' => date('Y-m-d')]);
    app('App\Http\Controllers\Admin\WageCalculationController')->calculate($req);
    echo "SUCCESS\n";
} catch (\Throwable $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
