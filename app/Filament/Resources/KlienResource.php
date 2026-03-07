<?php

namespace App\Filament\Resources;

use App\Models\Klien;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KlienResource extends Resource
{
    protected static ?string $model = Klien::class;
    protected static ?string $navigationLabel = 'Klien';
    protected static ?string $label = 'Klien';
    protected static ?string $pluralLabel = 'Klien';

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_klien')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('jenis')
                ->options([
                    'DLH' => 'DLH',
                    'Swasta' => 'Swasta',
                    'Offtaker' => 'Offtaker',
                ])
                ->required(),
            Forms\Components\TextInput::make('kontak')
                ->tel(),
            Forms\Components\Textarea::make('alamat')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_klien')->searchable(),
                Tables\Columns\TextColumn::make('jenis'),
                Tables\Columns\TextColumn::make('kontak'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')->options([
                    'DLH' => 'DLH',
                    'Swasta' => 'Swasta',
                    'Offtaker' => 'Offtaker',
                ]),
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
            'index' => \App\Filament\Resources\KlienResource\Pages\ManageKliens::route('/'),
        ];
    }
}
