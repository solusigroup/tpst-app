<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class InvoiceAdminController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_invoice');
        $query = Invoice::with('klien');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_invoice', 'like', '%' . $search . '%')
                  ->orWhereHas('klien', function($qKlien) use ($search) {
                      $qKlien->where('nama_klien', 'like', '%' . $search . '%');
                  });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderByDesc('tanggal_invoice')->paginate(15)->withQueryString();

        return view('admin.invoice.index', compact('invoices'));
    }

    public function create()
    {
        Gate::authorize('create_invoice');
        $kliens = Klien::orderBy('nama_klien')->get();
        $coas = \App\Models\Coa::where('tipe', 'Asset')
            ->where(function ($q) {
                $q->where('kode_akun', 'like', '11%')
                  ->orWhere('nama_akun', 'like', '%Kas%')
                  ->orWhere('nama_akun', 'like', '%Bank%');
            })
            ->where('klasifikasi', 'Aset Lancar')
            ->orderBy('kode_akun')
            ->get();
        return view('admin.invoice.form', compact('kliens', 'coas'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_invoice');

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'periode_bulan' => 'required|string',
            'periode_tahun' => 'required|string',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'total_tagihan' => 'required|numeric|min:0',
            'uang_muka' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Sent,Paid,Canceled',
            'keterangan' => 'nullable|string',
            'deskripsi_layanan' => 'nullable|string',
            'selected_ritase' => 'nullable|array',
            'selected_ritase.*' => 'exists:ritase,id',
            'selected_penjualan' => 'nullable|array',
            'selected_penjualan.*' => 'exists:penjualan,id',
            'coa_pembayaran_id' => 'nullable|exists:coa,id',
        ]);

        $invoiceData = collect($validated)->except(['selected_ritase', 'selected_penjualan'])->toArray();
        $invoice = Invoice::create($invoiceData);

        // Attach Ritase
        if (!empty($validated['selected_ritase'])) {
            \App\Models\Ritase::whereIn('id', $validated['selected_ritase'])->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);
        }

        // Attach Penjualan
        if (!empty($validated['selected_penjualan'])) {
            \App\Models\Penjualan::whereIn('id', $validated['selected_penjualan'])->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);
        }

        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function show(Invoice $invoice)
    {
        Gate::authorize('view_invoice');
        $invoice->load(['klien', 'ritase', 'penjualan']);
        return view('admin.invoice.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        Gate::authorize('update_invoice');
        $kliens = Klien::orderBy('nama_klien')->get();
        $coas = \App\Models\Coa::where('tipe', 'Asset')
            ->where(function ($q) {
                $q->where('kode_akun', 'like', '11%')
                  ->orWhere('nama_akun', 'like', '%Kas%')
                  ->orWhere('nama_akun', 'like', '%Bank%');
            })
            ->where('klasifikasi', 'Aset Lancar')
            ->orderBy('kode_akun')
            ->get();
        return view('admin.invoice.form', compact('invoice', 'kliens', 'coas'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        Gate::authorize('update_invoice');

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'periode_bulan' => 'required|string',
            'periode_tahun' => 'required|string',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'total_tagihan' => 'required|numeric|min:0',
            'uang_muka' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Sent,Paid,Canceled',
            'keterangan' => 'nullable|string',
            'deskripsi_layanan' => 'nullable|string',
            'selected_ritase' => 'nullable|array',
            'selected_ritase.*' => 'exists:ritase,id',
            'selected_penjualan' => 'nullable|array',
            'selected_penjualan.*' => 'exists:penjualan,id',
            'coa_pembayaran_id' => 'nullable|exists:coa,id',
        ]);

        $invoiceData = collect($validated)->except(['selected_ritase', 'selected_penjualan'])->toArray();
        $invoice->update($invoiceData);

        // Sync Ritase: nullify old attachments first
        \App\Models\Ritase::where('invoice_id', $invoice->id)->update(['invoice_id' => null, 'status_invoice' => 'Draft']);
        if (!empty($validated['selected_ritase'])) {
            \App\Models\Ritase::whereIn('id', $validated['selected_ritase'])->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);
        }

        // Sync Penjualan: nullify old attachments first
        \App\Models\Penjualan::where('invoice_id', $invoice->id)->update(['invoice_id' => null, 'status_invoice' => 'Draft']);
        if (!empty($validated['selected_penjualan'])) {
            \App\Models\Penjualan::whereIn('id', $validated['selected_penjualan'])->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);
        }

        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice)
    {
        Gate::authorize('delete_invoice');
        $invoice->delete();
        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dihapus.');
    }

    public function mergeDrafts()
    {
        Gate::authorize('update_invoice');
        
        $masterDLH = Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first();
        $allDrafts = Invoice::with('klien')->where('status', 'Draft')->get();
        
        // 1. First, redirect all DLH-type invoices to the Master DLH client ID
        if ($masterDLH) {
            foreach ($allDrafts as $draft) {
                if ($draft->klien && $draft->klien->jenis === 'DLH' && $draft->klien_id != $masterDLH->id) {
                    // Update this draft to belong to Master DLH so it gets grouped in the next step
                    $draft->update(['klien_id' => $masterDLH->id]);
                }
            }
            // Refresh drafts after redirection
            $allDrafts = Invoice::with('klien')->where('status', 'Draft')->get();
        }

        // 2. Group by klien_id, periode_bulan, and periode_tahun to ensure we don't merge across periods
        $groupedDrafts = $allDrafts->groupBy(function($item) {
            // Normalize month to integer string to handle '04' vs '4'
            $month = (int)$item->periode_bulan;
            return $item->klien_id . '-' . $month . '-' . $item->periode_tahun;
        });
        
        $mergedCount = 0;
        DB::transaction(function () use ($groupedDrafts, &$mergedCount) {
            foreach ($groupedDrafts as $key => $invoices) {
                if ($invoices->count() > 1) {
                    $master = $invoices->first();
                    $others = $invoices->slice(1);
                    
                    foreach ($others as $other) {
                        \App\Models\Ritase::where('invoice_id', $other->id)->update([
                            'invoice_id' => $master->id
                        ]);
                        
                        \App\Models\Penjualan::where('invoice_id', $other->id)->update([
                            'invoice_id' => $master->id
                        ]);
                        
                        $other->delete();
                        $mergedCount++;
                    }
                    
                    // Recalculate totals for the merged master invoice
                    $totalRitase = \App\Models\Ritase::where('invoice_id', $master->id)->sum('biaya_tipping');
                    $totalPenjualan = \App\Models\Penjualan::where('invoice_id', $master->id)->sum('total_harga');
                    $totalUangMuka = \App\Models\Penjualan::where('invoice_id', $master->id)->sum('jumlah_bayar');
                    
                    $master->update([
                        'total_tagihan' => $totalRitase + $totalPenjualan,
                        'uang_muka' => $totalUangMuka,
                        'coa_pembayaran_id' => $master->coa_pembayaran_id ?? \App\Models\Coa::where('kode_akun', '1102')->value('id'),
                        'keterangan' => empty($master->keterangan) ? 'Merged automatically' : $master->keterangan . ' (Merged)'
                    ]);
                }
            }
        });

        if ($mergedCount > 0) {
            return redirect()->route('admin.invoice.index')->with('success', "$mergedCount invoice draft berhasil digabungkan.");
        }
        return redirect()->route('admin.invoice.index')->with('info', 'Tidak ada draft invoice untuk klien yang sama yang perlu digabungkan.');
    }
    public function syncDlhItems($id)
    {
        Gate::authorize('update_invoice');
        
        $invoice = Invoice::with('klien')->findOrFail($id);
        
        if (!$invoice->klien || $invoice->klien->jenis !== 'DLH') {
            return back()->with('error', 'Hanya invoice DLH yang dapat disinkronkan otomatis.');
        }

        $count = 0;
        DB::transaction(function () use ($invoice, &$count) {
            // Find all ritase from DLH type clients in the same month/year
            $missingRitase = \App\Models\Ritase::whereHas('klien', function($q) {
                    $q->where('jenis', 'DLH');
                })
                ->whereYear('waktu_masuk', $invoice->periode_tahun)
                ->whereMonth('waktu_masuk', $invoice->periode_bulan)
                ->where(function($q) use ($invoice) {
                    $q->whereNull('invoice_id')->orWhere('invoice_id', '!=', $invoice->id);
                })
                ->where('status_invoice', '!=', 'Paid') // Don't steal from paid invoices
                ->get();

            foreach ($missingRitase as $ritase) {
                $ritase->update([
                    'invoice_id' => $invoice->id,
                    'status_invoice' => $invoice->status,
                    'status' => 'selesai' // Ensure status is consistent
                ]);
                $count++;
            }

            // Also check Penjualan
            $missingPenjualan = \App\Models\Penjualan::whereHas('klien', function($q) {
                    $q->where('jenis', 'DLH');
                })
                ->whereYear('tanggal', $invoice->periode_tahun)
                ->whereMonth('tanggal', $invoice->periode_bulan)
                ->where(function($q) use ($invoice) {
                    $q->whereNull('invoice_id')->orWhere('invoice_id', '!=', $invoice->id);
                })
                ->get();

            foreach ($missingPenjualan as $p) {
                $p->update(['invoice_id' => $invoice->id]);
                $count++;
            }

            if ($count > 0) {
                $totalRitase = \App\Models\Ritase::where('invoice_id', $invoice->id)->sum('biaya_tipping');
                $totalPenjualan = \App\Models\Penjualan::where('invoice_id', $invoice->id)->sum('total_harga');
                $totalUangMuka = \App\Models\Penjualan::where('invoice_id', $invoice->id)->sum('jumlah_bayar');

                $invoice->update([
                    'total_tagihan' => $totalRitase + $totalPenjualan,
                    'uang_muka' => $totalUangMuka,
                ]);
            }
        });

        return back()->with('success', "Berhasil menyinkronkan $count data baru ke invoice ini.");
    }
}
