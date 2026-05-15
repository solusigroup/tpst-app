<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = \App\Models\Tenant::where('domain', 'sampahjaya.simpleakunting.shop')->first();

        if (!$tenant) {
            $this->command->error("Tenant 'sampahjaya.simpleakunting.shop' not found.");
            return;
        }

        $user = \App\Models\User::withoutGlobalScopes()->updateOrCreate(
            ['username' => 'demo'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Demo User',
                'email' => 'demo@sampahjaya.com',
                'password' => \Illuminate\Support\Facades\Hash::make('demo1234'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $user->syncRoles(['manajemen']);

        $this->command->info("Demo user 'demo' created/updated for tenant '" . $tenant->name . "'.");
    }
}
