<?php

namespace App\Filament\Resources\JurnalKas\Pages;

use App\Filament\Resources\JurnalKas\JurnalKasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJurnalKas extends ListRecords
{
    protected static string $resource = JurnalKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
