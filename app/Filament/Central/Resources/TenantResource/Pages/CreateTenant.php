<?php

namespace App\Filament\Central\Resources\TenantResource\Pages;

use App\Filament\Central\Resources\TenantResource;
use App\Services\TenantProvisioningService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = new TenantProvisioningService();

        $tenant = $service->provision(
            tenantData: [
                'name' => $data['name'],
                'domain' => $data['domain'],
            ],
            adminData: [
                'name' => $data['admin_name'],
                'username' => $data['admin_username'] ?? null,
                'email' => $data['admin_email'],
                'password' => $data['admin_password'],
            ]
        );

        Notification::make()
            ->title('Tenant berhasil dibuat!')
            ->body("Tenant \"{$tenant->name}\" telah dibuat dengan admin user dan COA default.")
            ->success()
            ->send();

        return $tenant;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
