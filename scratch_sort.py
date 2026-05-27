import os
import re

controller_path = 'app/Http/Controllers/Admin/LaporanController.php'
with open(controller_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replacements for controller
replacements = [
    # bukuBesar
    ("        $query = JurnalDetail::query()", "        $sortDate = $request->get('sort_date', 'asc');\n        $query = JurnalDetail::query()"),
    ("            ->orderBy('jurnal_header.tanggal')", "            ->orderBy('jurnal_header.tanggal', $sortDate)"),
    ("            $data = compact('rows', 'coas', 'dari', 'sampai', 'coaId', 'selectedCoa', 'saldoAwal', 'title');", "            $data = compact('rows', 'coas', 'dari', 'sampai', 'coaId', 'selectedCoa', 'saldoAwal', 'title', 'sortDate');"),
    ("        return view('admin.laporan.buku-besar', compact('rows', 'coas', 'dari', 'sampai', 'coaId', 'selectedCoa', 'saldoAwal', 'pageSaldoAwal', 'title'));", "        return view('admin.laporan.buku-besar', compact('rows', 'coas', 'dari', 'sampai', 'coaId', 'selectedCoa', 'saldoAwal', 'pageSaldoAwal', 'title', 'sortDate'));"),

    # laporanRitase
    ("        $query = Ritase::with(['armada', 'klien'])", "        $sortDate = $request->get('sort_date', 'desc');\n        $query = Ritase::with(['armada', 'klien'])"),
    ("            ->orderByDesc('ritase.waktu_masuk');", "            ->orderBy('ritase.waktu_masuk', $sortDate);"),
    ("            $data = compact('rows', 'kliens', 'dari', 'sampai', 'klienId', 'jenisKlien', 'status', 'isApproved', 'totals', 'rekapJenis');", "            $data = compact('rows', 'kliens', 'dari', 'sampai', 'klienId', 'jenisKlien', 'status', 'isApproved', 'totals', 'rekapJenis', 'sortDate');"),
    ("        return view('admin.laporan.ritase', compact('rows', 'kliens', 'dari', 'sampai', 'klienId', 'jenisKlien', 'status', 'isApproved', 'totals', 'rekapJenis'));", "        return view('admin.laporan.ritase', compact('rows', 'kliens', 'dari', 'sampai', 'klienId', 'jenisKlien', 'status', 'isApproved', 'totals', 'rekapJenis', 'sortDate'));"),

    # rekapRitase2
    ("        $baseQuery = Ritase::with(['klien'])", "        $sortDate = $request->get('sort_date', 'asc');\n        $baseQuery = Ritase::with(['klien'])"),
    ("            ->groupBy(DB::raw('DATE(waktu_masuk)'))\n            ->orderBy(DB::raw('DATE(waktu_masuk)'))", "            ->groupBy(DB::raw('DATE(waktu_masuk)'))\n            ->orderBy(DB::raw('DATE(waktu_masuk)'), $sortDate)"),
    ("        $data = compact('bulan', 'tahun', 'klienId', 'isApproved', 'klien', 'kliens', 'rekapHarian', 'grandTotalRitase', 'grandTotalNetto');", "        $data = compact('bulan', 'tahun', 'klienId', 'isApproved', 'klien', 'kliens', 'rekapHarian', 'grandTotalRitase', 'grandTotalNetto', 'sortDate');"),

    # laporanPenjualan
    ("        $query = Penjualan::with('klien')", "        $sortDate = $request->get('sort_date', 'desc');\n        $query = Penjualan::with('klien')"),
    ("            ->orderByDesc('tanggal');", "            ->orderBy('tanggal', $sortDate);"),
    ("            $data = compact('rows', 'dari', 'sampai', 'totals');", "            $data = compact('rows', 'dari', 'sampai', 'totals', 'sortDate');"),
    ("        return view('admin.laporan.penjualan', compact('rows', 'dari', 'sampai', 'totals'));", "        return view('admin.laporan.penjualan', compact('rows', 'dari', 'sampai', 'totals', 'sortDate'));"),

    # penjualanPerKlien
    ("        $query = Penjualan::with('klien')", "        $sortDate = $request->get('sort_date', 'asc');\n        $query = Penjualan::with('klien')"),
    ("        $penjualan = $query->orderBy('klien_id')\n            ->orderBy('tanggal')\n            ->get();", "        $penjualan = $query->orderBy('klien_id')\n            ->orderBy('tanggal', $sortDate)\n            ->get();"),
    ("        $data = compact('reports', 'dari', 'sampai', 'klienId', 'kliens');", "        $data = compact('reports', 'dari', 'sampai', 'klienId', 'kliens', 'sortDate');"),

    # laporanHasilPilahan
    ("        $query = HasilPilahan::with(['wasteCategory.wageRates'])", "        $sortDate = $request->get('sort_date', 'desc');\n        $query = HasilPilahan::with(['wasteCategory.wageRates'])"),
    ("            ->orderByDesc('tanggal');", "            ->orderBy('tanggal', $sortDate);"),
    ("            $data = compact('rows', 'dari', 'sampai', 'kategori', 'userId', 'totals', 'stokSummary', 'summaryTotals', 'employees');", "            $data = compact('rows', 'dari', 'sampai', 'kategori', 'userId', 'totals', 'stokSummary', 'summaryTotals', 'employees', 'sortDate');"),
    ("        return view('admin.laporan.hasil-pilahan', compact('rows', 'dari', 'sampai', 'kategori', 'userId', 'totals', 'stokSummary', 'summaryTotals', 'employees'));", "        return view('admin.laporan.hasil-pilahan', compact('rows', 'dari', 'sampai', 'kategori', 'userId', 'totals', 'stokSummary', 'summaryTotals', 'employees', 'sortDate'));"),

    # laporanResidu
    ("        $query = PengangkutanResidu::with('armada')", "        $sortDate = $request->get('sort_date', 'desc');\n        $query = PengangkutanResidu::with('armada')"),
    ("            ->orderByDesc('tanggal');", "            ->orderBy('tanggal', $sortDate);"),
    ("            $data = compact('rows', 'dari', 'sampai', 'totals');", "            $data = compact('rows', 'dari', 'sampai', 'totals', 'sortDate');"),
    ("        return view('admin.laporan.residu', compact('rows', 'dari', 'sampai', 'totals'));", "        return view('admin.laporan.residu', compact('rows', 'dari', 'sampai', 'totals', 'sortDate'));"),

    # laporanKehadiran
    ("        $query = \App\Models\Attendance::with('user')", "        $sortDate = $request->get('sort_date', 'desc');\n        $query = \App\Models\Attendance::with('user')"),
    ("            ->orderByDesc('attendance_date');", "            ->orderBy('attendance_date', $sortDate);"),
    ("                $data = compact('rekapData', 'users', 'dari', 'sampai', 'userId', 'salaryType', 'mode', 'month', 'year');", "                $data = compact('rekapData', 'users', 'dari', 'sampai', 'userId', 'salaryType', 'mode', 'month', 'year', 'sortDate');"),
    ("            return view('admin.laporan.attendance-rekap', compact('rekapData', 'users', 'dari', 'sampai', 'userId', 'salaryType', 'mode', 'month', 'year'));", "            return view('admin.laporan.attendance-rekap', compact('rekapData', 'users', 'dari', 'sampai', 'userId', 'salaryType', 'mode', 'month', 'year', 'sortDate'));"),
    ("            $data = compact('rows', 'users', 'dari', 'sampai', 'userId', 'totals');", "            $data = compact('rows', 'users', 'dari', 'sampai', 'userId', 'totals', 'sortDate');"),
    ("        return view('admin.laporan.attendance', compact('rows', 'users', 'dari', 'sampai', 'userId', 'salaryType', 'mode', 'month', 'year', 'totals'));", "        return view('admin.laporan.attendance', compact('rows', 'users', 'dari', 'sampai', 'userId', 'salaryType', 'mode', 'month', 'year', 'totals', 'sortDate'));"),

    # laporanUpah
    ("        $query = WageCalculation::with('user')", "        $sortDate = $request->get('sort_date', 'desc');\n        $query = WageCalculation::with('user')"),
    ("            ->orderByDesc('week_start');", "            ->orderBy('week_start', $sortDate);"),
    ("            $data = compact('rows', 'dari', 'sampai', 'month', 'year', 'skemaUpah', 'status', 'totals', 'title');", "            $data = compact('rows', 'dari', 'sampai', 'month', 'year', 'skemaUpah', 'status', 'totals', 'title', 'sortDate');"),
    ("        return view('admin.laporan.upah', compact('rows', 'dari', 'sampai', 'month', 'year', 'skemaUpah', 'status', 'totals', 'title'));", "        return view('admin.laporan.upah', compact('rows', 'dari', 'sampai', 'month', 'year', 'skemaUpah', 'status', 'totals', 'title', 'sortDate'));"),
]

for old, new in replacements:
    content = content.replace(old, new)

with open(controller_path, 'w', encoding='utf-8') as f:
    f.write(content)


views = [
    'buku-besar.blade.php',
    'ritase.blade.php',
    'rekap-ritase-2.blade.php',
    'penjualan.blade.php',
    'penjualan-per-klien.blade.php',
    'hasil-pilahan.blade.php',
    'residu.blade.php',
    'attendance.blade.php',
    'upah.blade.php'
]

select_html = '''        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Urutan Tanggal</label>
            <select name="sort_date" class="form-select">
                <option value="desc" {{ (isset($sortDate) && $sortDate == 'desc') ? 'selected' : '' }}>Terbaru (Descending)</option>
                <option value="asc" {{ (isset($sortDate) && $sortDate == 'asc') ? 'selected' : '' }}>Terlama (Ascending)</option>
            </select>
        </div>
'''

for view in views:
    path = f'resources/views/admin/laporan/{view}'
    if os.path.exists(path):
        with open(path, 'r', encoding='utf-8') as f:
            v_content = f.read()
        
        # we will insert select_html before the filter button.
        # find the filter button `<div class="col-auto"><button class="btn btn-primary" type="submit">`
        # or similar
        idx = v_content.find('<button class="btn btn-primary" type="submit">')
        if idx != -1:
            # find the div before it
            div_idx = v_content.rfind('<div class="col-auto">', 0, idx)
            if div_idx != -1:
                v_content = v_content[:div_idx] + select_html + v_content[div_idx:]
                with open(path, 'w', encoding='utf-8') as f:
                    f.write(v_content)
        
