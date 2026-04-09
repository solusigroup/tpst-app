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
            $query->where('nomor_invoice', 'like', '%' . $request->search . '%');
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
        return view('admin.invoice.form', compact('kliens'));
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
            'status' => 'required|in:Draft,Sent,Paid,Canceled',
            'keterangan' => 'nullable|string',
            'deskripsi_layanan' => 'nullable|string',
            'selected_ritase' => 'nullable|array',
            'selected_ritase.*' => 'exists:ritase,id',
            'selected_penjualan' => 'nullable|array',
            'selected_penjualan.*' => 'exists:penjualan,id',
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

    public function edit(Invoice $invoice)
    {
        Gate::authorize('update_invoice');
        $kliens = Klien::orderBy('nama_klien')->get();
        return view('admin.invoice.form', compact('invoice', 'kliens'));
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
            'status' => 'required|in:Draft,Sent,Paid,Canceled',
            'keterangan' => 'nullable|string',
            'deskripsi_layanan' => 'nullable|string',
            'selected_ritase' => 'nullable|array',
            'selected_ritase.*' => 'exists:ritase,id',
            'selected_penjualan' => 'nullable|array',
            'selected_penjualan.*' => 'exists:penjualan,id',
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
        
        $draftInvoices = Invoice::where('status', 'Draft')->get()->groupBy('klien_id');
        
        $mergedCount = 0;
        DB::transaction(function () use ($draftInvoices, &$mergedCount) {
            foreach ($draftInvoices as $klienId => $invoices) {
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
                    
                    $totalRitase = \App\Models\Ritase::where('invoice_id', $master->id)->sum('biaya_tipping');
                    $totalPenjualan = \App\Models\Penjualan::where('invoice_id', $master->id)->sum('total_harga');
                    
                    $master->update([
                        'total_tagihan' => $totalRitase + $totalPenjualan,
                        'keterangan' => empty($master->keterangan) ? 'Merged with other drafts' : $master->keterangan . ' (Merged)'
                    ]);
                }
            }
        });

        if ($mergedCount > 0) {
            return redirect()->route('admin.invoice.index')->with('success', "$mergedCount invoice draft berhasil digabungkan.");
        }
        return redirect()->route('admin.invoice.index')->with('info', 'Tidak ada draft invoice untuk klien yang sama yang perlu digabungkan.');
    }
}
