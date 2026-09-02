<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Models\Penjualan;
use App\Models\HasilPilahan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\PengangkutanResidu;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // If the logged-in user is a regular employee, redirect to their attendance recap
        if (auth()->check() && (auth()->user()->hasRole('karyawan') || auth()->user()->salary_type === 'bulanan')) {
            return redirect()->route('admin.hrd.attendance.index', ['user_id' => auth()->id()]);
        }

        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        $today = Carbon::today();
        $monthStart = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Stats - Only approved ritase
        $tonaseHariIni = Ritase::where('is_approved', 1)->whereDate('waktu_masuk', $today)->sum('berat_netto');
        $tonaseBulanIni = Ritase::where('is_approved', 1)->whereBetween('waktu_masuk', [$monthStart, $monthEnd])->sum('berat_netto');
        
        $jumlahRitaseHariIni = Ritase::where('is_approved', 1)->whereDate('waktu_masuk', $today)->count();
        $jumlahRitaseBulanIni = Ritase::where('is_approved', 1)->whereBetween('waktu_masuk', [$monthStart, $monthEnd])->count();

        // Residu dan Pilahan (Akumulasi / All-Time) - Based on approved ritase
        $tonaseAkumulasi = Ritase::where('is_approved', 1)->sum('berat_netto');
        $residuAkumulasi = PengangkutanResidu::sum('berat_netto');
        $pilahanAkumulasi = HasilPilahan::sum('tonase');

        $persenResidu = $tonaseAkumulasi > 0 
            ? ($residuAkumulasi / $tonaseAkumulasi) * 100 
            : 0;

        $kemampuanReduceKeseluruhan = $tonaseAkumulasi > 0 
            ? 100 - $persenResidu
            : 0;

        $kemampuanReducePilahan = $tonaseAkumulasi > 0 
            ? ($pilahanAkumulasi / $tonaseAkumulasi) * 100 
            : 0;

        if (!auth()->user()->hasRole('ritase_only')) {
            $pendapatanTipping = Ritase::whereDate('waktu_masuk', $today)
                ->where('is_approved', 1)
                ->where('biaya_tipping', '>', 0)
                ->sum('biaya_tipping');
            $penjualanBulanIni = \App\Models\JurnalDetail::join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
                ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('coa.tipe', 'Revenue')
                ->whereBetween('jurnal_header.tanggal', [$monthStart, $monthEnd])
                ->selectRaw('SUM(jurnal_detail.kredit) - SUM(jurnal_detail.debit) as total')
                ->value('total') ?? 0;
            
            $biayaBulanIni = \App\Models\JurnalDetail::join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
                ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('coa.tipe', 'Expense')
                ->whereBetween('jurnal_header.tanggal', [$monthStart, $monthEnd])
                ->selectRaw('SUM(jurnal_detail.debit) - SUM(jurnal_detail.kredit) as total')
                ->value('total') ?? 0;
        } else {
            $pendapatanTipping = 0;
            $penjualanBulanIni = 0;
            $biayaBulanIni = 0;
        }


        // Chart data: Daily tonnage for selected month (Single aggregated query)
        $dailyTonnageData = Ritase::where('is_approved', 1)
            ->whereBetween('waktu_masuk', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()])
            ->selectRaw('DATE(waktu_masuk) as date_str, SUM(berat_netto) as total')
            ->groupByRaw('DATE(waktu_masuk)')
            ->pluck('total', 'date_str');

        $dailyTonnage = collect();
        $daysInMonth = $monthStart->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($selectedYear, $selectedMonth, $d);
            $dateKey = $date->format('Y-m-d');
            $dailyTonnage->push([
                'date' => $date->format('d/m'),
                'tonnage' => round($dailyTonnageData->get($dateKey, 0), 2),
            ]);
        }

        // Chart data: Revenue vs Expense for 6 months ending at selected month (Aggregated queries)
        $monthlyFinancials = collect();
        if (!auth()->user()->hasRole('ritase_only')) {
            $sixMonthStart = $monthStart->copy()->subMonths(5)->startOfMonth();

            $revenueData = \App\Models\JurnalDetail::join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
                ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('coa.tipe', 'Revenue')
                ->whereBetween('jurnal_header.tanggal', [$sixMonthStart, $monthEnd])
                ->selectRaw("DATE_FORMAT(jurnal_header.tanggal, '%Y-%m') as y_m, SUM(jurnal_detail.kredit) - SUM(jurnal_detail.debit) as total")
                ->groupBy('y_m')
                ->pluck('total', 'y_m');

            $expenseData = \App\Models\JurnalDetail::join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
                ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('coa.tipe', 'Expense')
                ->whereBetween('jurnal_header.tanggal', [$sixMonthStart, $monthEnd])
                ->selectRaw("DATE_FORMAT(jurnal_header.tanggal, '%Y-%m') as y_m, SUM(jurnal_detail.debit) - SUM(jurnal_detail.kredit) as total")
                ->groupBy('y_m')
                ->pluck('total', 'y_m');

            for ($i = 5; $i >= 0; $i--) {
                $month = $monthStart->copy()->subMonths($i);
                $ymKey = $month->format('Y-m');

                $monthlyFinancials->push([
                    'month' => $month->format('M Y'),
                    'revenue' => round($revenueData->get($ymKey, 0), 0),
                    'expense' => round($expenseData->get($ymKey, 0), 0),
                ]);
            }
        }

        // Month and Year options for selector
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create()->month($m)->translatedFormat('F');
        }

        $years = [];
        $startYear = date('Y') - 5;
        $endYear = date('Y') + 1;
        for ($y = $startYear; $y <= $endYear; $y++) {
            $years[$y] = $y;
        }

        return view('admin.dashboard', compact(
            'tonaseHariIni',
            'tonaseBulanIni',
            'pendapatanTipping',
            'penjualanBulanIni',
            'biayaBulanIni',
            'jumlahRitaseHariIni',
            'jumlahRitaseBulanIni',
            'kemampuanReduceKeseluruhan',
            'kemampuanReducePilahan',
            'dailyTonnage',
            'monthlyFinancials',
            'selectedMonth',
            'selectedYear',
            'months',
            'years'
        ));
    }
}
