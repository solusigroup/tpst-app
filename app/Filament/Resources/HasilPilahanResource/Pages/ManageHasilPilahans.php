<?php

namespace App\Filament\Resources\HasilPilahanResource\Pages;

use App\Filament\Resources\HasilPilahanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHasilPilahans extends ManageRecords
{
    protected static string $resource = HasilPilahanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
