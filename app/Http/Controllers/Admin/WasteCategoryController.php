<?php

namespace App\Http\Controllers\Admin;

use App\Models\WasteCategory;
use App\Models\WageRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WasteCategoryController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $categories = WasteCategory::where('tenant_id', $tenantId)
            ->with('wageRates')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.hrd.waste-category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.hrd.waste-category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        WasteCategory::create($validated);

        return redirect()->route('admin.hrd.waste-category.index')
            ->with('success', 'Kategori sampah berhasil ditambahkan');
    }

    public function edit(WasteCategory $wasteCategory)
    {
        $this->authorize('update', $wasteCategory);
        return view('admin.hrd.waste-category.edit', compact('wasteCategory'));
    }

    public function update(Request $request, WasteCategory $wasteCategory)
    {
        $this->authorize('update', $wasteCategory);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $wasteCategory->update($validated);

        return redirect()->route('admin.hrd.waste-category.index')
            ->with('success', 'Kategori sampah berhasil diperbarui');
    }

    public function destroy(WasteCategory $wasteCategory)
    {
        $this->authorize('delete', $wasteCategory);
        $wasteCategory->update(['is_active' => false]);

        return redirect()->route('admin.hrd.waste-category.index')
            ->with('success', 'Kategori sampah berhasil dinonaktifkan');
    }
}
