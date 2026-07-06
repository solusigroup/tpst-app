<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Models\Armada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RitaseDlhController extends Controller
{
    /**
     * Build the base query for DLH ritase (only approved, only DLH-type clients).
     */
    private function baseQuery(Request $request)
    {
        $query = Ritase::with(['armada', 'klien', 'invoice'])
            ->where('is_approved', true)
            ->whereHas('klien', fn ($q) => $q->where('jenis', 'DLH'));

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_masuk', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_masuk', '<=', $request->end_date);
        }

        // Search by ticket number
        if ($request->filled('search')) {
            $searchValue = $request->search;
            $query->where(function ($q) use ($searchValue) {
                $q->where('nomor_tiket', 'like', '%' . $searchValue . '%')
                  ->orWhere('tiket', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('klien', fn ($qk) => $qk->where('nama_klien', 'like', '%' . $searchValue . '%'));
            });
        }

        // Filter by armada
        if ($request->filled('armada_id')) {
            $query->where('armada_id', $request->armada_id);
        }

        return $query;
    }

    /**
     * Ritase DLH yang disetujui (Approved) tapi belum dibayar (invoice bukan Paid).
     */
    public function approved(Request $request)
    {
        Gate::authorize('view_ritase');

        $query = $this->baseQuery($request)
            ->where(function ($q) {
                $q->whereNull('status_invoice')
                  ->orWhere('status_invoice', '!=', 'Paid');
            });

        $totalBeratNetto = (clone $query)->sum('berat_netto');
        $totalBiayaTipping = (clone $query)->sum('biaya_tipping');
        $totalCount = (clone $query)->count();
        $ritase = $query->orderByDesc('waktu_masuk')->paginate(15)->withQueryString();
        $armadas = Armada::orderBy('plat_nomor')->get();

        return view('admin.ritase_dlh.approved', compact(
            'ritase', 'totalBeratNetto', 'totalBiayaTipping', 'totalCount', 'armadas'
        ));
    }

    /**
     * Ritase DLH yang sudah dibayar (Invoice status = Paid).
     */
    public function paid(Request $request)
    {
        Gate::authorize('view_ritase');

        $query = $this->baseQuery($request)
            ->where('status_invoice', 'Paid');

        $totalBeratNetto = (clone $query)->sum('berat_netto');
        $totalBiayaTipping = (clone $query)->sum('biaya_tipping');
        $totalCount = (clone $query)->count();
        $ritase = $query->orderByDesc('waktu_masuk')->paginate(15)->withQueryString();
        $armadas = Armada::orderBy('plat_nomor')->get();

        return view('admin.ritase_dlh.paid', compact(
            'ritase', 'totalBeratNetto', 'totalBiayaTipping', 'totalCount', 'armadas'
        ));
    }

    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request)
    {
        Gate::authorize('view_ritase');

        $type = $request->get('type', 'approved');

        $query = $this->baseQuery($request);
        if ($type === 'paid') {
            $query->where('status_invoice', 'Paid');
        } else {
            $query->where(function ($q) {
                $q->whereNull('status_invoice')
                  ->orWhere('status_invoice', '!=', 'Paid');
            });
        }

        $ritase = $query->orderByDesc('waktu_masuk')->get();
        $totalBeratNetto = $ritase->sum('berat_netto');
        $totalBiayaTipping = $ritase->sum('biaya_tipping');

        $label = $type === 'paid' ? 'Dibayar' : 'Disetujui';
        $filename = 'Ritase_DLH_' . $label . '_' . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanExcelExport('admin.ritase_dlh.exports.export-excel', [
                'ritase' => $ritase,
                'totalBeratNetto' => $totalBeratNetto,
                'totalBiayaTipping' => $totalBiayaTipping,
                'label' => $label,
            ]),
            $filename
        );
    }

    /**
     * Export to PDF with 3 signature blocks (DLH, Manajer, Admin).
     */
    public function exportPdf(Request $request)
    {
        Gate::authorize('view_ritase');

        $type = $request->get('type', 'approved');

        $query = $this->baseQuery($request);
        if ($type === 'paid') {
            $query->where('status_invoice', 'Paid');
        } else {
            $query->where(function ($q) {
                $q->whereNull('status_invoice')
                  ->orWhere('status_invoice', '!=', 'Paid');
            });
        }

        $ritase = $query->orderByDesc('waktu_masuk')->get();
        $totalBeratNetto = $ritase->sum('berat_netto');
        $totalBiayaTipping = $ritase->sum('biaya_tipping');

        $label = $type === 'paid' ? 'Dibayar' : 'Disetujui';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ritase_dlh.exports.export-pdf', compact(
            'ritase', 'totalBeratNetto', 'totalBiayaTipping', 'label'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream('Ritase_DLH_' . $label . '_' . date('Ymd_His') . '.pdf');
    }
}
