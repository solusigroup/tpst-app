<?php

namespace App\Http\Controllers\Admin;

use App\Models\WageCalculation;
use App\Models\EmployeeOutput;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class WageCalculationController extends Controller
{
    public function index(Request $request)
    {
        $query = WageCalculation::query();
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('week_start')) {
            $query->whereDate('week_start', '>=', $request->week_start);
        }

        $wages = $query->with('user')
            ->orderBy('week_start', 'desc')
            ->paginate(20);

        $usersQuery = User::role('karyawan');
        if (!auth()->user()->isSuperAdmin()) {
            $usersQuery->where('tenant_id', auth()->user()->tenant_id);
        }
        $users = $usersQuery->orderBy('name')->get();

        return view('admin.hrd.wage-calculation.index', compact('wages', 'users'));
    }

    public function show(WageCalculation $wageCalculation)
    {
        $this->authorize('view', $wageCalculation);

        $outputs = EmployeeOutput::where('user_id', $wageCalculation->user_id)
            ->where('tenant_id', $wageCalculation->tenant_id)
            ->whereBetween('output_date', [
                $wageCalculation->week_start,
                $wageCalculation->week_end
            ])
            ->with(['wasteCategory'])
            ->orderBy('output_date')
            ->get();

        $attendances = \App\Models\Attendance::where('user_id', $wageCalculation->user_id)
            ->where('tenant_id', $wageCalculation->tenant_id)
            ->whereBetween('attendance_date', [
                $wageCalculation->week_start,
                $wageCalculation->week_end
            ])
            ->orderBy('attendance_date')
            ->get();

        return view('admin.hrd.wage-calculation.show', compact('wageCalculation', 'outputs', 'attendances'));
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'week_start' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek();
        $tenantId = auth()->user()->tenant_id;

        if ($validated['user_id'] ?? null) {
            $users = User::role('karyawan')->where('id', $validated['user_id'])->get();
        } else {
            $usersQuery = User::role('karyawan');
            if (!auth()->user()->isSuperAdmin()) {
                $usersQuery->where('tenant_id', $tenantId);
            }
            $users = $usersQuery->get();
        }

        $count = 0;
        foreach ($users as $user) {
            WageCalculation::calculateForEmployee($user->id, $weekStart, $user->tenant_id);
            $count++;
        }

        return redirect()->route('admin.hrd.wage-calculation.index')
            ->with('success', "Upah untuk {$count} karyawan berhasil dihitung");
    }

    public function approve(WageCalculation $wageCalculation)
    {
        $this->authorize('update', $wageCalculation);

        $wageCalculation->update(['status' => 'approved']);

        return back()->with('success', 'Upah berhasil disetujui');
    }

    public function pay(Request $request, WageCalculation $wageCalculation)
    {
        $this->authorize('update', $wageCalculation);

        $validated = $request->validate([
            'paid_date' => 'required|date',
        ]);

        $wageCalculation->update([
            'status' => 'paid',
            'paid_date' => $validated['paid_date'],
        ]);

        return back()->with('success', 'Upah berhasil ditandai sebagai dibayar');
    }

    public function exportRekap(Request $request)
    {
        $query = WageCalculation::query();
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('week_start')) {
            $query->whereDate('week_start', '>=', $request->week_start);
        }

        $wages = $query->with('user')
            ->orderBy('week_start', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.hrd.wage-calculation.pdf-rekap', compact('wages'));
        
        return $pdf->stream('Rekap_Upah_Karyawan.pdf');
    }

    public function exportSlip(WageCalculation $wageCalculation)
    {
        $this->authorize('view', $wageCalculation);

        $outputs = EmployeeOutput::where('user_id', $wageCalculation->user_id)
            ->where('tenant_id', $wageCalculation->tenant_id)
            ->whereBetween('output_date', [
                $wageCalculation->week_start,
                $wageCalculation->week_end
            ])
            ->with(['wasteCategory'])
            ->orderBy('output_date')
            ->get();

        $pdf = Pdf::loadView('admin.hrd.wage-calculation.pdf-slip', compact('wageCalculation', 'outputs'));
        
        return $pdf->stream('Slip_Gaji_' . ($wageCalculation->user->name ?? 'Karyawan') . '_' . \Carbon\Carbon::parse($wageCalculation->week_start)->format('dmY') . '.pdf');
    }
}
