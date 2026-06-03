<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Hash;

class ResetTenantPassword extends Command
{
    protected $signature = 'tenant:reset-password {domain} {--email=} {--password=}';
    protected $description = 'Reset password for a user on a specific tenant domain';

    public function handle()
    {
        $domain = $this->argument('domain');

        $tenant = Tenant::withoutGlobalScope(TenantScope::class)
            ->where('domain', 'like', "%{$domain}%")
            ->first();

        if (!$tenant) {
            $this->error("Tenant with domain containing '{$domain}' not found.");
            return 1;
        }

        $this->info("Tenant: {$tenant->name} (ID: {$tenant->id}, Domain: {$tenant->domain})");
        $this->newLine();

        // List users
        $users = User::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenant->id)
            ->get(['id', 'name', 'email', 'username', 'role']);

        if ($users->isEmpty()) {
            $this->error('No users found for this tenant.');
            return 1;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Username', 'Role'],
            $users->map(fn($u) => [$u->id, $u->name, $u->email, $u->username, $u->role])
        );

        // Select user
        $email = $this->option('email');
        if (!$email) {
            $email = $this->choice(
                'Pilih user yang ingin di-reset password-nya:',
                $users->pluck('email')->toArray()
            );
        }

        $user = User::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenant->id)
            ->where('email', $email)
            ->first();

        if (!$user) {
            $this->error("User '{$email}' not found.");
            return 1;
        }

        // Set password
        $password = $this->option('password');
        if (!$password) {
            $password = $this->ask('Masukkan password baru', 'password123');
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->newLine();
        $this->info('✅ Password berhasil di-reset!');
        $this->table(
            ['Field', 'Value'],
            [
                ['Tenant', $tenant->name],
                ['Domain', $tenant->domain],
                ['User', $user->name],
                ['Email', $user->email],
                ['Username', $user->username],
                ['New Password', $password],
            ]
        );

        return 0;
    }
}
