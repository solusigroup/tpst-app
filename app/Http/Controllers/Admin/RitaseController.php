<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Models\Armada;
use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        if ($request->filled('approval_status')) {
            if ($request->approval_status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->approval_status === 'pending') {
                $query->where('is_approved', false);
            }
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

        if ($request->filled('approval_status')) {
            if ($request->approval_status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->approval_status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        $ritase = $query->orderByDesc('waktu_masuk')->get();
        $totalBeratNetto = $ritase->sum('berat_netto');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ritase.pdf-rekap', compact('ritase', 'totalBeratNetto'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Rekap_Ritase_' . date('Ymd_His') . '.pdf');
    }

    /**
     * API: Return distinct asal_sampah (jenis_sampah) values for a given klien_id.
     */
    public function asalSampahByKlien(Request $request)
    {
        $klienId = $request->get('klien_id');
        if (!$klienId) {
            return response()->json([]);
        }

        $items = Ritase::where('klien_id', $klienId)
            ->whereNotNull('jenis_sampah')
            ->where('jenis_sampah', '!=', '')
            ->selectRaw('jenis_sampah, COUNT(*) as used_count')
            ->groupBy('jenis_sampah')
            ->orderByDesc('used_count')
            ->limit(50)
            ->pluck('jenis_sampah');

        return response()->json($items);
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
            'jenis_sampah' => 'required|string',
            'biaya_tipping' => 'nullable|numeric|min:0',
            'status' => 'required|in:masuk,timbang,keluar,selesai',
            'tiket' => 'nullable|string',
            'foto_tiket' => 'nullable|image|max:5120',
            'foto_tiket_bruto' => 'nullable|image|max:5120',
            'foto_tiket_tarra' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('foto_tiket')) {
            $validated['foto_tiket'] = \App\Helpers\ImageHelper::compressAndStore($request->file('foto_tiket'), 'ritase_tiket');
        }
        if ($request->hasFile('foto_tiket_bruto')) {
            $validated['foto_tiket_bruto'] = \App\Helpers\ImageHelper::compressAndStore($request->file('foto_tiket_bruto'), 'ritase_tiket');
        }
        if ($request->hasFile('foto_tiket_tarra')) {
            $validated['foto_tiket_tarra'] = \App\Helpers\ImageHelper::compressAndStore($request->file('foto_tiket_tarra'), 'ritase_tiket');
        }

        $validated['berat_netto'] = ($validated['berat_bruto'] ?? 0) - ($validated['berat_tarra'] ?? 0);

        $validated['tenant_id'] = auth()->user()->getEffectiveTenantId();

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
            'jenis_sampah' => 'required|string',
            'biaya_tipping' => 'nullable|numeric|min:0',
            'status' => 'required|in:masuk,timbang,keluar,selesai',
            'tiket' => 'nullable|string',
            'foto_tiket' => 'nullable|image|max:5120',
            'foto_tiket_bruto' => 'nullable|image|max:5120',
            'foto_tiket_tarra' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('foto_tiket')) {
            // Delete old photo
            if ($ritase->foto_tiket && \Illuminate\Support\Facades\Storage::disk('public')->exists($ritase->foto_tiket)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($ritase->foto_tiket);
            }
            $validated['foto_tiket'] = \App\Helpers\ImageHelper::compressAndStore($request->file('foto_tiket'), 'ritase_tiket');
        }

        if ($request->hasFile('foto_tiket_bruto')) {
            if ($ritase->foto_tiket_bruto && \Illuminate\Support\Facades\Storage::disk('public')->exists($ritase->foto_tiket_bruto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($ritase->foto_tiket_bruto);
            }
            $validated['foto_tiket_bruto'] = \App\Helpers\ImageHelper::compressAndStore($request->file('foto_tiket_bruto'), 'ritase_tiket');
        }

        if ($request->hasFile('foto_tiket_tarra')) {
            if ($ritase->foto_tiket_tarra && \Illuminate\Support\Facades\Storage::disk('public')->exists($ritase->foto_tiket_tarra)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($ritase->foto_tiket_tarra);
            }
            $validated['foto_tiket_tarra'] = \App\Helpers\ImageHelper::compressAndStore($request->file('foto_tiket_tarra'), 'ritase_tiket');
        }

        $validated['berat_netto'] = ($validated['berat_bruto'] ?? 0) - ($validated['berat_tarra'] ?? 0);

        if (empty($ritase->tenant_id)) {
            $validated['tenant_id'] = auth()->user()->getEffectiveTenantId();
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

    private function processApproval(Ritase $ritase, bool $recalculate = true): ?\App\Models\Invoice
    {
        $isDlh = $ritase->klien && $ritase->klien->jenis === 'DLH';

        // Check dynamically whether status_invoice column allows NULL in the database.
        // This ensures compatibility even if the database has not yet run the migration.
        static $statusInvoiceNullable = null;
        if ($statusInvoiceNullable === null) {
            try {
                $col = DB::select("SHOW COLUMNS FROM ritase LIKE 'status_invoice'");
                $statusInvoiceNullable = !empty($col) && strtoupper($col[0]->Null ?? '') === 'YES';
            } catch (\Throwable $e) {
                $statusInvoiceNullable = false;
            }
        }

        $ritaseData = [
            'is_approved' => true,
            'approved_at' => now(),
            'status' => 'selesai',
        ];

        if ($isDlh) {
            $ritaseData['status_invoice'] = $statusInvoiceNullable ? null : 'Draft';
        } else {
            $ritaseData['status_invoice'] = 'Draft';
        }

        $ritase->update($ritaseData);

        // Khusus DLH: Tidak membuat invoice eceran harian.
        // Rekapitulasi dilakukan di akhir bulan dalam 1 invoice resmi.
        if ($isDlh) {
            return null;
        }

        // Auto-Invoice Logic untuk Klien Non-DLH
        $waktuMasuk = $ritase->waktu_masuk ? \Carbon\Carbon::parse($ritase->waktu_masuk) : now();
        $month = $waktuMasuk->format('n');
        $year = $waktuMasuk->format('Y');
        $klienId = $ritase->klien_id;
        $tenantId = $ritase->tenant_id 
            ?? (auth()->check() ? auth()->user()->getEffectiveTenantId() : null)
            ?? \App\Models\Tenant::first()?->id;

        $invoice = \App\Models\Invoice::where('tenant_id', $tenantId)
            ->where('klien_id', $klienId)
            ->where('periode_bulan', $month)
            ->where('periode_tahun', $year)
            ->where('status', 'Draft')
            ->first();

        if (!$invoice) {
            $invoice = \App\Models\Invoice::create([
                'tenant_id' => $tenantId,
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

        // Recalculate Invoice total if requested
        if ($recalculate) {
            $invoice->recalculateTotals();
        }

        return $invoice;
    }

    public function approve(Ritase $ritase)
    {
        Gate::authorize('update_ritase');
        
        try {
            DB::transaction(function () use ($ritase) {
                $this->processApproval($ritase);
            });
        } catch (\Throwable $e) {
            Log::error('Error in approve ritase: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'ritase_id' => $ritase->id,
            ]);

            return redirect()->back()->with('error', 'Gagal meng-approve ritase: ' . $e->getMessage());
        }

        $msg = ($ritase->klien && $ritase->klien->jenis === 'DLH')
            ? 'Ritase DLH berhasil di-approve (siap direkap pada Invoice Bulanan DLH).'
            : 'Ritase berhasil di-approve dan ditambahkan ke Invoice Draft.';

        return redirect()->back()->with('success', $msg);
    }

    public function bulkApprove(Request $request)
    {
        Gate::authorize('update_ritase');

        $request->validate([
            'ritase_ids' => 'required|string',
        ]);

        $ids = array_filter(array_map('trim', explode(',', $request->ritase_ids)));
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada ritase yang dipilih.');
        }

        $ritases = Ritase::with(['klien'])->whereIn('id', $ids)->where('is_approved', false)->get();

        if ($ritases->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada ritase yang dipilih atau sudah di-approve.');
        }

        try {
            DB::transaction(function () use ($ritases) {
                $affectedInvoices = [];
                foreach ($ritases as $ritase) {
                    $invoice = $this->processApproval($ritase, false);
                    if ($invoice) {
                        $affectedInvoices[$invoice->id] = $invoice;
                    }
                }

                // Recalculate totals once per affected invoice
                foreach ($affectedInvoices as $invoice) {
                    $invoice->recalculateTotals();
                }
            });
        } catch (\Throwable $e) {
            Log::error('Error in bulkApprove ritase: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'ritase_ids' => $request->ritase_ids,
            ]);

            return redirect()->back()->with('error', 'Gagal memproses approve kolektif: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', count($ritases) . ' ritase berhasil di-approve secara kolektif.');
    }

    public function show(Ritase $ritase)
    {
        Gate::authorize('view_ritase');
        $ritase->load(['armada', 'klien']);
        return view('admin.ritase.show', compact('ritase'));
    }
}
