<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        try {
            Gate::authorize('view_vendor');
            $query = Vendor::query();

            if ($request->filled('search')) {
                $query->where('nama_vendor', 'like', '%' . $request->search . '%');
            }

            $vendors = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

            return view('admin.vendor.index', compact('vendors'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        Gate::authorize('create_vendor');
        return view('admin.vendor.form');
    }

    public function store(Request $request)
    {
        try {
            Gate::authorize('create_vendor');
            
            $validated = $request->validate([
                'nama_vendor' => 'required|string|max:255',
                'kontak' => 'nullable|string',
                'alamat' => 'nullable|string',
            ]);

            $tenantId = auth()->user()->tenant_id;
            if (!$tenantId) {
                $firstTenant = \App\Models\Tenant::first();
                if ($firstTenant) {
                    $tenantId = $firstTenant->id;
                }
            }
            $validated['tenant_id'] = $tenantId;

            Vendor::create($validated);

            return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Vendor $vendor)
    {
        Gate::authorize('update_vendor');
        return view('admin.vendor.form', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        Gate::authorize('update_vendor');
        
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'kontak' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $vendor->update($validated);

        return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        Gate::authorize('delete_vendor');
        $vendor->delete();
        return redirect()->route('admin.vendor.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
