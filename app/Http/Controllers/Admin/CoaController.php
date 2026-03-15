<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CoaController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_coa');
        $query = Coa::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_akun', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_akun', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $coas = $query->orderBy('kode_akun')->paginate(15)->withQueryString();

        return view('admin.coa.index', compact('coas'));
    }

    public function create()
    {
        Gate::authorize('create_coa');
        return view('admin.coa.form');
    }

    public function store(Request $request)
    {
        Gate::authorize('create_coa');

        $validated = $request->validate([
            'kode_akun' => 'required|string|unique:coa,kode_akun',
            'nama_akun' => 'required|string',
            'tipe' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'klasifikasi' => 'required|string',
        ]);

        Coa::create($validated);

        return redirect()->route('admin.coa.index')->with('success', 'COA berhasil ditambahkan.');
    }

    public function edit(Coa $coa)
    {
        Gate::authorize('update_coa');
        return view('admin.coa.form', compact('coa'));
    }

    public function update(Request $request, Coa $coa)
    {
        Gate::authorize('update_coa');

        $validated = $request->validate([
            'kode_akun' => 'required|string|unique:coa,kode_akun,' . $coa->id,
            'nama_akun' => 'required|string',
            'tipe' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'klasifikasi' => 'required|string',
        ]);

        $coa->update($validated);

        return redirect()->route('admin.coa.index')->with('success', 'COA berhasil diperbarui.');
    }

    public function destroy(Coa $coa)
    {
        Gate::authorize('delete_coa');
        $coa->delete();
        return redirect()->route('admin.coa.index')->with('success', 'COA berhasil dihapus.');
    }
}
