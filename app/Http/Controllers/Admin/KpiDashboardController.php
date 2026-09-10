<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiDailyChecklist;
use App\Models\KpiMachineActivityLog;
use App\Models\KpiStakeholderComplaint;
use App\Models\Machine;
use App\Models\Ritase;
use App\Models\Invoice;
use App\Models\Penjualan;
use App\Models\HasilPilahan;
use App\Models\ProduksiHarian;
use App\Services\KpiCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KpiDashboardController extends Controller
{
    protected $kpiService;

    public function __construct(KpiCalculationService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Parsing filter periode (Harian, Mingguan, Bulanan, Custom)
     */
    protected function getPeriod(Request $request)
    {
        $filter = $request->get('filter', 'mingguan');
        $today = Carbon::today();

        switch ($filter) {
            case 'harian':
                $startDate = $request->get('start_date', $today->toDateString());
                $endDate = $startDate;
                break;
            case 'bulanan':
                $bulan = $request->get('bulan', $today->month);
                $tahun = $request->get('tahun', $today->year);
                $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
                $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();
                break;
            case 'custom':
                $startDate = $request->get('start_date', $today->copy()->subDays(6)->toDateString());
                $endDate = $request->get('end_date', $today->toDateString());
                break;
            case 'mingguan':
            default:
                // Default 7 hari terakhir atau minggu berjalan
                $startDate = $request->get('start_date', $today->copy()->startOfWeek()->toDateString());
                $endDate = $request->get('end_date', $today->copy()->endOfWeek()->toDateString());
                break;
        }

        return [$startDate, $endDate, $filter];
    }

    /**
     * 1. Dashboard Site Manager (Budi Sucahyo)
     */
    public function siteManager(Request $request)
    {
        list($startDate, $endDate, $filter) = $this->getPeriod($request);
        $tenantId = auth()->user()->tenant_id;

        $globalKpi = $this->kpiService->calculateGlobalKpi($startDate, $endDate, $tenantId);
        $budiKpi = $this->kpiService->calculateEmployeeKpi('site_manager', $startDate, $endDate, $tenantId);
        $nitaKpi = $this->kpiService->calculateEmployeeKpi('admin_commercial', $startDate, $endDate, $tenantId);
        $agungKpi = $this->kpiService->calculateEmployeeKpi('equipment_logistik', $startDate, $endDate, $tenantId);
        $anaKpi = $this->kpiService->calculateEmployeeKpi('operasional_qc', $startDate, $endDate, $tenantId);

        // Riwayat keluhan stakeholder terkini
        $complaints = KpiStakeholderComplaint::whereBetween('tanggal', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        // Foto bukti kebersihan area bongkar terkini
        $latestProof = KpiDailyChecklist::whereNotNull('foto_bukti')->latest('tanggal')->first();

        return view('admin.kpi.dashboard-site-manager', compact(
            'startDate', 'endDate', 'filter',
            'globalKpi', 'budiKpi', 'nitaKpi', 'agungKpi', 'anaKpi', 'complaints', 'latestProof'
        ));
    }

    /**
     * 2. Dashboard Operasional (Ana Maria & Agung)
     */
    public function operasional(Request $request)
    {
        list($startDate, $endDate, $filter) = $this->getPeriod($request);
        $tenantId = auth()->user()->tenant_id;

        $globalKpi = $this->kpiService->calculateGlobalKpi($startDate, $endDate, $tenantId);
        $anaKpi = $this->kpiService->calculateEmployeeKpi('operasional_qc', $startDate, $endDate, $tenantId);
        $agungKpi = $this->kpiService->calculateEmployeeKpi('equipment_logistik', $startDate, $endDate, $tenantId);

        // Checklist kebersihan harian terkini
        $checklists = KpiDailyChecklist::whereBetween('tanggal', [$startDate, $endDate])
            ->latest('tanggal')
            ->take(10)
            ->get();

        // Data tonase harian untuk chart
        $dailyTonnage = Ritase::whereBetween('waktu_masuk', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->selectRaw('DATE(waktu_masuk) as tgl, SUM(berat_netto)/1000 as tonase, COUNT(*) as ritase_count')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        return view('admin.kpi.dashboard-operasional', compact(
            'startDate', 'endDate', 'filter',
            'globalKpi', 'anaKpi', 'agungKpi', 'checklists', 'dailyTonnage'
        ));
    }

    /**
     * 3. Dashboard Mesin & Alat Berat (Wheel Loader, RDF, Conveyor)
     */
    public function mesin(Request $request)
    {
        list($startDate, $endDate, $filter) = $this->getPeriod($request);
        $tenantId = auth()->user()->tenant_id;

        $globalKpi = $this->kpiService->calculateGlobalKpi($startDate, $endDate, $tenantId);
        $agungKpi = $this->kpiService->calculateEmployeeKpi('equipment_logistik', $startDate, $endDate, $tenantId);

        // Daftar aktivitas & jam operasional mesin
        $machineLogs = KpiMachineActivityLog::whereBetween('tanggal', [$startDate, $endDate])
            ->latest('tanggal')
            ->get();

        // Kesiapan per unit alat
        $unitSummary = KpiMachineActivityLog::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('nama_alat, SUM(jam_operasi) as total_operasi, SUM(jam_downtime) as total_downtime, SUM(bbm_liter) as total_bbm')
            ->groupBy('nama_alat')
            ->get();

        $machines = Machine::all();

        return view('admin.kpi.dashboard-mesin', compact(
            'startDate', 'endDate', 'filter',
            'globalKpi', 'agungKpi', 'machineLogs', 'unitSummary', 'machines'
        ));
    }

    /**
     * 4. Dashboard Revenue & Tipping Fee (Nita Khoirunisa)
     */
    public function revenue(Request $request)
    {
        list($startDate, $endDate, $filter) = $this->getPeriod($request);
        $tenantId = auth()->user()->tenant_id;

        $globalKpi = $this->kpiService->calculateGlobalKpi($startDate, $endDate, $tenantId);
        $nitaKpi = $this->kpiService->calculateEmployeeKpi('admin_commercial', $startDate, $endDate, $tenantId);

        // Rincian penagihan & invoice swasta
        $invoices = Invoice::whereBetween('tanggal_invoice', [$startDate, $endDate])
            ->with('klien')
            ->latest('tanggal_invoice')
            ->get();

        // Penjualan produk hasil pilahan (RDF, plastik, dll)
        $penjualan = Penjualan::whereBetween('tanggal', [$startDate, $endDate])
            ->with('wasteCategory', 'klien')
            ->latest('tanggal')
            ->get();

        $totalPenjualanProduk = $penjualan->sum('total_harga');

        return view('admin.kpi.dashboard-revenue', compact(
            'startDate', 'endDate', 'filter',
            'globalKpi', 'nitaKpi', 'invoices', 'penjualan', 'totalPenjualanProduk'
        ));
    }

    /**
     * 5. Dashboard Pemilah & Material Recovery (12 Tenaga Pemilah)
     */
    public function pemilah(Request $request)
    {
        list($startDate, $endDate, $filter) = $this->getPeriod($request);
        $tenantId = auth()->user()->tenant_id;

        $leaderboard = $this->kpiService->calculatePemilahData($startDate, $endDate, $tenantId);

        // Total akumulasi sortir
        $totalKgSemua = array_sum(array_column($leaderboard, 'total_kg'));
        $totalNilaiSemua = array_sum(array_column($leaderboard, 'nilai_ekonomi'));

        // Komposisi material sortir
        $hasilPilahanList = HasilPilahan::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, SUM(tonase) as total_ton, SUM(jml_bal) as total_bal')
            ->groupBy('kategori')
            ->get();

        return view('admin.kpi.dashboard-pemilah', compact(
            'startDate', 'endDate', 'filter',
            'leaderboard', 'totalKgSemua', 'totalNilaiSemua', 'hasilPilahanList'
        ));
    }
}
