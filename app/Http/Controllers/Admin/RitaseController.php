<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Models\Armada;
use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class RitaseController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_ritase');
        $query = Ritase::with(['armada', 'klien']);

        if ($request->filled('search')) {
            $searchBy = $request->search_by ?? 'tiket';
            $searchValue = $request->search;

            if ($searchBy == 'armada') {
                $query->whereHas('armada', function($q) use ($searchValue) {
                    $q->where('plat_nomor', 'like', '%' . $searchValue . '%')
                      ->orWhere('nama_sopir', 'like', '%' . $searchValue . '%');
                });
            } elseif ($searchBy == 'klien') {
                $query->whereHas('klien', function($q) use ($searchValue) {
                    $q->where('nama_klien', 'like', '%' . $searchValue . '%');
                });
            } elseif ($searchBy == 'status_invoice') {
                $query->where('status_invoice', 'like', '%' . $searchValue . '%');
            } else {
                $query->where(function($q) use ($searchValue) {
                    $q->where('nomor_tiket', 'like', '%' . $searchValue . '%')
                      ->orWhere('tiket', 'like', '%' . $searchValue . '%');
                });
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('waktu_masuk', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('waktu_masuk', '<=', $request->end_date);
        }

        $totalBeratNetto = (clone $query)->sum('berat_netto');
        $ritase = $query->orderByDesc('waktu_masuk')->paginate(15)->withQueryString();

        return view('admin.ritase.index', compact('ritase', 'totalBeratNetto'));
    }

    public function exportRekap(Request $request)
    {
        Gate::authorize('view_ritase');
        $query = Ritase::with(['armada', 'klien']);

        if ($request->filled('search')) {
            $searchBy = $request->search_by ?? 'tiket';
            $searchValue = $request->search;

            if ($searchBy == 'armada') {
                $query->whereHas('armada', function($q) use ($searchValue) {
                    $q->where('plat_nomor', 'like', '%' . $searchValue . '%')
                      ->orWhere('nama_sopir', 'like', '%' . $searchValue . '%');
                });
            } elseif ($searchBy == 'klien') {
                $query->whereHas('klien', function($q) use ($searchValue) {
                    $q->where('nama_klien', 'like', '%' . $searchValue . '%');
                });
            } elseif ($searchBy == 'status_invoice') {
                $query->where('status_invoice', 'like', '%' . $searchValue . '%');
            } else {
                $query->where(function($q) use ($searchValue) {
                    $q->where('nomor_tiket', 'like', '%' . $searchValue . '%')
                      ->orWhere('tiket', 'like', '%' . $searchValue . '%');
                });
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('waktu_masuk', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('waktu_masuk', '<=', $request->end_date);
        }

        $ritase = $query->orderByDesc('waktu_masuk')->get();
        $totalBeratNetto = $ritase->sum('berat_netto');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ritase.pdf-rekap', compact('ritase', 'totalBeratNetto'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Rekap_Ritase_' . date('Ymd_His') . '.pdf');
    }

    public function create()
    {
        Gate::authorize('create_ritase');
        $armadas = Armada::orderBy('plat_nomor')->get();
        $kliens = Klien::orderBy('nama_klien')->get();
        return view('admin.ritase.form', compact('armadas', 'kliens'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_ritase');

        $validated = $request->validate([
            'armada_id' => 'required|exists:armada,id',
            'klien_id' => 'required|exists:klien,id',
            'waktu_masuk' => 'required|date',
            'waktu_keluar' => 'nullable|date',
            'berat_bruto' => 'required|numeric|min:0',
            'berat_tarra' => 'required|numeric|min:0',
            'jenis_sampah' => 'nullable|string',
            'biaya_tipping' => 'nullable|numeric|min:0',
            'status' => 'required|in:masuk,timbang,keluar,selesai',
            'tiket' => 'nullable|string',
            'foto_tiket' => 'nullable|image|max:2048',
            'foto_tiket_bruto' => 'nullable|image|max:2048',
            'foto_tiket_tarra' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto_tiket')) {
            $validated['foto_tiket'] = $request->file('foto_tiket')->store('ritase_tiket', 'public');
        }
        if ($request->hasFile('foto_tiket_bruto')) {
            $validated['foto_tiket_bruto'] = $request->file('foto_tiket_bruto')->store('ritase_tiket', 'public');
        }
        if ($request->hasFile('foto_tiket_tarra')) {
            $validated['foto_tiket_tarra'] = $request->file('foto_tiket_tarra')->store('ritase_tiket', 'public');
        }

        $validated['berat_netto'] = ($validated['berat_bruto'] ?? 0) - ($validated['berat_tarra'] ?? 0);

        $tenantId = auth()->user()->tenant_id;
        if (!$tenantId) {
            $firstTenant = \App\Models\Tenant::first();
            if ($firstTenant) {
                $tenantId = $firstTenant->id;
            }
        }
        $validated['tenant_id'] = $tenantId;

        DB::transaction(function () use ($validated) {
            Ritase::create($validated);
        });

        return redirect()->route('admin.ritase.index')->with('success', 'Ritase berhasil ditambahkan.');
    }

    public function edit(Ritase $ritase)
    {
        Gate::authorize('update_ritase');
        $armadas = Armada::orderBy('plat_nomor')->get();
        $kliens = Klien::orderBy('nama_klien')->get();
        return view('admin.ritase.form', compact('ritase', 'armadas', 'kliens'));
    }

    public function update(Request $request, Ritase $ritase)
    {
        Gate::authorize('update_ritase');

        $validated = $request->validate([
            'armada_id' => 'required|exists:armada,id',
            'klien_id' => 'required|exists:klien,id',
            'waktu_masuk' => 'required|date',
            'waktu_keluar' => 'nullable|date',
            'berat_bruto' => 'required|numeric|min:0',
            'berat_tarra' => 'required|numeric|min:0',
            'jenis_sampah' => 'nullable|string',
            'biaya_tipping' => 'nullable|numeric|min:0',
            'status' => 'required|in:masuk,timbang,keluar,selesai',
            'tiket' => 'nullable|string',
            'foto_tiket' => 'nullable|image|max:2048',
            'foto_tiket_bruto' => 'nullable|image|max:2048',
            'foto_tiket_tarra' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto_tiket')) {
            // Delete old photo
            if ($ritase->foto_tiket && \Illuminate\Support\Facades\Storage::disk('public')->exists($ritase->foto_tiket)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($ritase->foto_tiket);
            }
            $validated['foto_tiket'] = $request->file('foto_tiket')->store('ritase_tiket', 'public');
        }

        if ($request->hasFile('foto_tiket_bruto')) {
            if ($ritase->foto_tiket_bruto && \Illuminate\Support\Facades\Storage::disk('public')->exists($ritase->foto_tiket_bruto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($ritase->foto_tiket_bruto);
            }
            $validated['foto_tiket_bruto'] = $request->file('foto_tiket_bruto')->store('ritase_tiket', 'public');
        }

        if ($request->hasFile('foto_tiket_tarra')) {
            if ($ritase->foto_tiket_tarra && \Illuminate\Support\Facades\Storage::disk('public')->exists($ritase->foto_tiket_tarra)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($ritase->foto_tiket_tarra);
            }
            $validated['foto_tiket_tarra'] = $request->file('foto_tiket_tarra')->store('ritase_tiket', 'public');
        }

        $validated['berat_netto'] = ($validated['berat_bruto'] ?? 0) - ($validated['berat_tarra'] ?? 0);

        if (empty($ritase->tenant_id)) {
            $tenantId = auth()->user()->tenant_id;
            if (!$tenantId) {
                $firstTenant = \App\Models\Tenant::first();
                if ($firstTenant) {
                    $tenantId = $firstTenant->id;
                }
            }
            $validated['tenant_id'] = $tenantId;
        }

        DB::transaction(function () use ($ritase, $validated) {
            $ritase->update($validated);
        });

        return redirect()->route('admin.ritase.index')->with('success', 'Ritase berhasil diperbarui.');
    }

    public function destroy(Ritase $ritase)
    {
        Gate::authorize('delete_ritase');
        DB::transaction(function () use ($ritase) {
            $ritase->delete();
        });
        return redirect()->route('admin.ritase.index')->with('success', 'Ritase berhasil dihapus.');
    }

    public function approve(Ritase $ritase)
    {
        Gate::authorize('update_ritase');
        
        DB::transaction(function () use ($ritase) {
            $ritase->update([
                'is_approved' => true,
                'approved_at' => now(),
            ]);

            // Auto-Invoice Logic
            $month = $ritase->waktu_masuk->format('n');
            $year = $ritase->waktu_masuk->format('Y');

            $klienId = $ritase->klien_id;
            
            // If client is DLH type, use the master DLH client as payer
            if ($ritase->klien && $ritase->klien->jenis === 'DLH') {
                $masterDLH = \App\Models\Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first();
                if ($masterDLH) {
                    $klienId = $masterDLH->id;
                }
            }

            $invoice = \App\Models\Invoice::where('tenant_id', $ritase->tenant_id)
                ->where('klien_id', $klienId)
                ->where('periode_bulan', $month)
                ->where('periode_tahun', $year)
                ->where('status', 'Draft')
                ->first();

            if (!$invoice) {
                $invoice = \App\Models\Invoice::create([
                    'tenant_id' => $ritase->tenant_id,
                    'klien_id' => $klienId,
                    'periode_bulan' => $month,
                    'periode_tahun' => $year,
                    'tanggal_invoice' => now(),
                    'tanggal_jatuh_tempo' => now()->addDays(30),
                    'total_tagihan' => 0,
                    'status' => 'Draft',
                    'keterangan' => 'Generated automatically from approved ritase',
                ]);
            }

            // Attach Ritase to Invoice
            $ritase->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);

            // Recalculate Invoice total
            $totalRitase = $invoice->ritase()->sum('biaya_tipping');
            $totalPenjualan = $invoice->penjualan()->sum('total_harga');
            $invoice->update(['total_tagihan' => $totalRitase + $totalPenjualan]);
        });

        return redirect()->back()->with('success', 'Ritase berhasil di-approve dan ditambahkan ke Invoice Draft.');
    }

    public function show(Ritase $ritase)
    {
        Gate::authorize('view_ritase');
        $ritase->load(['armada', 'klien']);
        return view('admin.ritase.show', compact('ritase'));
    }
}
