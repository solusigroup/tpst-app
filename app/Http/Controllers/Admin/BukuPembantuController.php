<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BukuPembantu;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExcelExport;

class BukuPembantuController extends Controller
{
    /**
     * Display the AR Subsidiary Ledger (Buku Pembantu Piutang).
     */
    public function piutang(Request $request)
    {
        try {
            Gate::authorize('view_buku_pembantu');
            $query = BukuPembantu::with(['contactable', 'jurnalHeader'])
                ->whereNotNull('jurnal_header_id')
                ->where('tipe', 'piutang');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                      ->orWhereHasMorph('contactable', [
                        \App\Models\Klien::class, 
                        \App\Models\Vendor::class
                      ], function ($q2, $type) use ($search) {
                          if ($type === \App\Models\Klien::class) {
                              $q2->where('nama_klien', 'like', "%{$search}%");
                          } else {
                              $q2->where('nama_vendor', 'like', "%{$search}%");
                          }
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalJumlah = (clone $query)->where('status', 'pending')->selectRaw('SUM(jumlah - terbayar) as total')->value('total') ?? 0;
            $entries = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

            return view('admin.buku_pembantu.piutang', compact('entries', 'totalJumlah'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the AP / Liability Subsidiary Ledger (Buku Pembantu Utang).
     */
    public function utang(Request $request)
    {
        try {
            Gate::authorize('view_buku_pembantu');
            $query = BukuPembantu::with(['contactable', 'jurnalHeader', 'settledByJurnalHeader', 'coa'])
                ->whereNotNull('jurnal_header_id')
                ->where('tipe', 'utang');

            $liabilityCoas = \App\Models\Coa::where('tipe', 'Liability')->orderBy('kode_akun')->get();

            if ($request->filled('coa_id')) {
                $query->where('coa_id', $request->coa_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                      ->orWhereHas('coa', function ($qCoa) use ($search) {
                          $qCoa->where('kode_akun', 'like', "%{$search}%")
                               ->orWhere('nama_akun', 'like', "%{$search}%");
                      })
                      ->orWhereHasMorph('contactable', [
                        \App\Models\Klien::class, 
                        \App\Models\Vendor::class
                      ], function ($q2, $type) use ($search) {
                          if ($type === \App\Models\Klien::class) {
                              $q2->where('nama_klien', 'like', "%{$search}%");
                          } else {
                              $q2->where('nama_vendor', 'like', "%{$search}%");
                          }
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $dari = $request->input('dari', $request->input('start_date'));
            $sampai = $request->input('sampai', $request->input('end_date'));

            if (!empty($dari)) {
                $query->whereDate('tanggal', '>=', $dari);
            }
            if (!empty($sampai)) {
                $query->whereDate('tanggal', '<=', $sampai);
            }

            $totalHutangAwal = (clone $query)->sum('jumlah');
            $totalTerbayar = (clone $query)->sum('terbayar');
            $totalJumlah = (clone $query)->where('status', 'pending')->selectRaw('SUM(jumlah - terbayar) as total')->value('total') ?? 0;

            if ($request->export === 'pdf' || $request->export === 'excel') {
                $entries = $query->orderByDesc('tanggal')->get();
                $selectedCoa = $request->filled('coa_id') ? Coa::find($request->coa_id) : null;
                $data = compact(
                    'entries',
                    'liabilityCoas',
                    'selectedCoa',
                    'totalHutangAwal',
                    'totalTerbayar',
                    'totalJumlah',
                    'dari',
                    'sampai'
                );

                $filename = 'buku-pembantu-utang-' . now()->format('YmdHis');

                if ($request->export === 'pdf') {
                    $pdf = Pdf::loadView('admin.buku_pembantu.exports.utang-export', $data)
                        ->setPaper('a4', 'landscape');
                    return $pdf->download($filename . '.pdf');
                } elseif ($request->export === 'excel') {
                    return Excel::download(
                        new LaporanExcelExport('admin.buku_pembantu.exports.utang-export', $data),
                        $filename . '.xlsx'
                    );
                }
            }
            
            $entries = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

            return view('admin.buku_pembantu.utang', compact(
                'entries', 
                'liabilityCoas', 
                'totalHutangAwal', 
                'totalTerbayar', 
                'totalJumlah',
                'dari',
                'sampai'
            ));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export Buku Pembantu Utang to PDF.
     */
    public function exportUtangPdf(Request $request)
    {
        $request->merge(['export' => 'pdf']);
        return $this->utang($request);
    }

    /**
     * Export Buku Pembantu Utang to Excel.
     */
    public function exportUtangExcel(Request $request)
    {
        $request->merge(['export' => 'excel']);
        return $this->utang($request);
    }
    /**
     * Sync status for all entries.
     */
    public function syncStatus()
    {
        Gate::authorize('view_buku_pembantu');
        
        $affected = 0;
        BukuPembantu::where('status', 'pending')
            ->whereColumn('terbayar', '>=', 'jumlah')
            ->get()
            ->each(function($entry) use (&$affected) {
                $entry->save(); // This triggers the saving hook in the model
                $affected++;
            });

        return back()->with('success', "Sinkronisasi berhasil. $affected data piutang telah diperbarui ke status Lunas.");
    }
}
