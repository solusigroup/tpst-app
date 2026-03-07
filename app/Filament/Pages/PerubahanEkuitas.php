<?php

namespace App\Filament\Pages;

use App\Models\Coa;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class PerubahanEkuitas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'Perubahan Ekuitas';
    protected static ?string $title = 'Laporan Perubahan Ekuitas';
    protected static ?string $slug = 'laporan-perubahan-ekuitas';
    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan Keuangan';
    }

    protected string $view = 'filament.pages.perubahan-ekuitas';

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
        $ekuitasAccounts = Coa::where('tipe', 'Equity')->orderBy('kode_akun')->get();

        $rows = [];
        $totalSaldoAwal = 0;
        $totalPenambahan = 0;
        $totalPengurangan = 0;
        $totalSaldoAkhir = 0;

        foreach ($ekuitasAccounts as $akun) {
            $saldoAwal = DB::table('jurnal_detail as jd')
                ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
                ->where('jd.coa_id', $akun->id)
                ->when($this->dari, fn ($q) => $q->whereDate('jh.tanggal', '<', $this->dari))
                ->selectRaw('COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0) as saldo')
                ->value('saldo') ?? 0;

            $mutasi = DB::table('jurnal_detail as jd')
                ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
                ->where('jd.coa_id', $akun->id)
                ->when($this->dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $this->dari))
                ->when($this->sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $this->sampai))
                ->selectRaw('COALESCE(SUM(jd.kredit), 0) as penambahan, COALESCE(SUM(jd.debit), 0) as pengurangan')
                ->first();

            $penambahan = $mutasi->penambahan ?? 0;
            $pengurangan = $mutasi->pengurangan ?? 0;
            $saldoAkhir = $saldoAwal + $penambahan - $pengurangan;

            $totalSaldoAwal += $saldoAwal;
            $totalPenambahan += $penambahan;
            $totalPengurangan += $pengurangan;
            $totalSaldoAkhir += $saldoAkhir;

            $rows[] = [
                'kode_akun' => $akun->kode_akun,
                'nama_akun' => $akun->nama_akun,
                'saldo_awal' => $saldoAwal,
                'penambahan' => $penambahan,
                'pengurangan' => $pengurangan,
                'saldo_akhir' => $saldoAkhir,
            ];
        }

        // Add Laba/Rugi Bersih
        $labaRugi = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->join('coa', 'jd.coa_id', '=', 'coa.id')
            ->whereIn('coa.tipe', ['Revenue', 'Expense'])
            ->when($this->dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $this->sampai))
            ->selectRaw("
                COALESCE(SUM(CASE WHEN coa.tipe = 'Revenue' THEN jd.kredit - jd.debit ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN coa.tipe = 'Expense' THEN jd.debit - jd.kredit ELSE 0 END), 0) as laba_rugi
            ")
            ->value('laba_rugi') ?? 0;

        $totalSaldoAkhir += $labaRugi;

        return compact('rows', 'labaRugi', 'totalSaldoAwal', 'totalPenambahan', 'totalPengurangan', 'totalSaldoAkhir');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('exportExcel')
                ->label('Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $filename = 'Perubahan_Ekuitas_' . now()->format('Y-m-d_His') . '.xlsx';
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\PerubahanEkuitasExport($this->getReportData(), $this->dari, $this->sampai),
                        $filename
                    );
                }),
            
            \Filament\Actions\Action::make('exportPdf')
                ->label('PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $data = $this->getReportData();
                    $filename = 'Perubahan_Ekuitas_' . now()->format('Y-m-d_His') . '.pdf';

                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.perubahan-ekuitas', [
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
