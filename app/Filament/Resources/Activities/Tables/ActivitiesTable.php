<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('causer.name')
                    ->label('Pelaku')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('event')
                    ->label('Aksi')
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('subject_type')
                    ->label('Modul')
                    ->formatStateUsing(fn(string $state) => class_basename($state))
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
