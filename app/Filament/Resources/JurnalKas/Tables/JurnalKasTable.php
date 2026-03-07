<?php

namespace App\Filament\Resources\JurnalKas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class JurnalKasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Penerimaan' => 'success',
                        'Pengeluaran' => 'danger',
                    }),
                \Filament\Tables\Columns\TextColumn::make('coaKas.nama_akun')
                    ->label('Akun Kas')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('coaLawan.nama_akun')
                    ->label('Akun Lawan')
                    ->sortable()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('nominal')
                    ->numeric()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(30)
                    ->searchable(),
                \Filament\Tables\Columns\ImageColumn::make('bukti_transaksi')
                    ->label('Bukti')
                    ->circular(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('tipe')
                    ->options([
                        'Penerimaan' => 'Penerimaan',
                        'Pengeluaran' => 'Pengeluaran',
                    ]),
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
