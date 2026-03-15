<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('view_users');

        // Get all roles except super_admin (which cannot be modified/deleted normally)
        $roles = Role::where('name', '!=', 'super_admin')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        Gate::authorize('view_users');

        $permissions = Permission::all()->groupBy(function($perm) {
            return explode('_', $perm->name, 2)[1] ?? 'other'; // Grupping based on suffix, e.g. "klien" from "view_klien"
        });

        return view('admin.roles.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('view_users');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dibuat.');
    }

    public function edit(Role $role)
    {
        Gate::authorize('view_users');

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $permissions = Permission::all()->groupBy(function($perm) {
            return explode('_', $perm->name, 2)[1] ?? 'other';
        });

        // Current permissions applied to this role
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.form', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize('view_users');

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $validated['name']]);

        // Sync strictly what is submitted
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            // If empty array, it removes all permissions
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('view_users');

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Super Admin tidak dapat dihapus.');
        }

        // Prevent deleting a role that is currently in use by any user
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh beberapa pengguna.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
