<?php

namespace App\Filament\Pages;

use App\Models\Coa;
use App\Models\JurnalDetail;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class BukuBesar extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $title = 'Buku Besar';
    protected static ?string $slug = 'laporan-buku-besar';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan Keuangan';
    }

    protected string $view = 'filament.pages.laporan-keuangan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JurnalDetail::query()
                    ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                    ->join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
                    ->select([
                        'jurnal_detail.*',
                        'jurnal_header.tanggal',
                        'jurnal_header.deskripsi',
                        'coa.kode_akun',
                        'coa.nama_akun',
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kode_akun')
                    ->label('Kode Akun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_akun')
                    ->label('Nama Akun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Keterangan')
                    ->limit(40),
                Tables\Columns\TextColumn::make('debit')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kredit')
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
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q, $d) => $q->whereDate('jurnal_header.tanggal', '>=', $d))
                            ->when($data['sampai'], fn ($q, $d) => $q->whereDate('jurnal_header.tanggal', '<=', $d));
                    }),
                Tables\Filters\SelectFilter::make('coa_id')
                    ->label('Akun')
                    ->options(fn () => Coa::pluck('nama_akun', 'id')->toArray())
                    ->searchable(),
            ]);
    }
}
