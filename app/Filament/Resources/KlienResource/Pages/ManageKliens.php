<?php

namespace App\Filament\Resources\KlienResource\Pages;

use App\Filament\Resources\KlienResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKliens extends ManageRecords
{
    protected static string $resource = KlienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
