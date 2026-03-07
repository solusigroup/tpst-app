<?php

namespace App\Filament\Pages;

use App\Models\Coa;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class ArusKas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'Arus Kas';
    protected static ?string $title = 'Laporan Arus Kas';
    protected static ?string $slug = 'laporan-arus-kas';
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string | null
    {
        return 'Laporan Keuangan';
    }

    protected string $view = 'filament.pages.arus-kas';

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
        // Arus Kas Metode Langsung - grouped by activity
        // Kas/Bank accounts (Asset type, Aset Lancar, kode starts with 11)
        $kasAccounts = Coa::where('tipe', 'Asset')
            ->where('klasifikasi', 'Aset Lancar')
            ->where('kode_akun', 'like', '11%')
            ->pluck('id');

        // Operating: Revenue & Expense related cash flows
        $operasi = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->join('coa', 'jd.coa_id', '=', 'coa.id')
            ->where('jh.status', 'posted')
            ->whereIn('jd.coa_id', $kasAccounts)
            ->when($this->dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $this->dari))
            ->when($this->sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $this->sampai))
            ->select([
                'coa.kode_akun', 'coa.nama_akun',
                DB::raw('SUM(jd.debit) as kas_masuk'),
                DB::raw('SUM(jd.kredit) as kas_keluar'),
                DB::raw('SUM(jd.debit) - SUM(jd.kredit) as kas_bersih'),
            ])
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun')
            ->get();

        $totalKasMasuk = $operasi->sum('kas_masuk');
        $totalKasKeluar = $operasi->sum('kas_keluar');
        $totalKasBersih = $operasi->sum('kas_bersih');

        // Calculate beginning and ending cash balance
        $saldoAwal = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->where('jh.status', 'posted')
            ->whereIn('jd.coa_id', $kasAccounts)
            ->when($this->dari, fn ($q) => $q->whereDate('jh.tanggal', '<', $this->dari))
            ->selectRaw('COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0) as saldo')
            ->value('saldo') ?? 0;

        $saldoAkhir = $saldoAwal + $totalKasBersih;

        return compact('operasi', 'totalKasMasuk', 'totalKasKeluar', 'totalKasBersih', 'saldoAwal', 'saldoAkhir');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('exportExcel')
                ->label('Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $filename = 'Arus_Kas_' . now()->format('Y-m-d_His') . '.xlsx';
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\ArusKasExport($this->getReportData(), $this->dari, $this->sampai),
                        $filename
                    );
                }),
            
            \Filament\Actions\Action::make('exportPdf')
                ->label('PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $data = $this->getReportData();
                    $filename = 'Arus_Kas_' . now()->format('Y-m-d_His') . '.pdf';

                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.arus-kas', [
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
