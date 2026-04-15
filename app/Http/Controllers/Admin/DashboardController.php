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
    public function index()
    {
        // If the logged-in user is a regular employee, redirect to their attendance recap
        if (auth()->check() && (auth()->user()->hasRole('karyawan') || auth()->user()->salary_type === 'bulanan')) {
            return redirect()->route('admin.hrd.attendance.index', ['user_id' => auth()->id()]);
        }

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // Stats
        $tonaseHariIni = Ritase::whereDate('waktu_masuk', $today)->sum('berat_netto');
        $tonaseBulanIni = Ritase::whereBetween('waktu_masuk', [$monthStart, $monthEnd])->sum('berat_netto');
        
        $jumlahRitaseHariIni = Ritase::whereDate('waktu_masuk', $today)->count();
        $jumlahRitaseBulanIni = Ritase::whereBetween('waktu_masuk', [$monthStart, $monthEnd])->count();

        // Residu dan Pilahan (Akumulasi / All-Time)
        $tonaseAkumulasi = Ritase::sum('berat_netto');
        $residuAkumulasi = PengangkutanResidu::sum('berat_netto');
        $pilahanAkumulasi = HasilPilahan::sum('tonase');

        $kemampuanReduceKeseluruhan = $tonaseAkumulasi > 0 
            ? (($tonaseAkumulasi - $residuAkumulasi) / $tonaseAkumulasi) * 100 
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


        // Chart data: Daily tonnage for last 30 days
        $dailyTonnage = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $tonnage = Ritase::whereDate('waktu_masuk', $date)->sum('berat_netto');
            $dailyTonnage->push([
                'date' => $date->format('d/m'),
                'tonnage' => round($tonnage, 2),
            ]);
        }

        // Chart data: Revenue for last 6 months
        $monthlyRevenue = collect();
        if (!auth()->user()->hasRole('ritase_only')) {
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $revenue = Penjualan::whereYear('tanggal', $month->year)
                    ->whereMonth('tanggal', $month->month)
                    ->sum('total_harga');
                $monthlyRevenue->push([
                    'month' => $month->format('M Y'),
                    'revenue' => round($revenue, 0),
                ]);
            }
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
            'monthlyRevenue'
        ));
    }
}
