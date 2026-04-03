<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Models\Penjualan;
use App\Models\HasilPilahan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        if (!auth()->user()->hasRole('ritase_only')) {
            $pendapatanTipping = Ritase::whereDate('waktu_masuk', $today)
                ->where('biaya_tipping', '>', 0)
                ->sum('biaya_tipping');
            $penjualanBulanIni = Penjualan::whereBetween('tanggal', [$monthStart, $monthEnd])
                ->sum('total_harga');
        } else {
            $pendapatanTipping = 0;
            $penjualanBulanIni = 0;
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
            'jumlahRitaseHariIni',
            'jumlahRitaseBulanIni',
            'dailyTonnage',
            'monthlyRevenue'
        ));
    }
}
