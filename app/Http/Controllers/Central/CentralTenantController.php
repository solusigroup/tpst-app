<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CentralTenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $tenants = Tenant::withCount('users')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('domain', 'like', "%{$search}%"))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('central.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('central.tenants.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'admin_name' => 'required|string|max:255',
            'admin_username' => 'nullable|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|min:8',
        ]);

        try {
            DB::beginTransaction();
            
            // Create tenant
            $tenant = Tenant::create([
                'name' => $request->name,
                'domain' => $request->domain,
            ]);

            // Create admin user for this tenant
            $admin = User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->admin_name,
                'username' => $request->admin_username,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'admin',
            ]);

            DB::commit();
            return redirect()->route('central.tenants.index')->with('success', 'Tenant dan admin berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat tenant: ' . $e->getMessage());
        }
    }

    public function edit(Tenant $tenant)
    {
        return view('central.tenants.form', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $tenant->id,
        ]);

        $tenant->update($request->only('name', 'domain'));
        return redirect()->route('central.tenants.index')->with('success', 'Data tenant berhasil diperbarui.');
    }

    public function destroy(Tenant $tenant)
    {
        // Delete all related data or simply rely on cascade if DB is set up that way
        $tenant->delete();
        return redirect()->route('central.tenants.index')->with('success', 'Tenant berhasil dihapus.');
    }
}
