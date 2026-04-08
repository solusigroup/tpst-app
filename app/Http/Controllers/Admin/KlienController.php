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
        try {
            Gate::authorize('view_klien');
            $query = Klien::query();

            if ($request->filled('search')) {
                $query->where('nama_klien', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('jenis')) {
                $query->where('jenis', $request->jenis);
            }

            $kliens = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

            $view = view('admin.klien.index', compact('kliens'))->render();
            return response($view);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5)->map(fn($t) => ($t['file'] ?? '') . ':' . ($t['line'] ?? ''))->toArray(),
            ], 200);
        }
    }

    public function create()
    {
        Gate::authorize('create_klien');
        return view('admin.klien.form');
    }

    public function store(Request $request)
    {
        try {
            Gate::authorize('create_klien');
            
            $validated = $request->validate([
                'nama_klien' => 'required|string|max:255',
                'jenis' => 'required|in:DLH,Swasta,Offtaker,Internal',
                'jenis_tarif' => 'nullable|in:Bulanan,Per Ritase',
                'tarif_bulanan' => 'nullable|numeric|min:0',
                'kontak' => 'nullable|string',
                'alamat' => 'nullable|string',
            ]);

            if (($validated['jenis'] ?? '') !== 'Swasta') {
                $validated['tarif_bulanan'] = null;
            }

            $tenantId = auth()->user()->tenant_id;
            if (!$tenantId) {
                // If the user has no tenant (Superadmin), default to the first tenant to prevent 1048 constraint violation
                $firstTenant = \App\Models\Tenant::first();
                if ($firstTenant) {
                    $tenantId = $firstTenant->id;
                }
            }
            $validated['tenant_id'] = $tenantId;

            Klien::create($validated);

            return redirect()->route('admin.klien.index')->with('success', 'Klien berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5)->map(fn($t) => ($t['file'] ?? '') . ':' . ($t['line'] ?? ''))->toArray(),
            ], 200);
        }
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
            'jenis' => 'required|in:DLH,Swasta,Offtaker,Internal',
            'jenis_tarif' => 'nullable|in:Bulanan,Per Ritase',
            'tarif_bulanan' => 'nullable|numeric|min:0',
            'kontak' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        if (($validated['jenis'] ?? '') !== 'Swasta') {
            $validated['tarif_bulanan'] = null;
        }

        if (empty($klien->tenant_id)) {
            $tenantId = auth()->user()->tenant_id;
            if (!$tenantId) {
                $firstTenant = \App\Models\Tenant::first();
                if ($firstTenant) {
                    $tenantId = $firstTenant->id;
                }
            }
            $validated['tenant_id'] = $tenantId;
        }

        $klien->update($validated);

        return redirect()->route('admin.klien.index')->with('success', 'Klien berhasil diperbarui.');
    }

    public function destroy(Klien $klien)
    {
        Gate::authorize('delete_klien');
        $klien->delete();
        return redirect()->route('admin.klien.index')->with('success', 'Klien berhasil dihapus.');
    }

    public function show(Klien $klien)
    {
        Gate::authorize('view_klien');
        $klien->load('armada');
        return view('admin.klien.show', compact('klien'));
    }
}
