<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Armada;
use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ArmadaController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_armada');
        $query = Armada::with('klien');

        if ($request->filled('search')) {
            $query->where('plat_nomor', 'like', '%' . $request->search . '%');
        }

        $armadas = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.armada.index', compact('armadas'));
    }

    public function create()
    {
        Gate::authorize('create_armada');
        $kliens = Klien::orderBy('nama_klien')->get();
        return view('admin.armada.form', compact('kliens'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_armada');

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'plat_nomor' => 'required|string|unique:armada,plat_nomor',
            'kapasitas_maksimal' => 'required|numeric|min:0',
        ]);

        Armada::create($validated);

        return redirect()->route('admin.armada.index')->with('success', 'Armada berhasil ditambahkan.');
    }

    public function edit(Armada $armada)
    {
        Gate::authorize('update_armada');
        $kliens = Klien::orderBy('nama_klien')->get();
        return view('admin.armada.form', compact('armada', 'kliens'));
    }

    public function update(Request $request, Armada $armada)
    {
        Gate::authorize('update_armada');

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'plat_nomor' => 'required|string|unique:armada,plat_nomor,' . $armada->id,
            'kapasitas_maksimal' => 'required|numeric|min:0',
        ]);

        $armada->update($validated);

        return redirect()->route('admin.armada.index')->with('success', 'Armada berhasil diperbarui.');
    }

    public function destroy(Armada $armada)
    {
        Gate::authorize('delete_armada');
        $armada->delete();
        return redirect()->route('admin.armada.index')->with('success', 'Armada berhasil dihapus.');
    }
}
