<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BukuPembantu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
     * Display the AP Subsidiary Ledger (Buku Pembantu Utang).
     */
    public function utang(Request $request)
    {
        try {
            Gate::authorize('view_buku_pembantu');
            $query = BukuPembantu::with(['contactable', 'jurnalHeader'])
                ->whereNotNull('jurnal_header_id')
                ->where('tipe', 'utang');

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

            return view('admin.buku_pembantu.utang', compact('entries', 'totalJumlah'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
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
