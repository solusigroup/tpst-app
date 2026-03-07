<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Invoice';

    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('Informasi Invoice')
                    ->schema([
                        Forms\Components\Select::make('klien_id')
                            ->relationship('klien', 'nama_klien')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('nomor_invoice')
                            ->placeholder('Otomatis')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('periode_bulan')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ])
                            ->required(),
                        Forms\Components\Select::make('periode_tahun')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y') - 2, date('Y') + 1), range(date('Y') - 2, date('Y') + 1)))
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_invoice')
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_jatuh_tempo')
                            ->default(now()->addDays(14))
                            ->required(),
                        Forms\Components\TextInput::make('total_tagihan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Sent' => 'Sent',
                                'Paid' => 'Paid',
                                'Canceled' => 'Canceled',
                            ])
                            ->default('Draft')
                            ->required(),
                        Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_invoice')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('klien.nama_klien')
                    ->label('Klien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->getStateUsing(fn ($record) => $record->periode_bulan . '/' . $record->periode_tahun),
                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Sent' => 'info',
                        'Draft' => 'warning',
                        'Canceled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('tanggal_invoice')
                    ->label('Tgl Invoice')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\Action::make('cetak_invoice')
                    ->label('Cetak')
                    ->url(fn (Invoice $record): string => route('invoices.print', $record))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-printer')
                    ->color('success'),
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
            'index' => Pages\ManageInvoices::route('/'),
        ];
    }
}
