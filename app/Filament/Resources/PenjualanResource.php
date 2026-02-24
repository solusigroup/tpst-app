<?php

namespace App\Filament\Resources;

use App\Models\Penjualan;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Penjualan')->schema([
                Forms\Components\Select::make('klien_id')->relationship('klien', 'nama_klien')->required(),
                Forms\Components\DatePickerInput::make('tanggal')->required(),
                Forms\Components\TextInput::make('jenis_produk')->required(),
            ]),
            Forms\Components\Section::make('Detail Penjualan')->schema([
                Forms\Components\TextInput::make('berat_kg')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, Get $get) => $set('total_harga', ($get('berat_kg') ?? 0) * ($get('harga_satuan') ?? 0))),
                Forms\Components\TextInput::make('harga_satuan')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, Get $get) => $set('total_harga', ($get('berat_kg') ?? 0) * ($get('harga_satuan') ?? 0))),
                Forms\Components\TextInput::make('total_harga')
                    ->numeric()
                    ->readonly()
                    ->dehydrated(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('klien.nama_klien'),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('jenis_produk'),
                Tables\Columns\TextColumn::make('berat_kg')->numeric(),
                Tables\Columns\TextColumn::make('total_harga')->numeric(),
            ]);
    }
}
