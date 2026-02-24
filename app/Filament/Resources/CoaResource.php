<?php

namespace App\Filament\Resources;

use App\Models\Coa;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CoaResource extends Resource
{
    protected static ?string $model = Coa::class;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\TextInput::make('kode_akun')
                ->required()
                ->unique(Coa::class, 'kode_akun'),
            Forms\Components\TextInput::make('nama_akun')
                ->required(),
            Forms\Components\Select::make('tipe')
                ->options([
                    'Asset' => 'Asset',
                    'Liability' => 'Liability',
                    'Equity' => 'Equity',
                    'Revenue' => 'Revenue',
                    'Expense' => 'Expense',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_akun')->searchable(),
                Tables\Columns\TextColumn::make('nama_akun')->searchable(),
                Tables\Columns\TextColumn::make('tipe')->badge(),
            ]);
    }
}
