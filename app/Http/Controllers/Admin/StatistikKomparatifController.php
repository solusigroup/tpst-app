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

        if ($selectedMonth === 'YTD') {
            $monthStart = Carbon::createFromDate($selectedYear, 1, 1)->startOfDay();
            if ($selectedYear == date('Y')) {
                $monthEnd = Carbon::now()->endOfDay();
            } else {
                $monthEnd = Carbon::createFromDate($selectedYear, 12, 31)->endOfDay();
            }
        } else {
            $monthStart = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
        }

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

    /**
     * Tonnage per month comparative by source (klien.jenis)
     */
    public function tonasePerSumber(Request $request)
    {
        $this->checkAccess();
        $selectedYear = $request->get('year', date('Y'));
        $compareYear = $request->get('compare_year');
        $sumber = $request->get('sumber', 'all');

        $sumberLabel = $this->getSumberLabels();

        // Build query for selected year
        $ritaseQuery = Ritase::where('is_approved', 1)
            ->whereYear('waktu_masuk', $selectedYear);

        if ($sumber !== 'all') {
            $ritaseQuery->whereHas('klien', function ($q) use ($sumber) {
                $q->where('jenis', $sumber);
            });
        }

        $ritaseData = $ritaseQuery
            ->selectRaw('MONTH(waktu_masuk) as month, SUM(berat_netto) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Build query for compare year if selected
        $compareRitaseData = collect();
        if ($compareYear && $compareYear != $selectedYear) {
            $compareQuery = Ritase::where('is_approved', 1)
                ->whereYear('waktu_masuk', $compareYear);

            if ($sumber !== 'all') {
                $compareQuery->whereHas('klien', function ($q) use ($sumber) {
                    $q->where('jenis', $sumber);
                });
            }

            $compareRitaseData = $compareQuery
                ->selectRaw('MONTH(waktu_masuk) as month, SUM(berat_netto) as total')
                ->groupBy('month')
                ->pluck('total', 'month');
        }

        $months = $this->getMonthNames();
        $chartData = [];

        $totalTonase = 0;
        $totalCompareTonase = 0;

        for ($m = 1; $m <= 12; $m++) {
            $tonaseVal = round($ritaseData->get($m, 0), 2);
            $compareVal = $compareYear ? round($compareRitaseData->get($m, 0), 2) : 0;

            $totalTonase += $tonaseVal;
            $totalCompareTonase += $compareVal;

            $tonaseTon = round($tonaseVal / 1000, 3);
            $compareTon = round($compareVal / 1000, 3);

            $diff = $tonaseVal - $compareVal;
            $diffPercent = $compareVal > 0 ? ($diff / $compareVal) * 100 : 0;

            $chartData[] = [
                'month_num' => $m,
                'month_name' => $months[$m],
                'tonase_kg' => $tonaseVal,
                'tonase_ton' => $tonaseTon,
                'compare_kg' => $compareVal,
                'compare_ton' => $compareTon,
                'diff' => round($diff, 2),
                'diff_percent' => round($diffPercent, 1),
            ];
        }

        $totalTonaseTon = round($totalTonase / 1000, 3);
        $totalCompareTon = round($totalCompareTonase / 1000, 3);
        $totalDiff = $totalTonase - $totalCompareTonase;
        $totalDiffPercent = $totalCompareTonase > 0 ? ($totalDiff / $totalCompareTonase) * 100 : 0;

        // Monthly average
        $monthsWithData = collect($chartData)->filter(fn($r) => $r['tonase_kg'] > 0)->count();
        $avgTonasePerMonth = $monthsWithData > 0 ? $totalTonase / $monthsWithData : 0;

        $years = $this->getYearRange();

        return view('admin.statistik.tonase_sumber', compact(
            'chartData',
            'totalTonase',
            'totalTonaseTon',
            'totalCompareTonase',
            'totalCompareTon',
            'totalDiff',
            'totalDiffPercent',
            'avgTonasePerMonth',
            'selectedYear',
            'compareYear',
            'sumber',
            'sumberLabel',
            'years'
        ));
    }

    /**
     * Export Tonase per Sumber to PDF
     */
    public function exportTonasePdf(Request $request)
    {
        $this->checkAccess();
        $data = $this->getTonaseExportData($request);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.statistik.exports.tonase_sumber_export', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Tonase_Per_Sumber_' . $data['selectedYear'] . '_' . $data['sumber'] . '.pdf');
    }

    /**
     * Export Tonase per Sumber to Excel
     */
    public function exportTonaseExcel(Request $request)
    {
        $this->checkAccess();
        $data = $this->getTonaseExportData($request);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanExcelExport('admin.statistik.exports.tonase_sumber_export', $data),
            'Tonase_Per_Sumber_' . $data['selectedYear'] . '_' . $data['sumber'] . '_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Get tonase export data (shared between PDF and Excel)
     */
    private function getTonaseExportData(Request $request): array
    {
        $selectedYear = $request->get('year', date('Y'));
        $compareYear = $request->get('compare_year');
        $sumber = $request->get('sumber', 'all');

        $sumberLabel = $this->getSumberLabels();

        $ritaseQuery = Ritase::where('is_approved', 1)
            ->whereYear('waktu_masuk', $selectedYear);

        if ($sumber !== 'all') {
            $ritaseQuery->whereHas('klien', function ($q) use ($sumber) {
                $q->where('jenis', $sumber);
            });
        }

        $ritaseData = $ritaseQuery
            ->selectRaw('MONTH(waktu_masuk) as month, SUM(berat_netto) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $compareRitaseData = collect();
        if ($compareYear && $compareYear != $selectedYear) {
            $compareQuery = Ritase::where('is_approved', 1)
                ->whereYear('waktu_masuk', $compareYear);

            if ($sumber !== 'all') {
                $compareQuery->whereHas('klien', function ($q) use ($sumber) {
                    $q->where('jenis', $sumber);
                });
            }

            $compareRitaseData = $compareQuery
                ->selectRaw('MONTH(waktu_masuk) as month, SUM(berat_netto) as total')
                ->groupBy('month')
                ->pluck('total', 'month');
        }

        $months = $this->getMonthNames();
        $chartData = [];
        $totalTonase = 0;
        $totalCompareTonase = 0;

        for ($m = 1; $m <= 12; $m++) {
            $tonaseVal = round($ritaseData->get($m, 0), 2);
            $compareVal = $compareYear ? round($compareRitaseData->get($m, 0), 2) : 0;

            $totalTonase += $tonaseVal;
            $totalCompareTonase += $compareVal;

            $diff = $tonaseVal - $compareVal;
            $diffPercent = $compareVal > 0 ? ($diff / $compareVal) * 100 : 0;

            $chartData[] = [
                'month_num' => $m,
                'month_name' => $months[$m],
                'tonase_kg' => $tonaseVal,
                'tonase_ton' => round($tonaseVal / 1000, 3),
                'compare_kg' => $compareVal,
                'compare_ton' => round($compareVal / 1000, 3),
                'diff' => round($diff, 2),
                'diff_percent' => round($diffPercent, 1),
            ];
        }

        $totalDiff = $totalTonase - $totalCompareTonase;
        $totalDiffPercent = $totalCompareTonase > 0 ? ($totalDiff / $totalCompareTonase) * 100 : 0;

        return compact(
            'chartData',
            'totalTonase',
            'totalCompareTonase',
            'totalDiff',
            'totalDiffPercent',
            'selectedYear',
            'compareYear',
            'sumber',
            'sumberLabel'
        );
    }

    /**
     * Rekap Hasil Pilahan: daily / weekly / monthly / yearly
     */
    public function hasilPilahan(Request $request)
    {
        $this->checkAccess();

        $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));
        $selectedCategory = $request->get('waste_category_id', 'all');

        // Build base query
        $query = HasilPilahan::query();

        // Optional filter by waste category (jenis sampah)
        if ($selectedCategory !== 'all') {
            $query->where('waste_category_id', $selectedCategory);
        }

        // Determine grouping and date constraints based on period
        switch ($period) {
            case 'daily':
                $query->whereYear('tanggal', $selectedYear)
                      ->whereMonth('tanggal', $selectedMonth);
                $groupSelect = 'DAY(tanggal) as period_key';
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int) $selectedMonth, (int) $selectedYear);
                $periodRange = range(1, $daysInMonth);
                $periodLabels = [];
                foreach ($periodRange as $d) {
                    $periodLabels[$d] = $d;
                }
                break;

            case 'weekly':
                $query->whereYear('tanggal', $selectedYear);
                $groupSelect = 'WEEK(tanggal, 1) as period_key';
                // ISO weeks 1-53
                $periodRange = range(1, 53);
                $periodLabels = [];
                foreach ($periodRange as $w) {
                    $periodLabels[$w] = 'Minggu ' . $w;
                }
                break;

            case 'yearly':
                // No date filter — all years
                $groupSelect = 'YEAR(tanggal) as period_key';
                $yearRange = $this->getYearRange();
                $periodRange = array_keys($yearRange);
                $periodLabels = [];
                foreach ($periodRange as $y) {
                    $periodLabels[$y] = (string) $y;
                }
                break;

            case 'monthly':
            default:
                $period = 'monthly';
                $query->whereYear('tanggal', $selectedYear);
                $groupSelect = 'MONTH(tanggal) as period_key';
                $periodRange = range(1, 12);
                $months = $this->getMonthNames();
                $periodLabels = $months;
                break;
        }

        // Main aggregation: per period_key per kategori
        $rawData = (clone $query)
            ->selectRaw($groupSelect . ', kategori, SUM(tonase) as total_tonase, SUM(jml_bal) as total_bal')
            ->groupBy('period_key', 'kategori')
            ->get();

        // Also get breakdown by jenis (waste category name) for the detail table
        $jenisData = (clone $query)
            ->selectRaw($groupSelect . ', jenis, SUM(tonase) as total_tonase, SUM(jml_bal) as total_bal')
            ->groupBy('period_key', 'jenis')
            ->get();

        // Kategori list
        $kategoriList = ['Organik', 'Anorganik', 'B3', 'Residu'];

        // Pivot data: period_key => kategori => values
        $pivoted = [];
        foreach ($rawData as $row) {
            $key = $row->period_key;
            if (!isset($pivoted[$key])) {
                $pivoted[$key] = ['total_tonase' => 0, 'total_bal' => 0];
                foreach ($kategoriList as $k) {
                    $pivoted[$key][$k] = ['tonase' => 0, 'bal' => 0];
                }
            }
            $kat = $row->kategori;
            if (in_array($kat, $kategoriList)) {
                $pivoted[$key][$kat]['tonase'] = (float) $row->total_tonase;
                $pivoted[$key][$kat]['bal'] = (int) $row->total_bal;
            }
            $pivoted[$key]['total_tonase'] += (float) $row->total_tonase;
            $pivoted[$key]['total_bal'] += (int) $row->total_bal;
        }

        // Pivot jenis data: period_key => jenis => values
        $jenisPivoted = [];
        $allJenis = [];
        foreach ($jenisData as $row) {
            $key = $row->period_key;
            $jenis = $row->jenis;
            if (!in_array($jenis, $allJenis)) {
                $allJenis[] = $jenis;
            }
            if (!isset($jenisPivoted[$key])) {
                $jenisPivoted[$key] = [];
            }
            $jenisPivoted[$key][$jenis] = [
                'tonase' => (float) $row->total_tonase,
                'bal' => (int) $row->total_bal,
            ];
        }
        sort($allJenis);

        // Build chart data arrays
        $chartData = [];
        $totalTonase = 0;
        $totalBal = 0;
        $periodsWithData = 0;

        foreach ($periodRange as $pk) {
            $entry = [
                'period_key' => $pk,
                'period_label' => $periodLabels[$pk] ?? $pk,
            ];
            $rowTotal = 0;
            foreach ($kategoriList as $kat) {
                $val = $pivoted[$pk][$kat]['tonase'] ?? 0;
                $entry[$kat] = round($val, 2);
                $rowTotal += $val;
            }
            $entry['total'] = round($rowTotal, 2);
            $entry['bal'] = $pivoted[$pk]['total_bal'] ?? 0;

            // Jenis breakdown for this period
            $jenisBreakdown = [];
            foreach ($allJenis as $j) {
                $jenisBreakdown[$j] = [
                    'tonase' => round($jenisPivoted[$pk][$j]['tonase'] ?? 0, 2),
                    'bal' => $jenisPivoted[$pk][$j]['bal'] ?? 0,
                ];
            }
            $entry['jenis_breakdown'] = $jenisBreakdown;

            $totalTonase += $rowTotal;
            $totalBal += $entry['bal'];
            if ($rowTotal > 0) {
                $periodsWithData++;
            }

            $chartData[] = $entry;
        }

        // For weekly mode, trim weeks with no data at the end
        if ($period === 'weekly') {
            $chartData = array_values(array_filter($chartData, function ($item) use ($pivoted) {
                return isset($pivoted[$item['period_key']]);
            }));
            // If no data at all, keep at least one placeholder
            if (empty($chartData)) {
                $chartData[] = [
                    'period_key' => 1,
                    'period_label' => 'Minggu 1',
                    'Organik' => 0, 'Anorganik' => 0, 'B3' => 0, 'Residu' => 0,
                    'total' => 0, 'bal' => 0, 'jenis_breakdown' => [],
                ];
            }
        }

        $avgPerPeriod = $periodsWithData > 0 ? $totalTonase / $periodsWithData : 0;

        // Waste categories for filter dropdown
        $wasteCategories = WasteCategory::where('is_active', true)->orderBy('name')->get();

        $months = $this->getMonthNames();
        $years = $this->getYearRange();

        return view('admin.statistik.hasil_pilahan', compact(
            'chartData',
            'kategoriList',
            'allJenis',
            'totalTonase',
            'totalBal',
            'avgPerPeriod',
            'periodsWithData',
            'period',
            'selectedMonth',
            'selectedYear',
            'selectedCategory',
            'wasteCategories',
            'months',
            'years'
        ));
    }

    private function getSumberLabels(): array
    {
        return [
            'all' => 'Semua Sumber',
            'DLH' => 'Dinas Lingkungan Hidup',
            'Swasta' => 'Swasta',
            'Offtaker' => 'Offtaker',
            'Internal' => 'Internal',
        ];
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
