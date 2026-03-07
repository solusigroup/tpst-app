<?php

namespace App\Filament\Pages;

use App\Models\Coa;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class PosisiKeuangan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Posisi Keuangan';
    protected static ?string $title = 'Laporan Posisi Keuangan';
    protected static ?string $slug = 'laporan-posisi-keuangan';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan Keuangan';
    }

    protected string $view = 'filament.pages.posisi-keuangan';

    #[Url]
    public ?string $sampai = null;

    public function mount(): void
    {
        $this->sampai = $this->sampai ?? now()->format('Y-m-d');
    }

    public function getReportData(): array
    {
        $query = Coa::query()
            ->select([
                'coa.id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi',
                DB::raw("CASE
                    WHEN coa.tipe = 'Asset' THEN COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0)
                    ELSE COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0)
                END as saldo"),
            ])
            ->leftJoin('jurnal_detail as jd', 'coa.id', '=', 'jd.coa_id')
            ->leftJoin('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->whereIn('coa.tipe', ['Asset', 'Liability', 'Equity'])
            ->where('jh.status', 'posted')
            ->when($this->sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $this->sampai))
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi')
            ->orderBy('coa.kode_akun')
            ->get();

        $asetLancar = $query->where('klasifikasi', 'Aset Lancar');
        $asetTidakLancar = $query->where('klasifikasi', 'Aset Tidak Lancar');
        $liabilitasJangkaPendek = $query->where('klasifikasi', 'Liabilitas Jangka Pendek');
        $liabilitasJangkaPanjang = $query->where('klasifikasi', 'Liabilitas Jangka Panjang');
        $ekuitas = $query->where('klasifikasi', 'Ekuitas');

        $totalAsetLancar = $asetLancar->sum('saldo');
        $totalAsetTidakLancar = $asetTidakLancar->sum('saldo');
        $totalAset = $totalAsetLancar + $totalAsetTidakLancar;

        $totalLiabilitasJP = $liabilitasJangkaPendek->sum('saldo');
        $totalLiabilitasJPj = $liabilitasJangkaPanjang->sum('saldo');
        $totalLiabilitas = $totalLiabilitasJP + $totalLiabilitasJPj;

        $totalEkuitas = $ekuitas->sum('saldo');
        $totalLiabilitasEkuitas = $totalLiabilitas + $totalEkuitas;

        return compact(
            'asetLancar', 'asetTidakLancar',
            'liabilitasJangkaPendek', 'liabilitasJangkaPanjang', 'ekuitas',
            'totalAsetLancar', 'totalAsetTidakLancar', 'totalAset',
            'totalLiabilitasJP', 'totalLiabilitasJPj', 'totalLiabilitas',
            'totalEkuitas', 'totalLiabilitasEkuitas'
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('exportExcel')
                ->label('Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $filename = 'Posisi_Keuangan_' . now()->format('Y-m-d_His') . '.xlsx';
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\PosisiKeuanganExport($this->getReportData(), $this->sampai),
                        $filename
                    );
                }),
            
            \Filament\Actions\Action::make('exportPdf')
                ->label('PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $data = $this->getReportData();
                    $filename = 'Posisi_Keuangan_' . now()->format('Y-m-d_His') . '.pdf';

                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.posisi-keuangan', [
                        'data' => $data,
                        'sampai' => $this->sampai,
                        'isExport' => true,
                    ]);

                    return response()->streamDownload(fn () => print($pdf->output()), $filename);
                }),
                
            \Filament\Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->extraAttributes(['onclick' => 'window.print()']),
        ];
    }
}
