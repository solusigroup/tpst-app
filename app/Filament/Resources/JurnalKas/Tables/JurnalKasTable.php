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
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'posted' => 'success',
                        'unposted' => 'warning',
                        default => 'gray',
                    }),
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
                \Filament\Actions\Action::make('post')
                    ->label('Post')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (\App\Models\JurnalKas $record) => $record->status !== 'posted')
                    ->action(function (\App\Models\JurnalKas $record) {
                        $record->update(['status' => 'posted']);
                        \App\Models\JurnalHeader::where('referensi_type', \App\Models\JurnalKas::class)
                            ->where('referensi_id', $record->id)
                            ->update(['status' => 'posted']);
                        \Filament\Notifications\Notification::make()->title('Kas berhasil di-post')->success()->send();
                    }),
                \Filament\Actions\Action::make('unpost')
                    ->label('Unpost')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (\App\Models\JurnalKas $record) => $record->status === 'posted')
                    ->action(function (\App\Models\JurnalKas $record) {
                        $record->update(['status' => 'unposted']);
                        \App\Models\JurnalHeader::where('referensi_type', \App\Models\JurnalKas::class)
                            ->where('referensi_id', $record->id)
                            ->update(['status' => 'unposted']);
                        \Filament\Notifications\Notification::make()->title('Kas di-unpost')->warning()->send();
                    }),
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
}
