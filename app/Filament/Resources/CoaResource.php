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
    protected static ?string $navigationLabel = 'Chart of Account';
    protected static ?string $label = 'COA';
    protected static ?string $pluralLabel = 'Chart of Account';

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
                ->required()
                ->live(),
            Forms\Components\Select::make('klasifikasi')
                ->options(fn (\Filament\Schemas\Components\Utilities\Get $get) => match ($get('tipe')) {
                    'Asset' => [
                        'Aset Lancar' => 'Aset Lancar',
                        'Aset Tidak Lancar' => 'Aset Tidak Lancar',
                    ],
                    'Liability' => [
                        'Liabilitas Jangka Pendek' => 'Liabilitas Jangka Pendek',
                        'Liabilitas Jangka Panjang' => 'Liabilitas Jangka Panjang',
                    ],
                    'Equity' => ['Ekuitas' => 'Ekuitas'],
                    'Revenue' => ['Pendapatan' => 'Pendapatan'],
                    'Expense' => ['Beban' => 'Beban'],
                    default => [],
                })
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_akun')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama_akun')->searchable(),
                Tables\Columns\TextColumn::make('tipe')->badge(),
                Tables\Columns\TextColumn::make('klasifikasi')->badge()->color('gray'),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CoaResource\Pages\ManageCoas::route('/'),
        ];
    }
}
