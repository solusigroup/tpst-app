<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilPilahan;
use App\Models\User;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class HasilPilahanController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_hasil_pilahan');
        $query = HasilPilahan::with(['user', 'wasteCategory']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('jenis', 'like', '%' . $request->search . '%')
                  ->orWhere('officer', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $hasilPilahans = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('admin.hasil-pilahan.index', compact('hasilPilahans'));
    }

    public function create()
    {
        Gate::authorize('create_hasil_pilahan');

        // Waste categories (for jenis dropdown)
        $wasteCategories = WasteCategory::where('is_active', true)->orderBy('name')->get();

        // Petugas — karyawan borongan (for officer dropdown)
        $query = User::role('karyawan')->where('salary_type', 'borongan');
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }
        $petugas = $query->orderBy('name')->get();

        return view('admin.hasil-pilahan.form', compact('wasteCategories', 'petugas'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_hasil_pilahan');

        $validated = $request->validate([
            'tanggal'            => 'required|date',
            'kategori'           => 'required|in:Organik,Anorganik,B3,Residu',
            'waste_category_id'  => 'required|exists:waste_categories,id',
            'tonase'             => 'required|numeric|min:0',
            'jml_bal'            => 'nullable|integer|min:0',
            'user_id'            => 'required|exists:users,id',
            'keterangan'         => 'nullable|string|max:500',
        ]);

        // Auto-fill string fields from FK relations
        $user = User::find($validated['user_id']);
        $wasteCategory = WasteCategory::find($validated['waste_category_id']);

        $validated['officer'] = $user->name;
        $validated['jenis'] = $wasteCategory->name;

        DB::transaction(function () use ($validated) {
            HasilPilahan::create($validated);
        });

        return redirect()->route('admin.hasil-pilahan.index')->with('success', 'Hasil pilahan berhasil ditambahkan. Employee Output otomatis di-update.');
    }

    public function edit(HasilPilahan $hasilPilahan)
    {
        Gate::authorize('update_hasil_pilahan');

        $wasteCategories = WasteCategory::where('is_active', true)->orderBy('name')->get();

        $query = User::role('karyawan')->where('salary_type', 'borongan');
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }
        $petugas = $query->orderBy('name')->get();

        return view('admin.hasil-pilahan.form', compact('hasilPilahan', 'wasteCategories', 'petugas'));
    }

    public function update(Request $request, HasilPilahan $hasilPilahan)
    {
        Gate::authorize('update_hasil_pilahan');

        $validated = $request->validate([
            'tanggal'            => 'required|date',
            'kategori'           => 'required|in:Organik,Anorganik,B3,Residu',
            'waste_category_id'  => 'required|exists:waste_categories,id',
            'tonase'             => 'required|numeric|min:0',
            'jml_bal'            => 'nullable|integer|min:0',
            'user_id'            => 'required|exists:users,id',
            'keterangan'         => 'nullable|string|max:500',
        ]);

        // Auto-fill string fields from FK relations
        $user = User::find($validated['user_id']);
        $wasteCategory = WasteCategory::find($validated['waste_category_id']);

        $validated['officer'] = $user->name;
        $validated['jenis'] = $wasteCategory->name;

        // Capture old values for observer re-sync
        $oldUserId = $hasilPilahan->user_id;
        $oldWasteCategoryId = $hasilPilahan->waste_category_id;
        $oldTanggal = $hasilPilahan->tanggal;

        DB::transaction(function () use ($hasilPilahan, $validated, $oldUserId, $oldWasteCategoryId, $oldTanggal) {
            $hasilPilahan->update($validated);

            // If the user/category/date changed, we also need to re-sync the OLD combination
            if ($oldUserId && $oldWasteCategoryId && (
                $oldUserId != $validated['user_id'] ||
                $oldWasteCategoryId != $validated['waste_category_id'] ||
                $oldTanggal->toDateString() != $validated['tanggal']
            )) {
                $this->resyncOldCombination($hasilPilahan->tenant_id, $oldUserId, $oldWasteCategoryId, $oldTanggal);
            }
        });

        return redirect()->route('admin.hasil-pilahan.index')->with('success', 'Hasil pilahan berhasil diperbarui. Employee Output otomatis di-update.');
    }

    public function destroy(HasilPilahan $hasilPilahan)
    {
        Gate::authorize('delete_hasil_pilahan');
        DB::transaction(function () use ($hasilPilahan) {
            $hasilPilahan->delete();
        });
        return redirect()->route('admin.hasil-pilahan.index')->with('success', 'Hasil pilahan berhasil dihapus. Employee Output otomatis di-update.');
    }

    /**
     * Re-sync the old (user, waste_category, date) combination after an update changes those fields.
     */
    private function resyncOldCombination(int $tenantId, int $userId, int $wasteCategoryId, $tanggal): void
    {
        $totalTonase = HasilPilahan::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->where('waste_category_id', $wasteCategoryId)
            ->whereDate('tanggal', $tanggal)
            ->sum('tonase');

        if ($totalTonase > 0) {
            \App\Models\EmployeeOutput::updateOrCreate(
                [
                    'tenant_id'         => $tenantId,
                    'user_id'           => $userId,
                    'waste_category_id' => $wasteCategoryId,
                    'output_date'       => $tanggal,
                ],
                [
                    'quantity' => $totalTonase,
                    'unit'     => 'kg',
                    'notes'    => 'Auto-sync dari Hasil Pilahan',
                ]
            );
        } else {
            \App\Models\EmployeeOutput::where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->where('waste_category_id', $wasteCategoryId)
                ->whereDate('output_date', $tanggal)
                ->delete();
        }
    }
}
