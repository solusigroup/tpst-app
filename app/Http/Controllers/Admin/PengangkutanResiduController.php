<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengangkutanResidu;
use Illuminate\Http\Request;

use App\Models\Armada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PengangkutanResiduController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_pengangkutan_residu');
        $query = PengangkutanResidu::with(['armada'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where('nomor_tiket', 'like', "%{$request->search}%")
                  ->orWhereHas('armada', function($q) use ($request) {
                      $q->where('plat_nomor', 'like', "%{$request->search}%");
                  });
        }

        $entries = $query->paginate(20);

        return view('admin.pengangkutan_residu.index', compact('entries'));
    }

    public function create()
    {
        Gate::authorize('create_pengangkutan_residu');
        $armadas = Armada::orderBy('plat_nomor')->get();
        return view('admin.pengangkutan_residu.create', compact('armadas'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_pengangkutan_residu');
        $request->validate([
            'armada_id' => 'required|exists:armada,id',
            'tanggal' => 'required|date',
            'waktu_keluar' => 'nullable',
            'waktu_masuk' => 'nullable',
            'berat_bruto' => 'required|numeric|min:0',
            'berat_tarra' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        PengangkutanResidu::create($request->all());

        return redirect()->route('admin.pengangkutan-residu.index')
            ->with('success', 'Data pengangkutan residu berhasil dicatat.');
    }

    public function show(PengangkutanResidu $pengangkutanResidu)
    {
        Gate::authorize('view_pengangkutan_residu');
        return view('admin.pengangkutan_residu.show', [
            'item' => $pengangkutanResidu->load(['armada', 'jurnalHeader'])
        ]);
    }

    public function edit(PengangkutanResidu $pengangkutanResidu)
    {
        Gate::authorize('update_pengangkutan_residu');
        $armadas = Armada::orderBy('plat_nomor')->get();
        return view('admin.pengangkutan_residu.edit', [
            'item' => $pengangkutanResidu,
            'armadas' => $armadas
        ]);
    }

    public function update(Request $request, PengangkutanResidu $pengangkutanResidu)
    {
        Gate::authorize('update_pengangkutan_residu');
        $request->validate([
            'armada_id' => 'required|exists:armada,id',
            'tanggal' => 'required|date',
            'waktu_keluar' => 'nullable',
            'waktu_masuk' => 'nullable',
            'berat_bruto' => 'required|numeric|min:0',
            'berat_tarra' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $pengangkutanResidu->update($request->all());

        return redirect()->route('admin.pengangkutan-residu.index')
            ->with('success', 'Data pengangkutan residu berhasil diperbarui.');
    }

    public function destroy(PengangkutanResidu $pengangkutanResidu)
    {
        Gate::authorize('delete_pengangkutan_residu');
        $pengangkutanResidu->delete();
        return redirect()->route('admin.pengangkutan-residu.index')
            ->with('success', 'Data pengangkutan residu berhasil dihapus.');
    }
}
