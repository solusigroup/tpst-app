<?php

namespace App\Services;

use App\Models\Ritase;
use App\Models\PengangkutanResidu;
use App\Models\HasilPilahan;
use App\Models\Penjualan;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DataAnomalyDetector
{
    /**
     * Run all anomaly detection routines and return a unified collection.
     */
    public function detectAll(array $filters = []): Collection
    {
        $anomalies = collect();

        $anomalies = $anomalies->merge($this->detectRitase());
        $anomalies = $anomalies->merge($this->detectResidu());
        $anomalies = $anomalies->merge($this->detectHasilPilahan());
        $anomalies = $anomalies->merge($this->detectPenjualan());
        $anomalies = $anomalies->merge($this->detectStockDeficits());

        // Apply filters
        if (!empty($filters['module']) && $filters['module'] !== 'all') {
            $anomalies = $anomalies->where('module_key', $filters['module']);
        }

        if (!empty($filters['severity']) && $filters['severity'] !== 'all') {
            $anomalies = $anomalies->where('severity', $filters['severity']);
        }

        if (!empty($filters['start_date'])) {
            $anomalies = $anomalies->filter(function ($item) use ($filters) {
                return empty($item['date_raw']) || $item['date_raw'] >= $filters['start_date'];
            });
        }

        if (!empty($filters['end_date'])) {
            $anomalies = $anomalies->filter(function ($item) use ($filters) {
                return empty($item['date_raw']) || $item['date_raw'] <= $filters['end_date'];
            });
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $anomalies = $anomalies->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['ref_number'] ?? ''), $search)
                    || str_contains(strtolower($item['title'] ?? ''), $search)
                    || str_contains(strtolower($item['description'] ?? ''), $search);
            });
        }

        // Sort by severity (danger first, then warning, then info), then by date descending
        $severityWeight = ['danger' => 0, 'warning' => 1, 'info' => 2];
        return $anomalies->sort(function ($a, $b) use ($severityWeight) {
            $wA = $severityWeight[$a['severity']] ?? 3;
            $wB = $severityWeight[$b['severity']] ?? 3;
            if ($wA !== $wB) {
                return $wA <=> $wB;
            }
            return ($b['date_raw'] ?? '') <=> ($a['date_raw'] ?? '');
        })->values();
    }

    /**
     * Detect anomalies in Ritase (Penerimaan Sampah).
     */
    public function detectRitase(): Collection
    {
        $items = collect();
        $ritases = Ritase::with(['armada', 'klien'])->get();
        $today = Carbon::today();

        foreach ($ritases as $r) {
            $tgl = $r->waktu_masuk ? $r->waktu_masuk->format('Y-m-d') : null;
            $tglDisplay = $r->waktu_masuk ? $r->waktu_masuk->format('d/m/Y H:i') : '-';
            $editUrl = route('admin.ritase.edit', $r->id);
            $tiket = $r->nomor_tiket ?: ($r->tiket ?: 'ID #' . $r->id);

            // 1. Netto <= 0 kg (Bruto <= Tarra)
            if (($r->berat_netto ?? 0) <= 0 || ($r->berat_bruto <= $r->berat_tarra)) {
                $items->push([
                    'id' => 'ritase_netto_' . $r->id,
                    'module' => 'Ritase',
                    'module_key' => 'ritase',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Netto Ritase Negatif atau 0 kg',
                    'description' => "Netto tercatat {$r->berat_netto} kg (Bruto: " . number_format($r->berat_bruto, 2, ',', '.') . " kg, Tarra: " . number_format($r->berat_tarra, 2, ',', '.') . " kg). Kendaraan/muatan tidak menghasilkan berat bersih.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 2. Asal Sampah Kosong / Simbol strip
            $trimmedJenis = trim($r->jenis_sampah ?? '');
            if (empty($trimmedJenis) || in_array($trimmedJenis, ['-', '--', '---', 'n/a', 'N/A', 'null', 'NULL', '?'])) {
                $items->push([
                    'id' => 'ritase_asal_' . $r->id,
                    'module' => 'Ritase',
                    'module_key' => 'ritase',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Asal Sampah Tidak Terisi',
                    'description' => "Kolom jenis / asal sampah kosong atau hanya berupa simbol tanda hubung strip. Sumber sampah tidak dapat diidentifikasi.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 3. Tanpa Keterangan
            $trimmedKet = trim($r->keterangan ?? '');
            if (empty($trimmedKet) || in_array($trimmedKet, ['-', '--'])) {
                $items->push([
                    'id' => 'ritase_ket_' . $r->id,
                    'module' => 'Ritase',
                    'module_key' => 'ritase',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Keterangan Ritase Kosong',
                    'description' => "Entri ritase ini tidak memiliki keterangan operasional yang tercatat.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 4. Waktu Keluar Lebih Awal dari Waktu Masuk
            if ($r->waktu_keluar && $r->waktu_masuk && $r->waktu_keluar->lt($r->waktu_masuk)) {
                $items->push([
                    'id' => 'ritase_waktu_' . $r->id,
                    'module' => 'Ritase',
                    'module_key' => 'ritase',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Urutan Jam Timbang Tidak Logis',
                    'description' => "Waktu keluar ({$r->waktu_keluar->format('d/m/Y H:i')}) mendahului waktu masuk ({$r->waktu_masuk->format('d/m/Y H:i')}).",
                    'edit_url' => $editUrl,
                ]);
            }

            // 5. Tonase Ekstrem (> 30.000 kg / 30 ton per ritase)
            if (($r->berat_netto ?? 0) > 30000) {
                $items->push([
                    'id' => 'ritase_ekstrem_' . $r->id,
                    'module' => 'Ritase',
                    'module_key' => 'ritase',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Tonase Ritase Ekstrem (> 30 Ton)',
                    'description' => "Netto tercatat " . number_format($r->berat_netto, 2, ',', '.') . " kg. Melebihi kapasitas muatan wajar truk pengangkut sampah standar.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 6. Tanggal di masa depan
            if ($r->waktu_masuk && $r->waktu_masuk->isFuture()) {
                $items->push([
                    'id' => 'ritase_future_' . $r->id,
                    'module' => 'Ritase',
                    'module_key' => 'ritase',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Tanggal Ritase di Masa Depan',
                    'description' => "Tanggal ritase ({$r->waktu_masuk->format('d/m/Y')}) melampaui hari ini. Kemungkinan salah ketik tahun/tanggal.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 7. Ritase Disetujui padahal Netto <= 0
            if ($r->is_approved && ($r->berat_netto ?? 0) <= 0) {
                $items->push([
                    'id' => 'ritase_approved_zero_' . $r->id,
                    'module' => 'Ritase',
                    'module_key' => 'ritase',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Ritase Netto 0 kg Telah Disetujui',
                    'description' => "Data ritase ini telah disetujui (Approved) padahal berat nettonya 0 atau negatif. Perlu ditinjau ulang.",
                    'edit_url' => $editUrl,
                ]);
            }
        }

        return $items;
    }

    /**
     * Detect anomalies in Pengangkutan Residu.
     */
    public function detectResidu(): Collection
    {
        $items = collect();
        $residus = PengangkutanResidu::with('armada')->get();

        foreach ($residus as $pr) {
            $tgl = $pr->tanggal ? $pr->tanggal->format('Y-m-d') : null;
            $tglDisplay = $pr->tanggal ? $pr->tanggal->format('d/m/Y') : '-';
            $editUrl = route('admin.pengangkutan-residu.edit', $pr->id);
            $tiket = $pr->nomor_tiket ?: ('ID #' . $pr->id);

            // 1. Netto <= 0 kg (Bruto <= Tarra)
            if (($pr->berat_netto ?? 0) <= 0 || ($pr->berat_bruto <= $pr->berat_tarra)) {
                $items->push([
                    'id' => 'residu_netto_' . $pr->id,
                    'module' => 'Pengangkutan Residu',
                    'module_key' => 'residu',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Netto Residu Negatif atau 0 kg',
                    'description' => "Netto residu tercatat {$pr->berat_netto} kg (Bruto: " . number_format($pr->berat_bruto, 2, ',', '.') . " kg, Tarra: " . number_format($pr->berat_tarra, 2, ',', '.') . " kg). Truk tidak mengangkut residu secara riil.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 2. Biaya retribusi negatif
            if (($pr->biaya_retribusi ?? 0) < 0) {
                $items->push([
                    'id' => 'residu_biaya_' . $pr->id,
                    'module' => 'Pengangkutan Residu',
                    'module_key' => 'residu',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Biaya Retribusi Residu Negatif',
                    'description' => "Biaya retribusi bernilai Rp " . number_format($pr->biaya_retribusi, 0, ',', '.') . ". Nilai biaya tidak boleh negatif.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 3. Armada tidak ada
            if (empty($pr->armada_id) || !$pr->armada) {
                $items->push([
                    'id' => 'residu_armada_' . $pr->id,
                    'module' => 'Pengangkutan Residu',
                    'module_key' => 'residu',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Armada Residu Belum Dipilih',
                    'description' => "Data pengangkutan residu belum terhubung dengan unit armada pengangkut.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 4. Tanggal di masa depan
            if ($pr->tanggal && $pr->tanggal->isFuture()) {
                $items->push([
                    'id' => 'residu_future_' . $pr->id,
                    'module' => 'Pengangkutan Residu',
                    'module_key' => 'residu',
                    'ref_number' => $tiket,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Tanggal Residu di Masa Depan',
                    'description' => "Tanggal pengangkutan residu ({$pr->tanggal->format('d/m/Y')}) melampaui hari ini.",
                    'edit_url' => $editUrl,
                ]);
            }
        }

        return $items;
    }

    /**
     * Detect anomalies in Hasil Pilahan.
     */
    public function detectHasilPilahan(): Collection
    {
        $items = collect();
        $pilahans = HasilPilahan::with(['wasteCategory', 'user'])->get();

        foreach ($pilahans as $hp) {
            $tgl = $hp->tanggal ? $hp->tanggal->format('Y-m-d') : null;
            $tglDisplay = $hp->tanggal ? $hp->tanggal->format('d/m/Y') : '-';
            $editUrl = route('admin.hasil-pilahan.edit', $hp->id);
            $namaProduk = $hp->jenis ?: ($hp->wasteCategory->name ?? 'Pilahan ID #' . $hp->id);

            // 1. Tonase <= 0 kg
            if (($hp->tonase ?? 0) <= 0) {
                $items->push([
                    'id' => 'pilahan_tonase_' . $hp->id,
                    'module' => 'Hasil Pilahan',
                    'module_key' => 'hasil_pilahan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Tonase Hasil Pilahan $\le$ 0 kg',
                    'description' => "Hasil pilahan dicatat dengan tonase {$hp->tonase} kg. Hasil pemilahan harus memiliki berat positif.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 2. Jumlah bal kosong atau <= 0
            if (empty($hp->jml_bal) || $hp->jml_bal <= 0) {
                $items->push([
                    'id' => 'pilahan_bal_' . $hp->id,
                    'module' => 'Hasil Pilahan',
                    'module_key' => 'hasil_pilahan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Jumlah Bal Kosong atau 0',
                    'description' => "Entri hasil pilahan {$namaProduk} belum mencantumkan jumlah bal hasil pemadatan/packing (tercatat: " . ($hp->jml_bal ?? 'null') . ").",
                    'edit_url' => $editUrl,
                ]);
            }

            // 3. Petugas pemilah kosong
            if (empty($hp->user_id)) {
                $items->push([
                    'id' => 'pilahan_user_' . $hp->id,
                    'module' => 'Hasil Pilahan',
                    'module_key' => 'hasil_pilahan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Petugas Pemilah Belum Ditentukan',
                    'description' => "Data output pilahan belum dihubungkan ke karyawan/petugas pemilah, sehingga perhitungan upah borongan tidak dapat diproses.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 4. Rasio berat per bal tidak wajar (> 1.000 kg/bal atau < 0.5 kg/bal)
            if (($hp->jml_bal ?? 0) > 0 && ($hp->tonase ?? 0) > 0) {
                $kgPerBal = $hp->tonase / $hp->jml_bal;
                if ($kgPerBal > 1000 || $kgPerBal < 0.5) {
                    $items->push([
                        'id' => 'pilahan_ratio_' . $hp->id,
                        'module' => 'Hasil Pilahan',
                        'module_key' => 'hasil_pilahan',
                        'ref_number' => $namaProduk,
                        'date' => $tglDisplay,
                        'date_raw' => $tgl,
                        'severity' => 'warning',
                        'title' => 'Rasio Berat per Bal Tidak Lazim',
                        'description' => "Tercatat {$hp->tonase} kg dalam {$hp->jml_bal} bal (rerata: " . number_format($kgPerBal, 2, ',', '.') . " kg/bal). Kemungkinan ada kesalahan input angka bal atau tonase.",
                        'edit_url' => $editUrl,
                    ]);
                }
            }

            // 5. Tanggal di masa depan
            if ($hp->tanggal && $hp->tanggal->isFuture()) {
                $items->push([
                    'id' => 'pilahan_future_' . $hp->id,
                    'module' => 'Hasil Pilahan',
                    'module_key' => 'hasil_pilahan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Tanggal Pilahan di Masa Depan',
                    'description' => "Tanggal hasil pilahan ({$hp->tanggal->format('d/m/Y')}) melampaui hari ini.",
                    'edit_url' => $editUrl,
                ]);
            }
        }

        return $items;
    }

    /**
     * Detect anomalies in Penjualan (Penjualan Hasil Olahan).
     */
    public function detectPenjualan(): Collection
    {
        $items = collect();
        $penjualans = Penjualan::with(['klien', 'wasteCategory'])->get();

        foreach ($penjualans as $pj) {
            $tgl = $pj->tanggal ? $pj->tanggal->format('Y-m-d') : null;
            $tglDisplay = $pj->tanggal ? $pj->tanggal->format('d/m/Y') : '-';
            $editUrl = route('admin.penjualan.edit', $pj->id);
            $namaProduk = $pj->jenis_produk ?: ('Penjualan ID #' . $pj->id);

            // 1. Klien bukan berjenis Offtaker atau Klien Kosong
            if (empty($pj->klien_id) || !$pj->klien) {
                $items->push([
                    'id' => 'penjualan_klien_empty_' . $pj->id,
                    'module' => 'Penjualan',
                    'module_key' => 'penjualan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Klien Pembeli Tidak Tercatat',
                    'description' => "Entri penjualan tidak memiliki data klien pembeli.",
                    'edit_url' => $editUrl,
                ]);
            } elseif ($pj->klien->jenis !== 'Offtaker') {
                $items->push([
                    'id' => 'penjualan_non_offtaker_' . $pj->id,
                    'module' => 'Penjualan',
                    'module_key' => 'penjualan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Klien Pembeli Bukan Jenis Offtaker',
                    'description' => "Penjualan dicatat ke klien '{$pj->klien->nama_klien}' yang berstatus '{$pj->klien->jenis}'. Penjualan hasil olahan wajib ditujukan ke klien jenis 'Offtaker'.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 2. Berat kg <= 0
            if (($pj->berat_kg ?? 0) <= 0) {
                $items->push([
                    'id' => 'penjualan_berat_' . $pj->id,
                    'module' => 'Penjualan',
                    'module_key' => 'penjualan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Berat Barang Penjualan $\le$ 0 kg',
                    'description' => "Transaksi penjualan {$namaProduk} tercatat dengan berat {$pj->berat_kg} kg.",
                    'edit_url' => $editUrl,
                ]);
            }

            // 3. Harga Satuan <= 0
            if (($pj->harga_satuan ?? 0) <= 0) {
                $items->push([
                    'id' => 'penjualan_harga_' . $pj->id,
                    'module' => 'Penjualan',
                    'module_key' => 'penjualan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Harga Satuan Penjualan Rp 0 / Negatif',
                    'description' => "Harga satuan penjualan {$namaProduk} bernilai Rp " . number_format($pj->harga_satuan, 0, ',', '.') . ".",
                    'edit_url' => $editUrl,
                ]);
            }

            // 4. Total Harga Mismatch (Total Harga != Berat * Harga Satuan)
            $expectedTotal = ($pj->berat_kg ?? 0) * ($pj->harga_satuan ?? 0);
            if (abs(($pj->total_harga ?? 0) - $expectedTotal) > 1) {
                $items->push([
                    'id' => 'penjualan_mismatch_' . $pj->id,
                    'module' => 'Penjualan',
                    'module_key' => 'penjualan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'danger',
                    'title' => 'Perhitungan Total Harga Tidak Sesuai',
                    'description' => "Total harga tercatat Rp " . number_format($pj->total_harga, 0, ',', '.') . ", padahal hasil perhitungan {$pj->berat_kg} kg x Rp {$pj->harga_satuan} = Rp " . number_format($expectedTotal, 0, ',', '.') . ".",
                    'edit_url' => $editUrl,
                ]);
            }

            // 5. Jumlah Bayar > Total Harga
            if (($pj->jumlah_bayar ?? 0) > ($pj->total_harga ?? 0)) {
                $items->push([
                    'id' => 'penjualan_overpaid_' . $pj->id,
                    'module' => 'Penjualan',
                    'module_key' => 'penjualan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Uang Muka Melebihi Total Nilai Penjualan',
                    'description' => "Jumlah bayar/uang muka (Rp " . number_format($pj->jumlah_bayar, 0, ',', '.') . ") lebih besar dari nilai total penjualan (Rp " . number_format($pj->total_harga, 0, ',', '.') . ").",
                    'edit_url' => $editUrl,
                ]);
            }

            // 6. Tanggal di masa depan
            if ($pj->tanggal && $pj->tanggal->isFuture()) {
                $items->push([
                    'id' => 'penjualan_future_' . $pj->id,
                    'module' => 'Penjualan',
                    'module_key' => 'penjualan',
                    'ref_number' => $namaProduk,
                    'date' => $tglDisplay,
                    'date_raw' => $tgl,
                    'severity' => 'warning',
                    'title' => 'Tanggal Penjualan di Masa Depan',
                    'description' => "Tanggal penjualan ({$pj->tanggal->format('d/m/Y')}) melampaui hari ini.",
                    'edit_url' => $editUrl,
                ]);
            }
        }

        return $items;
    }

    /**
     * Detect stock deficits (Stok Item Barang Negatif).
     */
    public function detectStockDeficits(): Collection
    {
        $items = collect();
        $categories = WasteCategory::where('is_active', true)->get();

        foreach ($categories as $cat) {
            $masuk = HasilPilahan::where(function ($q) use ($cat) {
                $q->where('waste_category_id', $cat->id)
                  ->orWhere('jenis', $cat->name);
            })->sum('tonase');

            $keluar = Penjualan::where(function ($q) use ($cat) {
                $q->where('waste_category_id', $cat->id)
                  ->orWhere('jenis_produk', $cat->name);
            })->sum('berat_kg');

            $sisa = $masuk - $keluar;

            if ($sisa < -0.01) { // Allowing minimal float precision margin
                $items->push([
                    'id' => 'stok_negatif_' . $cat->id,
                    'module' => 'Stok Barang',
                    'module_key' => 'stok',
                    'ref_number' => $cat->name,
                    'date' => Carbon::now()->format('d/m/Y'),
                    'date_raw' => Carbon::today()->format('Y-m-d'),
                    'severity' => 'danger',
                    'title' => "Defisit Stok Item: {$cat->name} (Stok Negatif)",
                    'description' => "Total hasil pemilahan ({$masuk} kg) lebih kecil dari total yang sudah dijual ({$keluar} kg). Saldo stok saat ini: " . number_format($sisa, 2, ',', '.') . " kg. Terjadi penjualan barang melebihi produksi fisik.",
                    'edit_url' => route('admin.laporan-operasional.kartu-stok-item') . '?waste_category_id=' . $cat->id,
                ]);
            }
        }

        return $items;
    }
}
