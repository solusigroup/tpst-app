<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeOutput;
use App\Models\WasteCategory;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeOutputController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = EmployeeOutput::where('tenant_id', $tenantId);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('waste_category_id')) {
            $query->where('waste_category_id', $request->waste_category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('output_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('output_date', '<=', $request->date_to);
        }

        $outputs = $query->with(['user', 'wasteCategory'])
            ->orderBy('output_date', 'desc')
            ->paginate(20);

        $users = User::where('tenant_id', $tenantId)->orderBy('name')->get();
        $categories = WasteCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.hrd.output.index', compact('outputs', 'users', 'categories'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $users = User::where('tenant_id', $tenantId)->orderBy('name')->get();
        $categories = WasteCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.hrd.output.create', compact('users', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'output_date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        EmployeeOutput::create($validated);

        return redirect()->route('admin.hrd.output.index')
            ->with('success', 'Output karyawan berhasil ditambahkan');
    }

    public function edit(EmployeeOutput $output)
    {
        $this->authorize('update', $output);
        $tenantId = auth()->user()->tenant_id;
        $users = User::where('tenant_id', $tenantId)->orderBy('name')->get();
        $categories = WasteCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.hrd.output.edit', compact('output', 'users', 'categories'));
    }

    public function update(Request $request, EmployeeOutput $output)
    {
        $this->authorize('update', $output);

        $validated = $request->validate([
            'waste_category_id' => 'required|exists:waste_categories,id',
            'output_date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $output->update($validated);

        return redirect()->route('admin.hrd.output.index')
            ->with('success', 'Output karyawan berhasil diperbarui');
    }

    public function destroy(EmployeeOutput $output)
    {
        $this->authorize('delete', $output);
        $output->delete();

        return redirect()->route('admin.hrd.output.index')
            ->with('success', 'Output karyawan berhasil dihapus');
    }
}
