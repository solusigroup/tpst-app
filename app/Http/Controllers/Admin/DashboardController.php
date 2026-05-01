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

        // Stats
        $tonaseHariIni = Ritase::whereDate('waktu_masuk', $today)->sum('berat_netto');
        $tonaseBulanIni = Ritase::whereBetween('waktu_masuk', [$monthStart, $monthEnd])->sum('berat_netto');
        
        $jumlahRitaseHariIni = Ritase::whereDate('waktu_masuk', $today)->count();
        $jumlahRitaseBulanIni = Ritase::whereBetween('waktu_masuk', [$monthStart, $monthEnd])->count();

        // Residu dan Pilahan (Akumulasi / All-Time)
        $tonaseAkumulasi = Ritase::sum('berat_netto');
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
                ->where('biaya_tipping', '>', 0)
                ->sum('biaya_tipping');
            $penjualanBulanIni = Penjualan::whereBetween('tanggal', [$monthStart, $monthEnd])
                ->sum('total_harga');
            
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


        // Chart data: Daily tonnage for selected month
        $dailyTonnage = collect();
        $daysInMonth = $monthStart->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($selectedYear, $selectedMonth, $d);
            $tonnage = Ritase::whereDate('waktu_masuk', $date)->sum('berat_netto');
            $dailyTonnage->push([
                'date' => $date->format('d/m'),
                'tonnage' => round($tonnage, 2),
            ]);
        }

        // Chart data: Revenue vs Expense for 6 months ending at selected month
        $monthlyFinancials = collect();
        if (!auth()->user()->hasRole('ritase_only')) {
            for ($i = 5; $i >= 0; $i--) {
                $month = $monthStart->copy()->subMonths($i);
                $mStart = $month->copy()->startOfMonth();
                $mEnd = $month->copy()->endOfMonth();

                $revenue = Penjualan::whereYear('tanggal', $month->year)
                    ->whereMonth('tanggal', $month->month)
                    ->sum('total_harga');
                
                $expense = \App\Models\JurnalDetail::join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
                    ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                    ->where('jurnal_header.status', 'posted')
                    ->where('coa.tipe', 'Expense')
                    ->whereBetween('jurnal_header.tanggal', [$mStart, $mEnd])
                    ->selectRaw('SUM(jurnal_detail.debit) - SUM(jurnal_detail.kredit) as total')
                    ->value('total') ?? 0;

                $monthlyFinancials->push([
                    'month' => $month->format('M Y'),
                    'revenue' => round($revenue, 0),
                    'expense' => round($expense, 0),
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
