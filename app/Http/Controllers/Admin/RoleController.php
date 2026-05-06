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
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

        $permissions = Permission::all()->groupBy(function($perm) {
            // Group all laporan operasional sub-permissions together
            $laporanOpPerms = [
                'view_laporan_operasional',
                'view_laporan_ritase', 'view_laporan_rekap_ritase', 'view_laporan_rekap_ritase_2',
                'view_laporan_penjualan_op', 'view_laporan_hasil_pilahan',
                'view_laporan_residu', 'view_laporan_kehadiran', 'view_laporan_upah',
            ];
            if (in_array($perm->name, $laporanOpPerms)) {
                return 'laporan_operasional';
            }
            return explode('_', $perm->name, 2)[1] ?? 'other';
        });

        // Human-readable labels for permissions
        $permLabels = $this->getPermissionLabels();

        return view('admin.roles.form', compact('permissions', 'permLabels'));
    }

    public function store(Request $request)
    {
        Gate::authorize('view_users');
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

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
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $permissions = Permission::all()->groupBy(function($perm) {
            $laporanOpPerms = [
                'view_laporan_operasional',
                'view_laporan_ritase', 'view_laporan_rekap_ritase', 'view_laporan_rekap_ritase_2',
                'view_laporan_penjualan_op', 'view_laporan_hasil_pilahan',
                'view_laporan_residu', 'view_laporan_kehadiran', 'view_laporan_upah',
            ];
            if (in_array($perm->name, $laporanOpPerms)) {
                return 'laporan_operasional';
            }
            return explode('_', $perm->name, 2)[1] ?? 'other';
        });

        // Current permissions applied to this role
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permLabels = $this->getPermissionLabels();

        return view('admin.roles.form', compact('role', 'permissions', 'rolePermissions', 'permLabels'));
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize('view_users');
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

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
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

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

    /**
     * Human-readable labels for permissions (especially granular laporan operasional).
     */
    private function getPermissionLabels(): array
    {
        return [
            'view_laporan_operasional'    => 'Semua Laporan Operasional',
            'view_laporan_ritase'         => 'Laporan Ritase',
            'view_laporan_rekap_ritase'   => 'Rekap Ritase',
            'view_laporan_rekap_ritase_2' => 'Rekap Ritase II',
            'view_laporan_penjualan_op'   => 'Laporan Penjualan',
            'view_laporan_hasil_pilahan'  => 'Laporan Hasil Pilahan',
            'view_laporan_residu'         => 'Laporan Residu',
            'view_laporan_kehadiran'      => 'Laporan Kehadiran',
            'view_laporan_upah'           => 'Laporan Upah',
        ];
    }
}
