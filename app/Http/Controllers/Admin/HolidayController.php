<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_holiday');

        $query = Holiday::query();

        if ($request->filled('year')) {
            $query->whereYear('tanggal', $request->year);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $holidays = $query->orderBy('tanggal', 'desc')->paginate(15);
        $years = Holiday::selectRaw('YEAR(tanggal) as yr')->distinct()->orderBy('yr', 'desc')->pluck('yr');

        return view('admin.kpi.holidays.index', compact('holidays', 'years'));
    }

    public function create()
    {
        Gate::authorize('create_holiday');
        return view('admin.kpi.holidays.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create_holiday');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
        ]);

        Holiday::create($validated);

        return redirect()->route('admin.kpi.holidays.index')
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function edit(Holiday $holiday)
    {
        Gate::authorize('update_holiday');
        return view('admin.kpi.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        Gate::authorize('update_holiday');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
        ]);

        $holiday->update($validated);

        return redirect()->route('admin.kpi.holidays.index')
            ->with('success', 'Data hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $holiday)
    {
        Gate::authorize('delete_holiday');

        $holiday->delete();

        return redirect()->route('admin.kpi.holidays.index')
            ->with('success', 'Hari libur berhasil dihapus.');
    }
}
