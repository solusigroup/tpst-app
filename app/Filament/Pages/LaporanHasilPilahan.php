<?php

namespace App\Filament\Pages;

use App\Models\HasilPilahan;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class LaporanHasilPilahan extends Page implements HasTable
{
    use InteractsWithTable, \BezhanSalleh\FilamentShield\Traits\HasPageShield;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationLabel = 'Laporan Hasil Pilahan';
    protected static ?string $title = 'Laporan Hasil Pilahan Sampah';
    protected static ?string $slug = 'laporan-hasil-pilahan';

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan';
    }

    protected string $view = 'filament.pages.laporan-hasil-pilahan';

    public function table(Table $table): Table
    {
        return $table
            ->query(HasilPilahan::query())
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Organik' => 'success',
                        'Anorganik' => 'info',
                        'B3' => 'danger',
                        'Residu' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tonase')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('officer')
                    ->label('Petugas')
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'Organik' => 'Organik',
                        'Anorganik' => 'Anorganik',
                        'B3' => 'B3',
                        'Residu' => 'Residu',
                    ]),
            ]);
    }
}
