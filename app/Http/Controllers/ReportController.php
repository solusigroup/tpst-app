<?php

namespace App\Http\Controllers;

use App\Models\JurnalHeader;
use App\Models\JurnalDetail;
use App\Models\Coa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Build a printed Profit & Loss (Laba/Rugi) report.
     * Respects the current tenant_id from the authenticated user.
     */
    public function cetakLabaRugi(Request $request)
    {
        // Parameters for Month and Year, default to current
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $tenantId = auth()->user()->tenant_id;

        // Query Revenue (Pendapatan)
        // Standard Accounting: Revenue balance = Kredit - Debit
        $revenueDetails = JurnalDetail::query()
            ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
            ->where('jurnal_header.tenant_id', $tenantId)
            ->whereBetween('jurnal_header.tanggal', [$startDate, $endDate])
            ->where('coa.tipe', 'Revenue')
            ->select(
                'coa.kode_akun',
                'coa.nama_akun',
                DB::raw('SUM(jurnal_detail.kredit - jurnal_detail.debit) as total')
            )
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun')
            ->orderBy('coa.kode_akun')
            ->get();

        // Query Expenses (Beban)
        // Standard Accounting: Expense balance = Debit - Kredit
        $expenseDetails = JurnalDetail::query()
            ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
            ->where('jurnal_header.tenant_id', $tenantId)
            ->whereBetween('jurnal_header.tanggal', [$startDate, $endDate])
            ->where('coa.tipe', 'Expense')
            ->select(
                'coa.kode_akun',
                'coa.nama_akun',
                DB::raw('SUM(jurnal_detail.debit - jurnal_detail.kredit) as total')
            )
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun')
            ->orderBy('coa.kode_akun')
            ->get();

        $totalRevenue = $revenueDetails->sum('total');
        $totalExpenses = $expenseDetails->sum('total');
        $netProfit = $totalRevenue - $totalExpenses;

        $tenant = auth()->user()->tenant;

        // Format for Indonesian display
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $periodLabel = $monthNames[$month] . ' ' . $year;

        return view('reports.laba-rugi', [
            'revenue' => $revenueDetails,
            'expenses' => $expenseDetails,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'period' => $periodLabel,
            'tenant' => $tenant,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Build a printed Posisi Keuangan (Neraca) report with strict accounting logic.
     */
    public function cetakPosisiKeuangan(Request $request)
    {
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));
        $tenantId = auth()->user()->tenant_id;

        // Base query for the selected period (cumulative)
        $query = JurnalDetail::whereHas('jurnalHeader', function($q) use ($month, $year, $tenantId) {
            $q->whereDate('tanggal', '<=', Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d'))
              ->where('tenant_id', $tenantId);
        });

        $details = $query->with('coa')->get();

        $aset = 0;
        $liabilitas = 0;
        $ekuitasDasar = 0;
        $pendapatan = 0;
        $beban = 0;

        // Collections for the view
        $asetItems = collect();
        $liabilitasItems = collect();
        $ekuitasItems = collect();

        // Grouping and calculating based on Normal Balances
        foreach ($details as $detail) {
            $type = strtolower($detail->coa->tipe);
            
            if ($type === 'asset' || $type === 'aset') {
                $val = ($detail->debit - $detail->kredit);
                $aset += $val;
                $this->accumulateCOA($asetItems, $detail, $val);
            } elseif ($type === 'liability' || $type === 'kewajiban' || $type === 'liabilitas') {
                $val = ($detail->kredit - $detail->debit);
                $liabilitas += $val;
                $this->accumulateCOA($liabilitasItems, $detail, $val);
            } elseif ($type === 'equity' || $type === 'ekuitas') {
                $val = ($detail->kredit - $detail->debit);
                $ekuitasDasar += $val;
                $this->accumulateCOA($ekuitasItems, $detail, $val);
            } elseif ($type === 'revenue' || $type === 'pendapatan') {
                $pendapatan += ($detail->kredit - $detail->debit);
            } elseif ($type === 'expense' || $type === 'beban') {
                $beban += ($detail->debit - $detail->kredit);
            }
        }

        // Calculate Net Income and Total Equity
        $labaRugiBerjalan = $pendapatan - $beban;
        $totalEkuitas = $ekuitasDasar + $labaRugiBerjalan;
        $totalPasiva = $liabilitas + $totalEkuitas;

        // Validation Check
        $isBalanced = (round($aset, 2) === round($totalPasiva, 2));
        $selisih = abs($aset - $totalPasiva);

        $tenant = auth()->user()->tenant;
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $periodLabel = 'Per ' . Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('d') . ' ' . $monthNames[$month] . ' ' . $year;

        return view('reports.posisi-keuangan', compact(
            'month', 'year', 'aset', 'liabilitas', 'ekuitasDasar', 
            'labaRugiBerjalan', 'totalEkuitas', 'totalPasiva', 
            'isBalanced', 'selisih', 'periodLabel', 'tenant',
            'asetItems', 'liabilitasItems', 'ekuitasItems'
        ));
    }

    private function accumulateCOA($collection, $detail, $value)
    {
        $coaId = $detail->coa_id;
        if (!isset($collection[$coaId])) {
            $collection[$coaId] = (object)[
                'kode_akun' => $detail->coa->kode_akun,
                'nama_akun' => $detail->coa->nama_akun,
                'klasifikasi' => $detail->coa->klasifikasi,
                'saldo' => 0
            ];
        }
        $collection[$coaId]->saldo += $value;
    }

    /**
     * Build a printed Arus Kas report.
     */
    public function cetakArusKas(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $dari = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $sampai = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $tenantId = auth()->user()->tenant_id;

        $kasAccounts = Coa::where('tenant_id', $tenantId)
            ->where('tipe', 'Asset')
            ->where('klasifikasi', 'Aset Lancar')
            ->where('kode_akun', 'like', '11%')
            ->pluck('id');

        $operasi = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->join('coa', 'jd.coa_id', '=', 'coa.id')
            ->where('jh.tenant_id', $tenantId)
            ->whereIn('jd.coa_id', $kasAccounts)
            ->whereDate('jh.tanggal', '>=', $dari)
            ->whereDate('jh.tanggal', '<=', $sampai)
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

        $saldoAwal = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->where('jh.tenant_id', $tenantId)
            ->whereIn('jd.coa_id', $kasAccounts)
            ->whereDate('jh.tanggal', '<', $dari)
            ->selectRaw('COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0) as saldo')
            ->value('saldo') ?? 0;

        $saldoAkhir = $saldoAwal + $totalKasBersih;

        $tenant = auth()->user()->tenant;
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $periodLabel = $monthNames[$month] . ' ' . $year;

        return view('reports.arus-kas', compact(
            'operasi', 'totalKasMasuk', 'totalKasKeluar', 'totalKasBersih',
            'saldoAwal', 'saldoAkhir', 'periodLabel', 'tenant'
        ));
    }

    /**
     * Build a printed Perubahan Ekuitas report.
     */
    public function cetakPerubahanEkuitas(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $dari = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $sampai = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $tenantId = auth()->user()->tenant_id;

        $ekuitasAccounts = Coa::where('tenant_id', $tenantId)
            ->where('tipe', 'Equity')
            ->orderBy('kode_akun')
            ->get();

        $rows = [];
        $totalSaldoAwal = 0;
        $totalPenambahan = 0;
        $totalPengurangan = 0;
        $totalSaldoAkhir = 0;

        foreach ($ekuitasAccounts as $akun) {
            $saldoAwal = DB::table('jurnal_detail as jd')
                ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
                ->where('jh.tenant_id', $tenantId)
                ->where('jd.coa_id', $akun->id)
                ->whereDate('jh.tanggal', '<', $dari)
                ->selectRaw('COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0) as saldo')
                ->value('saldo') ?? 0;

            $mutasi = DB::table('jurnal_detail as jd')
                ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
                ->where('jh.tenant_id', $tenantId)
                ->where('jd.coa_id', $akun->id)
                ->whereDate('jh.tanggal', '>=', $dari)
                ->whereDate('jh.tanggal', '<=', $sampai)
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

        $labaRugi = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->join('coa', 'jd.coa_id', '=', 'coa.id')
            ->where('jh.tenant_id', $tenantId)
            ->whereIn('coa.tipe', ['Revenue', 'Expense'])
            ->whereDate('jh.tanggal', '>=', $dari)
            ->whereDate('jh.tanggal', '<=', $sampai)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN coa.tipe = 'Revenue' THEN jd.kredit - jd.debit ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN coa.tipe = 'Expense' THEN jd.debit - jd.kredit ELSE 0 END), 0) as laba_rugi
            ")
            ->value('laba_rugi') ?? 0;

        $totalSaldoAkhir += $labaRugi;

        $tenant = auth()->user()->tenant;
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $periodLabel = $monthNames[$month] . ' ' . $year;

        return view('reports.perubahan-ekuitas', compact(
            'rows', 'labaRugi', 'totalSaldoAwal', 'totalPenambahan', 'totalPengurangan', 'totalSaldoAkhir',
            'periodLabel', 'tenant'
        ));
    }

    /**
     * Build a printed Neraca Saldo report.
     */
    public function cetakNeracaSaldo(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $dari = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $sampai = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $tenantId = auth()->user()->tenant_id;

        $rows = Coa::query()
            ->select([
                'coa.*',
                DB::raw('COALESCE(SUM(jd.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(jd.kredit), 0) as total_kredit'),
                DB::raw('COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0) as saldo'),
            ])
            ->leftJoin('jurnal_detail as jd', 'coa.id', '=', 'jd.coa_id')
            ->leftJoin('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->where('coa.tenant_id', $tenantId)
            ->whereDate('jh.tanggal', '>=', $dari)
            ->whereDate('jh.tanggal', '<=', $sampai)
            ->groupBy('coa.id', 'coa.tenant_id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi', 'coa.created_at', 'coa.updated_at')
            ->orderBy('coa.kode_akun')
            ->get();

        $totalDebit = $rows->sum('total_debit');
        $totalKredit = $rows->sum('total_kredit');

        $tenant = auth()->user()->tenant;
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $periodLabel = $monthNames[$month] . ' ' . $year;

        return view('reports.neraca-saldo', compact('rows', 'totalDebit', 'totalKredit', 'periodLabel', 'tenant'));
    }
}
