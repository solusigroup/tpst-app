<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::query();

        // If the logged-in user is a monthly-salary employee, restrict the listing
        // to only their own attendances regardless of request parameters.
        if (auth()->check() && auth()->user()->salary_type === 'bulanan') {
            $request->merge(['user_id' => auth()->id()]);
        }
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->with('user')
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        $usersQuery = User::role('karyawan');
        if (!auth()->user()->isSuperAdmin()) {
            $usersQuery->where('tenant_id', auth()->user()->tenant_id);
        }
        $users = $usersQuery->orderBy('name')->get();

        return view('admin.hrd.attendance.index', compact('attendances', 'users'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $usersQuery = User::role('karyawan');
        if (!auth()->user()->isSuperAdmin()) {
            $usersQuery->where('tenant_id', auth()->user()->tenant_id);
        }
        $users = $usersQuery->orderBy('name')->get();
        return view('admin.hrd.attendance.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,sick,leave',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        Attendance::create($validated);

        return redirect()->route('admin.hrd.attendance.index')
            ->with('success', 'Kehadiran berhasil ditambahkan');
    }

    public function edit(Attendance $attendance)
    {
        $this->authorize('edit', $attendance);
        $tenantId = auth()->user()->tenant_id;
        $usersQuery = User::role('karyawan');
        if (!auth()->user()->isSuperAdmin()) {
            $usersQuery->where('tenant_id', auth()->user()->tenant_id);
        }
        $users = $usersQuery->orderBy('name')->get();
        return view('admin.hrd.attendance.edit', compact('attendance', 'users'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $this->authorize('update', $attendance);

        $validated = $request->validate([
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,sick,leave',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return redirect()->route('admin.hrd.attendance.index')
            ->with('success', 'Kehadiran berhasil diperbarui');
    }

    public function destroy(Attendance $attendance)
    {
        $this->authorize('delete', $attendance);
        $attendance->delete();

        return redirect()->route('admin.hrd.attendance.index')
            ->with('success', 'Kehadiran berhasil dihapus');
    }

    public function quickCheckIn(Request $request, User $user)
    {
        $this->authorize('quickCheckIn', Attendance::class);

        $tenantId = auth()->user()->tenant_id;
        $today = now()->toDateString();

        $attendance = Attendance::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'user_id' => $user->id,
                'attendance_date' => $today,
            ],
            [
                'status' => 'present',
            ]
        );

        $attendance->update(['check_in' => now()->format('H:i:s')]);

        return back()->with('success', 'Check-in berhasil: ' . $user->name);
    }

    public function quickCheckOut(Request $request, User $user)
    {
        $this->authorize('quickCheckOut', Attendance::class);

        $tenantId = auth()->user()->tenant_id;
        $today = now()->toDateString();

        $attendance = Attendance::where('tenant_id', $tenantId)
            ->where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->firstOrFail();

        $attendance->update(['check_out' => now()->format('H:i:s')]);

        return back()->with('success', 'Check-out berhasil: ' . $user->name);
    }
}
