<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CentralUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tenantId = $request->get('tenant_id');
        $role = $request->get('role');

        $users = User::withoutGlobalScopes()
            ->with('tenant')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($role, fn($q) => $q->where('role', $role))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $tenants = Tenant::orderBy('name')->get();

        return view('central.users.index', compact('users', 'tenants'));
    }

    public function create()
    {
        $tenants = Tenant::orderBy('name')->get();
        return view('central.users.form', compact('tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,timbangan,keuangan',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['is_super_admin'] = $request->has('is_super_admin');

        $user = User::withoutGlobalScopes()->create($data);

        return redirect()->route('central.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::withoutGlobalScopes()->findOrFail($id);
        $tenants = Tenant::orderBy('name')->get();
        return view('central.users.form', compact('user', 'tenants'));
    }

    public function update(Request $request, $id)
    {
        $user = User::withoutGlobalScopes()->findOrFail($id);
        
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8',
            'role' => 'required|in:admin,timbangan,keuangan',
        ]);

        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $data['is_super_admin'] = $request->has('is_super_admin');

        $user->update($data);

        return redirect()->route('central.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::withoutGlobalScopes()->findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun Anda sendiri.');
        }
        $user->delete();
        return redirect()->route('central.users.index')->with('success', 'User berhasil dihapus.');
    }
}
