<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Models\Armada;
use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RitaseController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_ritase');
        $query = Ritase::with(['armada', 'klien']);

        if ($request->filled('search')) {
            $query->where('nomor_tiket', 'like', '%' . $request->search . '%');
        }

        $ritase = $query->orderByDesc('waktu_masuk')->paginate(15)->withQueryString();

        return view('admin.ritase.index', compact('ritase'));
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
        ]);

        $validated['berat_netto'] = ($validated['berat_bruto'] ?? 0) - ($validated['berat_tarra'] ?? 0);

        $tenantId = auth()->user()->tenant_id;
        if (!$tenantId) {
            $firstTenant = \App\Models\Tenant::first();
            if ($firstTenant) {
                $tenantId = $firstTenant->id;
            }
        }
        $validated['tenant_id'] = $tenantId;

        Ritase::create($validated);

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
        ]);

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

        $ritase->update($validated);

        return redirect()->route('admin.ritase.index')->with('success', 'Ritase berhasil diperbarui.');
    }

    public function destroy(Ritase $ritase)
    {
        Gate::authorize('delete_ritase');
        $ritase->delete();
        return redirect()->route('admin.ritase.index')->with('success', 'Ritase berhasil dihapus.');
    }
}
