<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('karyawan');
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('ktp_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('salary_type')) {
            $query->where('salary_type', $request->salary_type);
        }

        if ($request->export === 'pdf' || $request->export === 'excel') {
            $employees = $query->orderBy('name')->get();
            $data = compact('employees');

            if ($request->export === 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.hrd.employee.export', $data)
                    ->setPaper('a4', 'landscape');
                return $pdf->stream('Database_Karyawan_' . now()->format('Ymd') . '.pdf');
            } elseif ($request->export === 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\LaporanExcelExport('admin.hrd.employee.export', $data), 
                    'Database_Karyawan_' . now()->format('Ymd') . '.xlsx'
                );
            }
        }

        $employees = $query->orderBy('name')->paginate(20);

        return view('admin.hrd.employee.index', compact('employees'));
    }

    public function create()
    {
        $this->authorize('create_employee', User::class);
        return view('admin.hrd.employee.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_employee', User::class);
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'ktp_number' => 'nullable|string|max:50',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'salary_type' => 'nullable|in:bulanan,borongan,harian',
            'monthly_salary' => 'nullable|numeric|min:0',
            'daily_wage' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|in:Mingguan,Dua Mingguan',
            'photo' => 'nullable|image',
            'joined_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
            'bpjs_status' => 'required|in:Aktif,Tidak Aktif',
            'bpjs_number' => 'nullable|string|max:50',
        ]);
        
        if ($validated['salary_type'] !== 'bulanan') {
            $validated['monthly_salary'] = null;
        } else {
            $validated['payment_frequency'] = null;
        }

        if ($validated['salary_type'] !== 'harian') {
            $validated['daily_wage'] = null;
        }

        $validated['tenant_id'] = $tenantId;
        $validated['password'] = Hash::make('password123'); // Default password
        $validated['role'] = 'karyawan';

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $path;
        }

        $user = User::create($validated);
        
        // Ensure "karyawan" role exists minimally and assign
        $role = Role::firstOrCreate(['name' => 'karyawan']);
        $user->assignRole($role);

        return redirect()->route('admin.hrd.employee.index')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit(User $employee)
    {
        $this->authorize('update_employee', $employee);
        if (!auth()->user()->isSuperAdmin()) {
            abort_if($employee->tenant_id !== auth()->user()->tenant_id, 403, 'Unauthorized.');
        }
        return view('admin.hrd.employee.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $this->authorize('update_employee', $employee);
        if (!auth()->user()->isSuperAdmin()) {
            abort_if($employee->tenant_id !== auth()->user()->tenant_id, 403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($employee->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->id)],
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'ktp_number' => 'nullable|string|max:50',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'salary_type' => 'nullable|in:bulanan,borongan,harian',
            'monthly_salary' => 'nullable|numeric|min:0',
            'daily_wage' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|in:Mingguan,Dua Mingguan',
            'photo' => 'nullable|image',
            'joined_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
            'bpjs_status' => 'required|in:Aktif,Tidak Aktif',
            'bpjs_number' => 'nullable|string|max:50',
        ]);

        if ($validated['salary_type'] !== 'bulanan') {
            $validated['monthly_salary'] = null;
        } else {
            $validated['payment_frequency'] = null;
        }

        if ($validated['salary_type'] !== 'harian') {
            $validated['daily_wage'] = null;
        }

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $path = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $path;
        }

        $employee->update($validated);

        return redirect()->route('admin.hrd.employee.index')
            ->with('success', 'Data Karyawan berhasil diperbarui');
    }

    public function destroy(User $employee)
    {
        $this->authorize('delete_employee', $employee);
        if (!auth()->user()->isSuperAdmin()) {
            abort_if($employee->tenant_id !== auth()->user()->tenant_id, 403, 'Unauthorized.');
        }

        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee->delete();

        return redirect()->route('admin.hrd.employee.index')
            ->with('success', 'Karyawan berhasil dihapus');
    }
}
