<?php

namespace App\Filament\Resources;

use App\Models\Ritase;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RitaseResource extends Resource
{
    protected static ?string $model = Ritase::class;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Ritase')->schema([
                Forms\Components\TextInput::make('nomor_tiket')->required(),
                Forms\Components\Select::make('armada_id')->relationship('armada', 'plat_nomor')->required(),
                Forms\Components\Select::make('klien_id')->relationship('klien', 'nama_klien')->required(),
                Forms\Components\DateTimePickerInput::make('waktu_masuk')->required(),
                Forms\Components\DateTimePickerInput::make('waktu_keluar'),
            ]),
            Forms\Components\Section::make('Pengukuran Berat')->schema([
                Forms\Components\TextInput::make('berat_bruto')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, Get $get) => $set('berat_netto', ($get('berat_bruto') ?? 0) - ($get('berat_tarra') ?? 0))),
                Forms\Components\TextInput::make('berat_tarra')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, Get $get) => $set('berat_netto', ($get('berat_bruto') ?? 0) - ($get('berat_tarra') ?? 0))),
                Forms\Components\TextInput::make('berat_netto')
                    ->numeric()
                    ->readonly()
                    ->dehydrated(),
            ]),
            Forms\Components\Section::make('Detail')->schema([
                Forms\Components\TextInput::make('jenis_sampah'),
                Forms\Components\TextInput::make('biaya_tipping')->numeric(),
                Forms\Components\Select::make('status')->options([
                    'masuk' => 'Masuk',
                    'timbang' => 'Timbang',
                    'keluar' => 'Keluar',
                    'selesai' => 'Selesai',
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_tiket')->searchable(),
                Tables\Columns\TextColumn::make('armada.plat_nomor'),
                Tables\Columns\TextColumn::make('klien.nama_klien'),
                Tables\Columns\TextColumn::make('berat_netto')->numeric(),
                Tables\Columns\TextColumn::make('status')->badge(),
            ]);
    }
}
