<?php

namespace App\Filament\Resources;

use App\Models\Armada;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArmadaResource extends Resource
{
    protected static ?string $model = Armada::class;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Select::make('klien_id')
                ->relationship('klien', 'nama_klien')
                ->required(),
            Forms\Components\TextInput::make('plat_nomor')
                ->required()
                ->unique(Armada::class, 'plat_nomor'),
            Forms\Components\TextInput::make('kapasitas_maksimal')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plat_nomor')->searchable(),
                Tables\Columns\TextColumn::make('klien.nama_klien'),
                Tables\Columns\TextColumn::make('kapasitas_maksimal'),
            ]);
    }
}
