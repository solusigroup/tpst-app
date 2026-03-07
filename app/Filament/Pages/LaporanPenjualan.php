<?php

namespace App\Filament\Pages;

use App\Models\Penjualan;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class LaporanPenjualan extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $title = 'Laporan Penjualan';
    protected static ?string $slug = 'laporan-penjualan';

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan';
    }

    protected string $view = 'filament.pages.laporan-penjualan';

    public function table(Table $table): Table
    {
        return $table
            ->query(Penjualan::query()->with('klien'))
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('klien.nama_klien')
                    ->label('Klien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_produk')
                    ->label('Jenis Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('berat_kg')
                    ->label('Berat')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\Filter::make('periode')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')
                            ->default(now()->startOfMonth()),
                        \Filament\Forms\Components\DatePicker::make('sampai')
                            ->default(now()),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    }),
            ]);
    }
}
