<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\Klien;
use App\Models\HasilPilahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PenjualanController extends Controller
{
    private function calculateAvailableStock(?int $excludePenjualanId = null): array
    {
        $hasilPilahan = HasilPilahan::selectRaw('jenis, SUM(tonase) as total_masuk')
            ->groupBy('jenis')
            ->get()
            ->keyBy('jenis');

        $penjualanQuery = Penjualan::selectRaw('jenis_produk, SUM(berat_kg) as total_keluar')
            ->groupBy('jenis_produk');
            
        if ($excludePenjualanId) {
            $penjualanQuery->where('id', '!=', $excludePenjualanId);
        }
        
        $penjualan = $penjualanQuery->get()->keyBy('jenis_produk');

        // Ensure all active waste categories are present in the stock list, even if 0
        $allCategories = \App\Models\WasteCategory::where('is_active', true)->pluck('name')->toArray();
        $stok = array_fill_keys($allCategories, 0);

        foreach ($hasilPilahan as $jenis => $data) {
            $masuk = $data->total_masuk;
            $keluar = isset($penjualan[$jenis]) ? $penjualan[$jenis]->total_keluar : 0;
            $sisa = $masuk - $keluar;
            
            if ($sisa > 0 || ($excludePenjualanId && isset($penjualan[$jenis])) || in_array($jenis, $allCategories)) {
                $stok[$jenis] = $sisa;
            }
        }

        return $stok;
    }
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
        $stokPilahan = $this->calculateAvailableStock();
        return view('admin.penjualan.form', compact('kliens', 'stokPilahan'));
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
            'jumlah_bayar' => 'nullable|numeric|min:0',
        ]);

        $stokTersedia = $this->calculateAvailableStock();
        $jenis = $request->jenis_produk;
        $maxStok = $stokTersedia[$jenis] ?? 0;

        if ($request->berat_kg > $maxStok) {
            return back()->withErrors(['berat_kg' => "Stok {$jenis} tidak mencukupi. Maksimal: {$maxStok} kg"])->withInput();
        }

        $validated['total_harga'] = ($validated['berat_kg'] ?? 0) * ($validated['harga_satuan'] ?? 0);
        $validated['jumlah_bayar'] = $validated['jumlah_bayar'] ?? 0;

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
        $stokPilahan = $this->calculateAvailableStock($penjualan->id);
        return view('admin.penjualan.form', compact('penjualan', 'kliens', 'stokPilahan'));
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
            'jumlah_bayar' => 'nullable|numeric|min:0',
        ]);

        $stokTersedia = $this->calculateAvailableStock($penjualan->id);
        $jenis = $request->jenis_produk;
        $maxStok = $stokTersedia[$jenis] ?? 0;

        if ($request->berat_kg > $maxStok) {
            return back()->withErrors(['berat_kg' => "Stok {$jenis} tidak mencukupi. Maksimal: {$maxStok} kg"])->withInput();
        }

        $validated['total_harga'] = ($validated['berat_kg'] ?? 0) * ($validated['harga_satuan'] ?? 0);
        $validated['jumlah_bayar'] = $validated['jumlah_bayar'] ?? 0;

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
