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
                $query->whereHasMorph('contactable', ['App\Models\Klien', 'App\Models\Vendor'], function ($q) use ($search) {
                    $q->where('nama_klien', 'like', "%{$search}%")
                      ->orWhere('nama_vendor', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalJumlah = (clone $query)->sum('jumlah');
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
                $query->whereHasMorph('contactable', ['App\Models\Klien', 'App\Models\Vendor'], function ($q) use ($search) {
                    $q->where('nama_klien', 'like', "%{$search}%")
                      ->orWhere('nama_vendor', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalJumlah = (clone $query)->sum('jumlah');
            $entries = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

            return view('admin.buku_pembantu.utang', compact('entries', 'totalJumlah'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
