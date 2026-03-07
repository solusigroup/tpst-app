<?php

namespace App\Filament\Pages;

use App\Models\Coa;
use App\Models\JurnalDetail;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class NeracaSaldo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Neraca Saldo';
    protected static ?string $title = 'Neraca Saldo';
    protected static ?string $slug = 'laporan-neraca-saldo';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan Keuangan';
    }

    protected string $view = 'filament.pages.neraca-saldo';

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
        $rows = Coa::query()
            ->select([
                'coa.*',
                DB::raw('COALESCE(SUM(jd.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(jd.kredit), 0) as total_kredit'),
                DB::raw('COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0) as saldo'),
            ])
            ->leftJoin('jurnal_detail as jd', 'coa.id', '=', 'jd.coa_id')
            ->leftJoin('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->when($this->dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $this->sampai))
            ->groupBy('coa.id', 'coa.tenant_id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi', 'coa.created_at', 'coa.updated_at')
            ->orderBy('coa.kode_akun')
            ->get();

        $totalDebit = $rows->sum('total_debit');
        $totalKredit = $rows->sum('total_kredit');

        return compact('rows', 'totalDebit', 'totalKredit');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('exportExcel')
                ->label('Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $filename = 'Neraca_Saldo_' . now()->format('Y-m-d_His') . '.xlsx';
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\NeracaSaldoExport($this->getReportData(), $this->dari, $this->sampai),
                        $filename
                    );
                }),
            
            \Filament\Actions\Action::make('exportPdf')
                ->label('PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $data = $this->getReportData();
                    $filename = 'Neraca_Saldo_' . now()->format('Y-m-d_His') . '.pdf';

                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.neraca-saldo', [
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
