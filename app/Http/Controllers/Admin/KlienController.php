<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class KlienController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_klien');
        $query = Klien::query();

        if ($request->filled('search')) {
            $query->where('nama_klien', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $kliens = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.klien.index', compact('kliens'));
    }

    public function create()
    {
        Gate::authorize('create_klien');
        return view('admin.klien.form');
    }

    public function store(Request $request)
    {
        Gate::authorize('create_klien');
        
        $validated = $request->validate([
            'nama_klien' => 'required|string|max:255',
            'jenis' => 'required|in:DLH,Swasta,Offtaker',
            'kontak' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        Klien::create($validated);

        return redirect()->route('admin.klien.index')->with('success', 'Klien berhasil ditambahkan.');
    }

    public function edit(Klien $klien)
    {
        Gate::authorize('update_klien');
        return view('admin.klien.form', compact('klien'));
    }

    public function update(Request $request, Klien $klien)
    {
        Gate::authorize('update_klien');
        
        $validated = $request->validate([
            'nama_klien' => 'required|string|max:255',
            'jenis' => 'required|in:DLH,Swasta,Offtaker',
            'kontak' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $klien->update($validated);

        return redirect()->route('admin.klien.index')->with('success', 'Klien berhasil diperbarui.');
    }

    public function destroy(Klien $klien)
    {
        Gate::authorize('delete_klien');
        $klien->delete();
        return redirect()->route('admin.klien.index')->with('success', 'Klien berhasil dihapus.');
    }
}
