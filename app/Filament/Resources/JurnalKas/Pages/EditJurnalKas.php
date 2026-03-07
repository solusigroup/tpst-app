<?php

namespace App\Filament\Resources\JurnalKas\Pages;

use App\Filament\Resources\JurnalKas\JurnalKasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJurnalKas extends EditRecord
{
    protected static string $resource = JurnalKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
