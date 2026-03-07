<?php

namespace App\Filament\Resources\RitaseResource\Pages;

use App\Filament\Resources\RitaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRitases extends ManageRecords
{
    protected static string $resource = RitaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
