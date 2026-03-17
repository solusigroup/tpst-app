<?php

namespace App\Http\Controllers\Admin;

use App\Models\WageRate;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WageRateController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $rates = WageRate::where('tenant_id', $tenantId)
            ->with('wasteCategory')
            ->orderBy('effective_date', 'desc')
            ->paginate(20);

        return view('admin.hrd.wage-rate.index', compact('rates'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $categories = WasteCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.hrd.wage-rate.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'waste_category_id' => 'required|exists:waste_categories,id',
            'rate_per_unit' => 'required|numeric|min:0.01',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after:effective_date',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['is_active'] = true;

        WageRate::create($validated);

        return redirect()->route('admin.hrd.wage-rate.index')
            ->with('success', 'Tarif upah berhasil ditambahkan');
    }

    public function edit(WageRate $wageRate)
    {
        $this->authorize('update', $wageRate);
        $tenantId = auth()->user()->tenant_id;
        $categories = WasteCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.hrd.wage-rate.edit', compact('wageRate', 'categories'));
    }

    public function update(Request $request, WageRate $wageRate)
    {
        $this->authorize('update', $wageRate);

        $validated = $request->validate([
            'waste_category_id' => 'required|exists:waste_categories,id',
            'rate_per_unit' => 'required|numeric|min:0.01',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after:effective_date',
            'is_active' => 'boolean',
        ]);

        $wageRate->update($validated);

        return redirect()->route('admin.hrd.wage-rate.index')
            ->with('success', 'Tarif upah berhasil diperbarui');
    }

    public function destroy(WageRate $wageRate)
    {
        $this->authorize('delete', $wageRate);
        $wageRate->delete();

        return redirect()->route('admin.hrd.wage-rate.index')
            ->with('success', 'Tarif upah berhasil dihapus');
    }
}
