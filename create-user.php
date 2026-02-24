<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updated = \App\Models\User::where('email', 'admin@tpst.test')->update([
    'password' => bcrypt('password123'),
    'role' => 'admin',
]);

if ($updated) {
    echo "✅ User updated successfully!\n";
} else {
    $tenant = \App\Models\Tenant::first();
    \App\Models\User::create([
        'tenant_id' => $tenant->id,
        'name' => 'Admin User',
        'email' => 'admin@tpst.test',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);
    echo "✅ User created successfully!\n";
}

echo "Email: admin@tpst.test\n";
echo "Password: password123\n";
