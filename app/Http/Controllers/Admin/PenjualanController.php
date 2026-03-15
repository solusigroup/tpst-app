<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('klien');

        if ($request->filled('search')) {
            $query->where('jenis_produk', 'like', '%' . $request->search . '%');
        }

        $penjualans = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('admin.penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        Gate::authorize('create_penjualan');
        $kliens = Klien::orderBy('nama_klien')->get();
        return view('admin.penjualan.form', compact('kliens'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_penjualan');

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'tanggal' => 'required|date',
            'jenis_produk' => 'required|string',
            'berat_kg' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $validated['total_harga'] = ($validated['berat_kg'] ?? 0) * ($validated['harga_satuan'] ?? 0);

        $tenantId = auth()->user()->tenant_id;
        if (!$tenantId) {
            $firstTenant = \App\Models\Tenant::first();
            if ($firstTenant) {
                $tenantId = $firstTenant->id;
            }
        }
        $validated['tenant_id'] = $tenantId;

        Penjualan::create($validated);

        return redirect()->route('admin.penjualan.index')->with('success', 'Penjualan berhasil ditambahkan.');
    }

    public function edit(Penjualan $penjualan)
    {
        Gate::authorize('update_penjualan');
        $kliens = Klien::orderBy('nama_klien')->get();
        return view('admin.penjualan.form', compact('penjualan', 'kliens'));
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        Gate::authorize('update_penjualan');

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'tanggal' => 'required|date',
            'jenis_produk' => 'required|string',
            'berat_kg' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $validated['total_harga'] = ($validated['berat_kg'] ?? 0) * ($validated['harga_satuan'] ?? 0);

        if (empty($penjualan->tenant_id)) {
            $tenantId = auth()->user()->tenant_id;
            if (!$tenantId) {
                $firstTenant = \App\Models\Tenant::first();
                if ($firstTenant) {
                    $tenantId = $firstTenant->id;
                }
            }
            $validated['tenant_id'] = $tenantId;
        }

        $penjualan->update($validated);

        return redirect()->route('admin.penjualan.index')->with('success', 'Penjualan berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan)
    {
        Gate::authorize('delete_penjualan');
        $penjualan->delete();
        return redirect()->route('admin.penjualan.index')->with('success', 'Penjualan berhasil dihapus.');
    }
}
