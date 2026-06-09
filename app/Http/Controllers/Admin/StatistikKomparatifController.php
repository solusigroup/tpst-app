<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Models\PengangkutanResidu;
use App\Models\HasilPilahan;
use App\Models\Penjualan;
use App\Models\Klien;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StatistikKomparatifController extends Controller
{
    private function checkAccess()
    {
        if (!Gate::allows('view_laporan_keuangan') && !Gate::allows('view_laporan_operasional') && !Gate::allows('view_statistik_komparatif')) {
            abort(403, 'Anda tidak memiliki akses ke halaman analitik.');
        }
    }

    /**
     * Comparison of Waste Incoming (Ritase) vs Waste Residue (Residu Outgoing)
     */
    public function ritaseResidu(Request $request)
    {
        $this->checkAccess();
        $selectedYear = $request->get('year', date('Y'));
        $compareYear = $request->get('compare_year');

        // Monthly Ritase (berat_netto in kg) - Approved only
        $ritaseData = Ritase::where('is_approved', 1)
            ->whereYear('waktu_masuk', $selectedYear)
            ->selectRaw('MONTH(waktu_masuk) as month, SUM(berat_netto) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Monthly Residue (berat_netto in kg)
        $residuData = PengangkutanResidu::whereYear('tanggal', $selectedYear)
            ->selectRaw('MONTH(tanggal) as month, SUM(berat_netto) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Monthly Ritase (berat_netto in kg) for compare year if selected
        $compareRitaseData = collect();
        if ($compareYear && $compareYear != $selectedYear) {
            $compareRitaseData = Ritase::where('is_approved', 1)
                ->whereYear('waktu_masuk', $compareYear)
                ->selectRaw('MONTH(waktu_masuk) as month, SUM(berat_netto) as total')
                ->groupBy('month')
                ->pluck('total', 'month');
        }

        $months = $this->getMonthNames();
        $chartData = [];

        $totalRitase = 0;
        $totalResidu = 0;
        $totalCompareRitase = 0;

        for ($m = 1; $m <= 12; $m++) {
            $ritaseVal = round($ritaseData->get($m, 0), 2);
            $residuVal = round($residuData->get($m, 0), 2);
            $compareVal = $compareYear ? round($compareRitaseData->get($m, 0), 2) : 0;

            $totalRitase += $ritaseVal;
            $totalResidu += $residuVal;
            $totalCompareRitase += $compareVal;

            $reduced = max(0, $ritaseVal - $residuVal);
            $rate = $ritaseVal > 0 ? ($reduced / $ritaseVal) * 100 : 0;

            $diff = $ritaseVal - $compareVal;
            $diffPercent = $compareVal > 0 ? ($diff / $compareVal) * 100 : 0;

            $chartData[] = [
                'month_num' => $m,
                'month_name' => $months[$m],
                'ritase' => $ritaseVal,
                'residu' => $residuVal,
                'reduced' => round($reduced, 2),
                'rate' => round($rate, 1),
                'compare_ritase' => $compareVal,
                'diff' => round($diff, 2),
                'diff_percent' => round($diffPercent, 1)
            ];
        }

        $totalReduced = max(0, $totalRitase - $totalResidu);
        $avgRecoveryRate = $totalRitase > 0 ? ($totalReduced / $totalRitase) * 100 : 0;
        $totalDiff = $totalRitase - $totalCompareRitase;
        $totalDiffPercent = $totalCompareRitase > 0 ? ($totalDiff / $totalCompareRitase) * 100 : 0;

        $years = $this->getYearRange();

        return view('admin.statistik.ritase_residu', compact(
            'chartData',
            'totalRitase',
            'totalResidu',
            'totalReduced',
            'avgRecoveryRate',
            'selectedYear',
            'compareYear',
            'totalCompareRitase',
            'totalDiff',
            'totalDiffPercent',
            'years'
        ));
    }

    /**
     * Client contribution comparison
     */
    public function klien(Request $request)
    {
        $this->checkAccess();
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        $monthStart = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Query client contributions from approved ritase
        $contributions = Ritase::with('klien')
            ->where('is_approved', 1)
            ->whereBetween('waktu_masuk', [$monthStart, $monthEnd])
            ->selectRaw('klien_id, COUNT(*) as total_ritase, SUM(berat_netto) as total_berat, SUM(biaya_tipping) as total_tipping')
            ->groupBy('klien_id')
            ->get();

        $klienContributions = [];
        $totalRitase = 0;
        $totalBerat = 0;
        $totalTipping = 0;
        $maxContributorName = '-';
        $maxContributorWeight = 0;

        foreach ($contributions as $item) {
            $clientName = $item->klien ? $item->klien->nama_klien : 'Klien Tidak Dikenal';
            $weight = (float) $item->total_berat;
            $ritaseCount = (int) $item->total_ritase;
            $tipping = (float) $item->total_tipping;

            $totalRitase += $ritaseCount;
            $totalBerat += $weight;
            $totalTipping += $tipping;

            if ($weight > $maxContributorWeight) {
                $maxContributorWeight = $weight;
                $maxContributorName = $clientName;
            }

            $klienContributions[] = [
                'klien_id' => $item->klien_id,
                'name' => $clientName,
                'total_ritase' => $ritaseCount,
                'total_berat' => round($weight, 2),
                'avg_berat' => $ritaseCount > 0 ? round($weight / $ritaseCount, 2) : 0,
                'total_tipping' => $tipping
            ];
        }

        // Sort descending by weight
        usort($klienContributions, function ($a, $b) {
            return $b['total_berat'] <=> $a['total_berat'];
        });

        $months = $this->getMonthNames();
        $years = $this->getYearRange();
        $totalKlien = count($klienContributions);

        return view('admin.statistik.klien', compact(
            'klienContributions',
            'totalRitase',
            'totalBerat',
            'totalTipping',
            'totalKlien',
            'maxContributorName',
            'selectedMonth',
            'selectedYear',
            'months',
            'years'
        ));
    }

    /**
     * Financial comparison: Revenue vs Expense
     */
    public function keuangan(Request $request)
    {
        $this->checkAccess();
        $selectedYear = $request->get('year', date('Y'));

        // Query revenues grouped by month
        $revenueData = \App\Models\JurnalDetail::join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
            ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->where('jurnal_header.status', 'posted')
            ->where('coa.tipe', 'Revenue')
            ->whereYear('jurnal_header.tanggal', $selectedYear)
            ->selectRaw('MONTH(jurnal_header.tanggal) as month, SUM(jurnal_detail.kredit) - SUM(jurnal_detail.debit) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Query expenses grouped by month
        $expenseData = \App\Models\JurnalDetail::join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
            ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->where('jurnal_header.status', 'posted')
            ->where('coa.tipe', 'Expense')
            ->whereYear('jurnal_header.tanggal', $selectedYear)
            ->selectRaw('MONTH(jurnal_header.tanggal) as month, SUM(jurnal_detail.debit) - SUM(jurnal_detail.kredit) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = $this->getMonthNames();
        $chartData = [];
        $totalRevenue = 0;
        $totalExpense = 0;

        for ($m = 1; $m <= 12; $m++) {
            $revenueVal = (float) $revenueData->get($m, 0);
            $expenseVal = (float) $expenseData->get($m, 0);

            $totalRevenue += $revenueVal;
            $totalExpense += $expenseVal;

            $netProfit = $revenueVal - $expenseVal;

            $chartData[] = [
                'month_num' => $m,
                'month_name' => $months[$m],
                'revenue' => round($revenueVal, 2),
                'expense' => round($expenseVal, 2),
                'net_profit' => round($netProfit, 2),
                'status' => $netProfit >= 0 ? 'Untung' : 'Rugi'
            ];
        }

        $totalNetProfit = $totalRevenue - $totalExpense;
        $profitMargin = $totalRevenue > 0 ? ($totalNetProfit / $totalRevenue) * 100 : 0;

        $years = $this->getYearRange();

        return view('admin.statistik.keuangan', compact(
            'chartData',
            'totalRevenue',
            'totalExpense',
            'totalNetProfit',
            'profitMargin',
            'selectedYear',
            'years'
        ));
    }

    /**
     * Comparison of Waste Sorted (Hasil Pilahan) vs Waste Sold (Penjualan)
     */
    public function produksiPenjualan(Request $request)
    {
        $this->checkAccess();
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        $monthStart = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Hasil Pilahan (tonase is in kg) grouped by category
        $produksiData = HasilPilahan::whereBetween('tanggal', [$monthStart, $monthEnd])
            ->selectRaw('waste_category_id, SUM(tonase) as total')
            ->groupBy('waste_category_id')
            ->pluck('total', 'waste_category_id');

        // Penjualan (berat_kg is in kg) grouped by category
        $penjualanData = Penjualan::whereBetween('tanggal', [$monthStart, $monthEnd])
            ->selectRaw('waste_category_id, SUM(berat_kg) as total_berat, SUM(total_harga) as total_rupiah')
            ->groupBy('waste_category_id')
            ->get()
            ->keyBy('waste_category_id');

        // Fetch active waste categories
        $categories = WasteCategory::where('is_active', true)->orderBy('name')->get();

        $compareData = [];
        $totalProduksi = 0;
        $totalPenjualan = 0;
        $totalRupiahPenjualan = 0;

        foreach ($categories as $cat) {
            $prodVal = (float) $produksiData->get($cat->id, 0);
            
            $penjItem = $penjualanData->get($cat->id);
            $penjVal = $penjItem ? (float) $penjItem->total_berat : 0;
            $rpVal = $penjItem ? (float) $penjItem->total_rupiah : 0;

            $totalProduksi += $prodVal;
            $totalPenjualan += $penjVal;
            $totalRupiahPenjualan += $rpVal;

            $delta = $prodVal - $penjVal;

            $compareData[] = [
                'category_id' => $cat->id,
                'category_name' => $cat->name,
                'kategori_utama' => $cat->kategori, // e.g. Organik/Anorganik
                'produksi' => round($prodVal, 2),
                'penjualan' => round($penjVal, 2),
                'delta' => round($delta, 2),
                'nilai_jual' => $rpVal
            ];
        }

        $deltaTotal = $totalProduksi - $totalPenjualan;

        $months = $this->getMonthNames();
        $years = $this->getYearRange();

        return view('admin.statistik.produksi_penjualan', compact(
            'compareData',
            'totalProduksi',
            'totalPenjualan',
            'totalRupiahPenjualan',
            'deltaTotal',
            'selectedMonth',
            'selectedYear',
            'months',
            'years'
        ));
    }

    private function getMonthNames()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }

    private function getYearRange()
    {
        $currentYear = (int) date('Y');
        $years = [];
        for ($y = $currentYear - 5; $y <= $currentYear + 1; $y++) {
            $years[$y] = $y;
        }
        return $years;
    }
}
