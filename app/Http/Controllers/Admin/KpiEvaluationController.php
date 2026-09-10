<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiEvaluation;
use App\Models\User;
use App\Services\KpiCalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KpiEvaluationController extends Controller
{
    protected $kpiService;

    public function __construct(KpiCalculationService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Daftar Riwayat Evaluasi KPI & Dokumen Pengesahan
     */
    public function index(Request $request)
    {
        $tipe = $request->get('periode_tipe', 'mingguan');
        $status = $request->get('status');

        $query = KpiEvaluation::with('user', 'approvedBy')
            ->orderBy('periode_mulai', 'desc');

        if ($tipe) {
            $query->where('periode_tipe', $tipe);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $evaluations = $query->paginate(15);

        return view('admin.kpi.evaluasi.index', compact('evaluations', 'tipe', 'status'));
    }

    /**
     * Generate kalkulasi evaluasi otomatis (Draft)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'periode_tipe' => 'required|in:mingguan,bulanan',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'target_tipe' => 'required|in:global,site_manager,admin_commercial,equipment_logistik,operasional_qc',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $tipe = $request->periode_tipe;
        $start = $request->periode_mulai;
        $end = $request->periode_selesai;
        $target = $request->target_tipe;

        if ($target === 'global') {
            $result = $this->kpiService->calculateGlobalKpi($start, $end, $tenantId);
            $jabatan = 'global';
            $userId = null;
        } else {
            $result = $this->kpiService->calculateEmployeeKpi($target, $start, $end, $tenantId);
            $jabatan = $target;
            // Temukan user jika ada
            $user = User::where('position', 'LIKE', '%' . str_replace('_', ' ', $target) . '%')->first();
            $userId = $user ? $user->id : null;
        }

        $evaluation = KpiEvaluation::create([
            'periode_tipe' => $tipe,
            'periode_mulai' => $start,
            'periode_selesai' => $end,
            'target_tipe' => $target === 'global' ? 'global' : 'individual',
            'user_id' => $userId,
            'jabatan' => $jabatan,
            'total_skor' => $result['total_skor'],
            'predikat' => $result['predikat'],
            'rincian_kpi' => $result['kpi_items'],
            'status' => 'draft',
            'supervisor_institution' => 'PT Pinastika Bhakti Semesta',
        ]);

        return redirect()->route('admin.kpi.evaluasi.show', $evaluation)
            ->with('success', 'Draft evaluasi KPI berhasil digenerate.');
    }

    /**
     * Tampilan Rincian & Lembar Evaluasi / Verifikasi Supervisi
     */
    public function show(KpiEvaluation $kpiEvaluation)
    {
        $kpiEvaluation->load('user', 'approvedBy');

        return view('admin.kpi.evaluasi.show', compact('kpiEvaluation'));
    }

    /**
     * Pengajuan Draft ke Supervisi PT PBS
     */
    public function submit(KpiEvaluation $kpiEvaluation)
    {
        $kpiEvaluation->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Evaluasi KPI telah diajukan ke Supervisi PT PBS.');
    }

    /**
     * Approval / Verifikasi Resmi oleh Supervisi PT PBS
     */
    public function approve(Request $request, KpiEvaluation $kpiEvaluation)
    {
        $request->validate([
            'supervisor_name' => 'required|string',
            'supervisor_notes' => 'nullable|string',
            'action' => 'required|in:approve,revision',
        ]);

        if ($request->action === 'revision') {
            $kpiEvaluation->update([
                'status' => 'revision',
                'supervisor_notes' => $request->supervisor_notes,
            ]);
            return redirect()->back()->with('warning', 'Evaluasi KPI dikembalikan untuk revisi dengan catatan supervisi.');
        }

        $kpiEvaluation->update([
            'status' => 'approved',
            'approved_by_id' => auth()->id(),
            'approved_by_name' => $request->supervisor_name,
            'approved_at' => now(),
            'supervisor_notes' => $request->supervisor_notes,
            'supervisor_institution' => 'PT Pinastika Bhakti Semesta',
        ]);

        return redirect()->back()->with('success', 'Evaluasi KPI telah disetujui resmi oleh Supervisi PT PBS.');
    }

    /**
     * Cetak PDF Laporan KPI Resmi
     */
    public function exportPdf(KpiEvaluation $kpiEvaluation)
    {
        $kpiEvaluation->load('user', 'approvedBy');

        $pdf = Pdf::loadView('admin.kpi.evaluasi.pdf-rekap', compact('kpiEvaluation'))
            ->setPaper('a4', 'portrait');

        $filename = 'KPI_' . strtoupper($kpiEvaluation->jabatan ?? 'GLOBAL') . '_' . 
            $kpiEvaluation->periode_mulai->format('Ymd') . '_' . $kpiEvaluation->periode_selesai->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
