<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
        ]);

        Invoice::create($validated);

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
        ]);

        $invoice->update($validated);

        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice)
    {
        Gate::authorize('delete_invoice');
        $invoice->delete();
        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dihapus.');
    }
}
