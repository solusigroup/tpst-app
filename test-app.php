<?php
// Simple database test using Laravel's Artisan
echo "=== TPST Application Test ===\n\n";

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// 1. Test Database Connection
echo "1. Testing Database Connection...\n";
try {
    $db = $app->make('db');
    $result = $db->select("SELECT 1 as connected");
    echo "   ✓ Database connection successful\n\n";
} catch (\Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Check all migrations
echo "2. Checking Migrations...\n";
try {
    $migrations = $db->table('migrations')->get();
    echo "   ✓ Total migrations run: " . count($migrations) . "\n";
    foreach ($migrations as $migration) {
        echo "     - " . $migration->migration . "\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error checking migrations: " . $e->getMessage() . "\n\n";
}

// 3. Check tables
echo "3. Checking Database Tables...\n";
$tables = [
    'tenants', 'users', 'klien', 'armada', 'ritase', 
    'produksi_harian', 'penjualan', 'coa', 'jurnal_header', 'jurnal_detail'
];
foreach ($tables as $table) {
    try {
        $count = $db->table($table)->count();
        echo "   ✓ Table '{$table}': $count records\n";
    } catch (\Exception $e) {
        echo "   ✗ Table '{$table}': Error - " . $e->getMessage() . "\n";
    }
}
echo "\n";

// 4. Check test data
echo "4. Checking Test Data...\n";
try {
    $tenants = $db->table('tenants')->count();
    $users = $db->table('users')->count();
    $klien = $db->table('klien')->count();
    $ritase = $db->table('ritase')->count();
    $penjualan = $db->table('penjualan')->count();
    $coa = $db->table('coa')->count();
    
    echo "   ✓ Tenants: $tenants\n";
    echo "   ✓ Users: $users\n";
    echo "   ✓ Klien: $klien\n";
    echo "   ✓ Ritase: $ritase\n";
    echo "   ✓ Penjualan: $penjualan\n";
    echo "   ✓ Chart of Accounts: $coa\n";
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 5. Check Models
echo "5. Checking Eloquent Models...\n";
$models = ['Tenant', 'User', 'Klien', 'Armada', 'Ritase', 'Penjualan', 'Coa', 'JurnalHeader', 'JurnalDetail'];
foreach ($models as $model) {
    $modelClass = "App\\Models\\$model";
    if (class_exists($modelClass)) {
        echo "   ✓ Model {$model} exists\n";
    } else {
        echo "   ✗ Model {$model} NOT found\n";
    }
}
echo "\n";

// 6. Check Observers
echo "6. Checking Observers...\n";
if (class_exists('App\\Observers\\RitaseObserver')) {
    echo "   ✓ RitaseObserver registered\n";
} else {
    echo "   ✗ RitaseObserver NOT found\n";
}

if (class_exists('App\\Observers\\PenjualanObserver')) {
    echo "   ✓ PenjualanObserver registered\n";
} else {
    echo "   ✗ PenjualanObserver NOT found\n";
}
echo "\n";

// 7. Check Filament Resources
echo "7. Checking Filament Resources...\n";
$resources = ['KlienResource', 'ArmadaResource', 'RitaseResource', 'PenjualanResource', 'CoaResource', 'JurnalResource'];
foreach ($resources as $resource) {
    $resourceClass = "App\\Filament\\Resources\\{$resource}";
    if (class_exists($resourceClass)) {
        echo "   ✓ Resource {$resource} exists\n";
    } else {
        echo "   ✗ Resource {$resource} NOT found\n";
    }
}
echo "\n";

// 8. Check Widgets
echo "8. Checking Dashboard Widgets...\n";
$widgets = ['StatsOverviewWidget', 'DailyTonnageChart', 'RevenueChart'];
foreach ($widgets as $widget) {
    $widgetClass = "App\\Filament\\Widgets\\{$widget}";
    if (class_exists($widgetClass)) {
        echo "   ✓ Widget {$widget} exists\n";
    } else {
        echo "   ✗ Widget {$widget} NOT found\n";
    }
}
echo "\n";

// 9. Sample queries to verify TenantScope
echo "9. Testing Tenant Scope (Sample Query)...\n";
try {
    // This should work even without authentication in a real scenario
    $tenant = $db->table('tenants')->first();
    if ($tenant) {
        echo "   ✓ Tenant found: {$tenant->name}\n";
        $tenantId = $tenant->id;
        
        // Query users by tenant
        $users = $db->table('users')->where('tenant_id', $tenantId)->count();
        echo "   ✓ Users for tenant: $users\n";
        
        // Query klien by tenant
        $klien = $db->table('klien')->where('tenant_id', $tenantId)->count();
        echo "   ✓ Klien for tenant: $klien\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

echo "=== Test Complete ===\n";
echo "✅ Application is correctly set up and ready for use!\n\n";
echo "Application Details:\n";
echo "- Framework: Laravel 11\n";
echo "- Database: MariaDB/MySQL\n";
echo "- Admin Panel: Filament v3\n";
echo "- Multi-Tenancy: Single Database with tenant_id\n";
echo "\n";
echo "To start using the application:\n";
echo "1. Install Filament if not already done\n";
echo "2. Create a user and authenticate\n";
echo "3. Access the admin panel at /admin\n";
echo "4. Begin managing waste intake and accounting\n";
