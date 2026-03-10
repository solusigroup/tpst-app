<?php

namespace App\Filament\Pages;

use App\Models\Ritase;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class LaporanRitase extends Page implements HasTable
{
    use InteractsWithTable, \BezhanSalleh\FilamentShield\Traits\HasPageShield;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Laporan Ritase';
    protected static ?string $title = 'Laporan Ritase';
    protected static ?string $slug = 'laporan-ritase';

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan';
    }

    protected string $view = 'filament.pages.laporan-ritase';

    public function table(Table $table): Table
    {
        return $table
            ->query(Ritase::query()->with(['armada', 'klien']))
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tanggal')
                    ->state(fn (Ritase $record) => $record->waktu_masuk)
                    ->date('d M Y')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('waktu_masuk', $direction)),
                Tables\Columns\TextColumn::make('nomor_tiket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('armada.plat_nomor')
                    ->label('Plat Nomor'),
                Tables\Columns\TextColumn::make('klien.nama_klien')
                    ->label('Klien'),
                Tables\Columns\TextColumn::make('berat_netto')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_tipping')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'masuk' => 'warning',
                        'timbang' => 'info',
                        'keluar' => 'primary',
                        'selesai' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('waktu_masuk', 'desc')
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
                            ->when($data['dari'], fn ($q, $date) => $q->whereDate('waktu_masuk', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->whereDate('waktu_masuk', '<=', $date));
                    }),
                Tables\Filters\SelectFilter::make('klien_id')
                    ->label('Klien')
                    ->relationship('klien', 'nama_klien')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'masuk' => 'Masuk',
                        'timbang' => 'Timbang',
                        'keluar' => 'Keluar',
                        'selesai' => 'Selesai',
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('cetakInvoiceGlobal')
                ->label('Cetak Invoice Global')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->modalHeading('Invoice Global Ritase')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalWidth('7xl')
                ->modalContent(function () {
                    // Extract data from table filters
                    $filters = $this->getTableFilters();
                    
                    $dari = $filters['periode']['dari'] ?? now()->startOfMonth()->format('Y-m-d');
                    $sampai = $filters['periode']['sampai'] ?? now()->format('Y-m-d');
                    $klienId = $filters['klien_id']['value'] ?? null;
                    
                    $klien = $klienId ? \App\Models\Klien::find($klienId) : null;
                    
                    $query = Ritase::query()
                        ->with(['armada', 'klien'])
                        ->whereDate('waktu_masuk', '>=', $dari)
                        ->whereDate('waktu_masuk', '<=', $sampai)
                        ->when($klienId, fn ($q) => $q->where('klien_id', $klienId))
                        ->orderBy('waktu_masuk', 'asc');
                        
                    $records = $query->get();
                    
                    return view('ritase.invoice-global', [
                        'records' => $records,
                        'klien' => $klien,
                        'dari' => $dari,
                        'sampai' => $sampai,
                    ]);
                }),
            \Filament\Actions\Action::make('downloadPdfGlobal')
                ->label('Download PDF Global')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $filters = $this->getTableFilters();
                    
                    $dari = $filters['periode']['dari'] ?? now()->startOfMonth()->format('Y-m-d');
                    $sampai = $filters['periode']['sampai'] ?? now()->format('Y-m-d');
                    $klienId = $filters['klien_id']['value'] ?? null;
                    
                    $klien = $klienId ? \App\Models\Klien::find($klienId) : null;
                    
                    $query = Ritase::query()
                        ->with(['armada', 'klien'])
                        ->whereDate('waktu_masuk', '>=', $dari)
                        ->whereDate('waktu_masuk', '<=', $sampai)
                        ->when($klienId, fn ($q) => $q->where('klien_id', $klienId))
                        ->orderBy('waktu_masuk', 'asc');
                        
                    $records = $query->get();
                    $filename = "Invoice_Global_Ritase_" . ($klien ? str_replace(' ', '_', $klien->nama_klien) : 'Semua') . "_" . now()->format('Ymd_His') . ".pdf";

                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ritase.invoice-global', [
                        'records' => $records,
                        'klien' => $klien,
                        'dari' => $dari,
                        'sampai' => $sampai,
                        'isExport' => true,
                    ])->setPaper('a4', 'portrait');

                    return response()->streamDownload(fn () => print($pdf->output()), $filename);
                }),
        ];
    }
}
