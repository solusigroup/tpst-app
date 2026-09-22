<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterAsalSampah;
use App\Models\Klien;
use App\Models\Ritase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class MasterAsalSampahController extends Controller
{
    /**
     * Tampilkan daftar Master Asal Sampah
     */
    public function index(Request $request)
    {
        if (Gate::has('view_klien')) {
            Gate::authorize('view_klien');
        }

        $query = MasterAsalSampah::with('klien');

        if ($request->filled('klien_id')) {
            $query->where('klien_id', $request->klien_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_asal_sampah', 'like', '%' . $search . '%')
                  ->orWhereHas('klien', function ($kq) use ($search) {
                      $kq->where('nama_klien', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $asalSampahList = $query->orderBy('klien_id')
            ->orderBy('nama_asal_sampah')
            ->paginate(20)
            ->withQueryString();

        // Hitung total penggunaan di tabel ritase untuk masing-masing item pada page aktif
        $usageCounts = Ritase::select('klien_id', 'jenis_sampah', DB::raw('count(*) as count'))
            ->groupBy('klien_id', 'jenis_sampah')
            ->get()
            ->keyBy(function ($item) {
                return $item->klien_id . '_' . $item->jenis_sampah;
            });

        foreach ($asalSampahList as $item) {
            $key = $item->klien_id . '_' . $item->nama_asal_sampah;
            $item->total_ritase = $usageCounts[$key]->count ?? 0;
        }

        $kliens = Klien::orderBy('nama_klien')->get();

        return view('admin.master_asal_sampah.index', compact('asalSampahList', 'kliens'));
    }

    /**
     * Simpan Asal Sampah baru ke Master Data
     */
    public function store(Request $request)
    {
        if (Gate::has('create_klien')) {
            Gate::authorize('create_klien');
        }

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'nama_asal_sampah' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $normalizedName = preg_replace('/\s+/', ' ', trim($validated['nama_asal_sampah']));
        $tenantId = auth()->user()->getEffectiveTenantId();

        $existing = MasterAsalSampah::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('klien_id', $validated['klien_id'])
            ->where('nama_asal_sampah', $normalizedName)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', "Asal sampah \"{$normalizedName}\" sudah terdaftar untuk klien ini.");
        }

        MasterAsalSampah::create([
            'tenant_id' => $tenantId,
            'klien_id' => $validated['klien_id'],
            'nama_asal_sampah' => $normalizedName,
            'kategori' => $validated['kategori'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', "Titik asal sampah \"{$normalizedName}\" berhasil ditambahkan.");
    }

    /**
     * Perbarui data Master Asal Sampah
     */
    public function update(Request $request, MasterAsalSampah $masterAsalSampah)
    {
        if (Gate::has('update_klien')) {
            Gate::authorize('update_klien');
        }

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'nama_asal_sampah' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'update_history' => 'nullable|boolean',
        ]);

        $oldName = $masterAsalSampah->nama_asal_sampah;
        $oldKlienId = $masterAsalSampah->klien_id;
        $newName = preg_replace('/\s+/', ' ', trim($validated['nama_asal_sampah']));
        $newKlienId = $validated['klien_id'];

        DB::transaction(function () use ($masterAsalSampah, $validated, $oldName, $oldKlienId, $newName, $newKlienId, $request) {
            $masterAsalSampah->update([
                'klien_id' => $newKlienId,
                'nama_asal_sampah' => $newName,
                'kategori' => $validated['kategori'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Jika user memilih untuk menyelaraskan riwayat transaksi ritase lama
            if ($request->boolean('update_history') && ($oldName !== $newName || $oldKlienId != $newKlienId)) {
                Ritase::withoutGlobalScopes()
                    ->where('klien_id', $oldKlienId)
                    ->where('jenis_sampah', $oldName)
                    ->update([
                        'jenis_sampah' => $newName,
                        'klien_id' => $newKlienId,
                    ]);
            }
        });

        return redirect()->back()->with('success', "Data asal sampah \"{$newName}\" berhasil diperbarui.");
    }

    /**
     * Hapus titik Master Asal Sampah
     */
    public function destroy(MasterAsalSampah $masterAsalSampah)
    {
        if (Gate::has('delete_klien')) {
            Gate::authorize('delete_klien');
        }

        $name = $masterAsalSampah->nama_asal_sampah;
        $masterAsalSampah->delete();

        return redirect()->back()->with('success', "Asal sampah \"{$name}\" berhasil dihapus dari Master Data.");
    }

    /**
     * Gabungkan (Merge) titik asal sampah duplikat / typo ke satu titik utama
     */
    public function merge(Request $request)
    {
        if (Gate::has('update_klien')) {
            Gate::authorize('update_klien');
        }

        $request->validate([
            'target_id' => 'required|exists:master_asal_sampah,id',
            'source_id' => 'required|exists:master_asal_sampah,id|different:target_id',
        ]);

        $target = MasterAsalSampah::findOrFail($request->target_id);
        $source = MasterAsalSampah::findOrFail($request->source_id);

        DB::transaction(function () use ($target, $source) {
            // Update seluruh ritase dari source ke target
            $updatedCount = Ritase::withoutGlobalScopes()
                ->where('klien_id', $source->klien_id)
                ->where('jenis_sampah', $source->nama_asal_sampah)
                ->update([
                    'jenis_sampah' => $target->nama_asal_sampah,
                    'klien_id' => $target->klien_id,
                ]);

            // Hapus master source yang typo/duplikat
            $sourceName = $source->nama_asal_sampah;
            $source->delete();

            session()->flash('merge_count', $updatedCount);
            session()->flash('source_name', $sourceName);
        });

        $count = session('merge_count', 0);
        $sourceName = session('source_name');

        return redirect()->back()->with('success', "Berhasil menggabungkan \"{$sourceName}\" ke dalam \"{$target->nama_asal_sampah}\". Sebanyak {$count} transaksi ritase lama telah diselaraskan.");
    }
}
