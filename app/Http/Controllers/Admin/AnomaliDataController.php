<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Services\DataAnomalyDetector;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class AnomaliDataController extends Controller
{
    protected DataAnomalyDetector $detector;

    public function __construct(DataAnomalyDetector $detector)
    {
        $this->detector = $detector;
    }

    /**
     * Display the anomaly audit dashboard.
     */
    public function index(Request $request)
    {
        // Check access: Anyone with operasional or laporan view permission can access
        if (
            !Gate::allows('view_ritase') &&
            !Gate::allows('view_laporan_operasional') &&
            !Gate::allows('view_tracing')
        ) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat pemeriksaan anomali data.');
        }

        $filters = [
            'module' => $request->get('module', 'all'),
            'severity' => $request->get('severity', 'all'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'search' => $request->get('search'),
        ];

        // Retrieve all unfiltered anomalies for summary statistics
        $allAnomalies = $this->detector->detectAll();

        $stats = [
            'total' => $allAnomalies->count(),
            'danger' => $allAnomalies->where('severity', 'danger')->count(),
            'warning' => $allAnomalies->where('severity', 'warning')->count(),
            'ritase' => $allAnomalies->where('module_key', 'ritase')->count(),
            'residu' => $allAnomalies->where('module_key', 'residu')->count(),
            'hasil_pilahan' => $allAnomalies->where('module_key', 'hasil_pilahan')->count(),
            'penjualan' => $allAnomalies->where('module_key', 'penjualan')->count(),
            'stok' => $allAnomalies->where('module_key', 'stok')->count(),
        ];

        // Retrieve filtered anomalies
        $filteredAnomalies = $this->detector->detectAll($filters);

        // Paginate collection manually
        $page = (int) $request->get('page', 1);
        $perPage = 20;
        $paginatedItems = $filteredAnomalies->slice(($page - 1) * $perPage, $perPage)->values();

        $anomalies = new LengthAwarePaginator(
            $paginatedItems,
            $filteredAnomalies->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.anomali.index', compact('anomalies', 'stats', 'filters'));
    }

    /**
     * Export anomaly findings to Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'module' => $request->get('module', 'all'),
            'severity' => $request->get('severity', 'all'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'search' => $request->get('search'),
        ];

        $anomalies = $this->detector->detectAll($filters);
        $filename = 'laporan_anomali_data_' . date('Y-m-d_His') . '.xls';

        return response()
            ->view('exports.anomali_excel', compact('anomalies', 'filters'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Batch auto-fill default keterangan for legacy Ritase records that are empty.
     */
    public function autoFillKeterangan(Request $request)
    {
        if (!Gate::allows('update_ritase')) {
            abort(403);
        }

        $count = Ritase::whereNull('keterangan')
            ->orWhere('keterangan', '')
            ->orWhere('keterangan', '-')
            ->update(['keterangan' => 'Diterima di TPST']);

        return redirect()->route('admin.anomali-data.index')
            ->with('success', "Berhasil mengisi keterangan default ('Diterima di TPST') pada {$count} transaksi ritase lama.");
    }
}
