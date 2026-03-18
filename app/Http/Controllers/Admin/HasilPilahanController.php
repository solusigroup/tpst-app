<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilPilahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HasilPilahanController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_hasil_pilahan');
        $query = HasilPilahan::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('jenis', 'like', '%' . $request->search . '%')
                  ->orWhere('officer', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $hasilPilahans = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('admin.hasil-pilahan.index', compact('hasilPilahans'));
    }

    public function create()
    {
        Gate::authorize('create_hasil_pilahan');
        $wasteCategories = \App\Models\WasteCategory::where('is_active', true)->orderBy('name')->pluck('name');
        
        $query = \App\Models\User::role('karyawan')->where('salary_type', 'borongan');
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }
        $petugas = $query->orderBy('name')->pluck('name');

        return view('admin.hasil-pilahan.form', compact('wasteCategories', 'petugas'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_hasil_pilahan');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|in:Organik,Anorganik,B3,Residu',
            'jenis' => 'required|string|max:255',
            'tonase' => 'required|numeric|min:0',
            'officer' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
        ]);

        HasilPilahan::create($validated);

        return redirect()->route('admin.hasil-pilahan.index')->with('success', 'Hasil pilahan berhasil ditambahkan.');
    }

    public function edit(HasilPilahan $hasilPilahan)
    {
        Gate::authorize('update_hasil_pilahan');
        $wasteCategories = \App\Models\WasteCategory::where('is_active', true)->orderBy('name')->pluck('name');

        $query = \App\Models\User::role('karyawan')->where('salary_type', 'borongan');
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }
        $petugas = $query->orderBy('name')->pluck('name');

        return view('admin.hasil-pilahan.form', compact('hasilPilahan', 'wasteCategories', 'petugas'));
    }

    public function update(Request $request, HasilPilahan $hasilPilahan)
    {
        Gate::authorize('update_hasil_pilahan');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|in:Organik,Anorganik,B3,Residu',
            'jenis' => 'required|string|max:255',
            'tonase' => 'required|numeric|min:0',
            'officer' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $hasilPilahan->update($validated);

        return redirect()->route('admin.hasil-pilahan.index')->with('success', 'Hasil pilahan berhasil diperbarui.');
    }

    public function destroy(HasilPilahan $hasilPilahan)
    {
        Gate::authorize('delete_hasil_pilahan');
        $hasilPilahan->delete();
        return redirect()->route('admin.hasil-pilahan.index')->with('success', 'Hasil pilahan berhasil dihapus.');
    }
}
