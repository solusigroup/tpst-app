<?php

namespace App\Filament\Pages;

use App\Models\Coa;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class LabaRugi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laba Rugi';
    protected static ?string $title = 'Laporan Laba Rugi';
    protected static ?string $slug = 'laporan-laba-rugi';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan Keuangan';
    }

    protected string $view = 'filament.pages.laba-rugi';

    #[Url]
    public ?string $dari = null;
    #[Url]
    public ?string $sampai = null;

    public function mount(): void
    {
        $this->dari = $this->dari ?? now()->startOfMonth()->format('Y-m-d');
        $this->sampai = $this->sampai ?? now()->format('Y-m-d');
    }

    public function getReportData(): array
    {
        $query = Coa::query()
            ->select([
                'coa.id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi',
                DB::raw("CASE
                    WHEN coa.tipe = 'Revenue' THEN COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0)
                    WHEN coa.tipe = 'Expense' THEN COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0)
                    ELSE 0
                END as saldo"),
            ])
            ->leftJoin('jurnal_detail as jd', 'coa.id', '=', 'jd.coa_id')
            ->leftJoin('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->whereIn('coa.tipe', ['Revenue', 'Expense'])
            ->where('jh.status', 'posted')
            ->when($this->dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $this->sampai))
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi')
            ->orderBy('coa.kode_akun')
            ->get();

        $pendapatan = $query->where('tipe', 'Revenue');
        $beban = $query->where('tipe', 'Expense');

        $totalPendapatan = $pendapatan->sum('saldo');
        $totalBeban = $beban->sum('saldo');
        $labaRugiBersih = $totalPendapatan - $totalBeban;

        return compact('pendapatan', 'beban', 'totalPendapatan', 'totalBeban', 'labaRugiBersih');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('exportExcel')
                ->label('Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $filename = 'Laba_Rugi_' . now()->format('Y-m-d_His') . '.xlsx';
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\LabaRugiExport($this->getReportData(), $this->dari, $this->sampai),
                        $filename
                    );
                }),
            
            \Filament\Actions\Action::make('exportPdf')
                ->label('PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $data = $this->getReportData();
                    $filename = 'Laba_Rugi_' . now()->format('Y-m-d_His') . '.pdf';

                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.laba-rugi', [
                        'data' => $data,
                        'dari' => $this->dari,
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
