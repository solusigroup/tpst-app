<?php

namespace App\Services;

use App\Models\KpiMaster;
use App\Models\KpiDailyChecklist;
use App\Models\KpiStakeholderComplaint;
use App\Models\KpiMachineActivityLog;
use App\Models\KpiEvaluation;
use App\Models\Ritase;
use App\Models\ProduksiHarian;
use App\Models\HasilPilahan;
use App\Models\Penjualan;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Attendance;
use App\Models\EmployeeOutput;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KpiCalculationService
{
    /**
     * Menghitung Indeks KPI Global TPST Megilan untuk periode tertentu
     */
    public function calculateGlobalKpi($startDate, $endDate, $tenantId = null)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        $totalDays = max(1, $start->diffInDays($end) + 1);

        // 1. Tonase Pengolahan (Target: 20 ton/hari)
        $ritaseQuery = Ritase::whereBetween('waktu_masuk', [$start, $end]);
        if ($tenantId) $ritaseQuery->where('tenant_id', $tenantId);
        $totalKgMasuk = (float) $ritaseQuery->sum('berat_netto');
        $totalTonMasuk = $totalKgMasuk / 1000;
        $rataTonasePerHari = $totalTonMasuk / $totalDays;
        $targetTonaseHarian = 20.0;
        $nilaiTonase = min(120.0, ($rataTonasePerHari / $targetTonaseHarian) * 100);

        // 2. Pendapatan Tipping Fee Swasta (Target: Rp 20 Jt/bulan, diskalakan sesuai jumlah hari)
        $targetTippingBulan = 20000000.0;
        $targetTippingPeriode = ($targetTippingBulan / 30) * $totalDays;
        
        // Ambil tipping dari ritase non-DLH atau total biaya_tipping
        $tippingQuery = Ritase::whereBetween('waktu_masuk', [$start, $end]);
        if ($tenantId) $tippingQuery->where('tenant_id', $tenantId);
        $totalTippingRitase = (float) $tippingQuery->sum('biaya_tipping');

        $invoiceQuery = Invoice::whereBetween('tanggal_invoice', [$start, $end]);
        if ($tenantId) $invoiceQuery->where('tenant_id', $tenantId);
        $totalInvoiceSwasta = (float) $invoiceQuery->whereHas('klien', function($q) {
            $q->where('jenis', '!=', 'DLH');
        })->sum('total_tagihan');

        $realisasiTipping = max($totalTippingRitase, $totalInvoiceSwasta);
        $nilaiTipping = $targetTippingPeriode > 0 ? min(120.0, ($realisasiTipping / $targetTippingPeriode) * 100) : 100.0;

        // 3. Kebersihan Area TPST (Persentase hari bersih / bebas tumpukan)
        $checklistQuery = KpiDailyChecklist::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        if ($tenantId) $checklistQuery->where('tenant_id', $tenantId);
        $totalChecklist = $checklistQuery->count();
        $cleanChecklist = (clone $checklistQuery)->where('ada_tumpukan_sampah', false)->count();
        $nilaiKebersihan = $totalChecklist > 0 ? ($cleanChecklist / $totalChecklist) * 100 : 100.0;

        // 4. Pengendalian Bau (Rata-rata skor bau: 100 = Tidak Bau, 70 = Ringan, 0 = Berat)
        $avgBau = (clone $checklistQuery)->avg('skor_bau');
        $nilaiBau = $avgBau !== null ? (float) $avgBau : 100.0;

        // 5. Keandalan Mesin (Zero Mesin Off - Availability)
        $machineLogQuery = KpiMachineActivityLog::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        if ($tenantId) $machineLogQuery->where('tenant_id', $tenantId);
        $totalJamOperasi = (float) (clone $machineLogQuery)->sum('jam_operasi');
        $totalJamDowntime = (float) (clone $machineLogQuery)->sum('jam_downtime');
        $totalJamTotal = $totalJamOperasi + $totalJamDowntime;
        $nilaiMesin = $totalJamTotal > 0 ? ($totalJamOperasi / $totalJamTotal) * 100 : 100.0;

        // 6. Kepuasan Stakeholder (DLH & Penggerobak / Warga) - Negatif KPI
        $complaintQuery = KpiStakeholderComplaint::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        if ($tenantId) $complaintQuery->where('tenant_id', $tenantId);
        $totalComplaints = $complaintQuery->count();
        $dlhComplaints = (clone $complaintQuery)->where('stakeholder_type', 'DLH')->count();
        $nilaiStakeholder = max(0.0, 100.0 - ($totalComplaints * 25.0));

        // Master bobot global
        $kpiItems = [
            [
                'kode' => 'GLOB_TONASE',
                'nama' => 'Target Pengolahan Sampah',
                'target' => number_format($targetTonaseHarian, 1) . ' ton/hari',
                'realisasi' => number_format($rataTonasePerHari, 1) . ' ton/hari (Total: ' . number_format($totalTonMasuk, 1) . ' ton)',
                'nilai' => round($nilaiTonase, 1),
                'bobot' => 20,
                'skor' => round(($nilaiTonase * 20) / 100, 2),
            ],
            [
                'kode' => 'GLOB_KEBERSIHAN',
                'nama' => 'Kebersihan Area TPST (Zero Tumpukan)',
                'target' => '100%',
                'realisasi' => round($nilaiKebersihan, 1) . '% hari bersih',
                'nilai' => round($nilaiKebersihan, 1),
                'bobot' => 20,
                'skor' => round(($nilaiKebersihan * 20) / 100, 2),
            ],
            [
                'kode' => 'GLOB_BAU',
                'nama' => 'Pengendalian Bau',
                'target' => '100 (Bebas Bau)',
                'realisasi' => round($nilaiBau, 1) . ' poin',
                'nilai' => round($nilaiBau, 1),
                'bobot' => 15,
                'skor' => round(($nilaiBau * 15) / 100, 2),
            ],
            [
                'kode' => 'GLOB_TIPPING',
                'nama' => 'Pendapatan Tipping Fee Swasta',
                'target' => 'Rp ' . number_format($targetTippingPeriode, 0, ',', '.'),
                'realisasi' => 'Rp ' . number_format($realisasiTipping, 0, ',', '.'),
                'nilai' => round($nilaiTipping, 1),
                'bobot' => 20,
                'skor' => round(($nilaiTipping * 20) / 100, 2),
            ],
            [
                'kode' => 'GLOB_MESIN',
                'nama' => 'Keandalan Mesin (Zero Mesin Off)',
                'target' => '100% Availability',
                'realisasi' => round($nilaiMesin, 1) . '% (Downtime: ' . $totalJamDowntime . ' jam)',
                'nilai' => round($nilaiMesin, 1),
                'bobot' => 15,
                'skor' => round(($nilaiMesin * 15) / 100, 2),
            ],
            [
                'kode' => 'GLOB_STAKEHOLDER',
                'nama' => 'Kepuasan Stakeholder (DLH & Mitra)',
                'target' => '0 Kasus Complain',
                'realisasi' => $totalComplaints . ' kasus (DLH: ' . $dlhComplaints . ')',
                'nilai' => round($nilaiStakeholder, 1),
                'bobot' => 10,
                'skor' => round(($nilaiStakeholder * 10) / 100, 2),
            ],
        ];

        $totalSkor = array_sum(array_column($kpiItems, 'skor'));
        $predikat = $this->determineGrade($totalSkor);

        return [
            'periode_mulai' => $start->toDateString(),
            'periode_selesai' => $end->toDateString(),
            'jumlah_hari' => $totalDays,
            'total_tonase' => round($totalTonMasuk, 2),
            'rata_tonase_harian' => round($rataTonasePerHari, 2),
            'realisasi_tipping' => $realisasiTipping,
            'total_complaints' => $totalComplaints,
            'total_jam_downtime' => $totalJamDowntime,
            'kpi_items' => $kpiItems,
            'total_skor' => round($totalSkor, 1),
            'predikat' => $predikat,
        ];
    }

    /**
     * Menghitung KPI Individu Pengurus Harian (Budi, Nita, Agung, Ana)
     */
    public function calculateEmployeeKpi($jabatan, $startDate, $endDate, $tenantId = null)
    {
        $global = $this->calculateGlobalKpi($startDate, $endDate, $tenantId);
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        $totalDays = $global['jumlah_hari'];

        $kpiItems = [];

        switch ($jabatan) {
            case 'site_manager': // Budi Sucahyo
                // Kinerja SDM (Disiplin kehadiran seluruh staf & pemilah)
                $attQuery = Attendance::whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);
                if ($tenantId) $attQuery->where('tenant_id', $tenantId);
                $totalAtt = $attQuery->count();
                $presentAtt = (clone $attQuery)->where('status', 'present')->count();
                $nilaiSdm = $totalAtt > 0 ? ($presentAtt / $totalAtt) * 100 : 95.0;

                // DLH Complaint
                $dlhComplaints = KpiStakeholderComplaint::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                    ->where('stakeholder_type', 'DLH')->count();
                $nilaiDlh = max(0.0, 100.0 - ($dlhComplaints * 30.0));

                $kpiItems = [
                    [
                        'nama' => 'Pencapaian Target Tonase (≥20 ton/hari)',
                        'target' => '20 ton/hari',
                        'realisasi' => $global['rata_tonase_harian'] . ' ton/hari',
                        'nilai' => $global['kpi_items'][0]['nilai'],
                        'bobot' => 20,
                        'skor' => round(($global['kpi_items'][0]['nilai'] * 20) / 100, 2),
                    ],
                    [
                        'nama' => 'Kebersihan TPST (Zero Tumpukan)',
                        'target' => '100%',
                        'realisasi' => $global['kpi_items'][1]['realisasi'],
                        'nilai' => $global['kpi_items'][1]['nilai'],
                        'bobot' => 15,
                        'skor' => round(($global['kpi_items'][1]['nilai'] * 15) / 100, 2),
                    ],
                    [
                        'nama' => 'Kinerja Mesin (Zero Downtime)',
                        'target' => '100%',
                        'realisasi' => $global['kpi_items'][4]['realisasi'],
                        'nilai' => $global['kpi_items'][4]['nilai'],
                        'bobot' => 15,
                        'skor' => round(($global['kpi_items'][4]['nilai'] * 15) / 100, 2),
                    ],
                    [
                        'nama' => 'Kepuasan DLH (Zero Complain)',
                        'target' => '0 Kasus',
                        'realisasi' => $dlhComplaints . ' kasus',
                        'nilai' => round($nilaiDlh, 1),
                        'bobot' => 15,
                        'skor' => round(($nilaiDlh * 15) / 100, 2),
                    ],
                    [
                        'nama' => 'Pendapatan Tipping Fee (Rp 20 Jt/Bln)',
                        'target' => 'Rp ' . number_format((20000000 / 30) * $totalDays, 0, ',', '.'),
                        'realisasi' => 'Rp ' . number_format($global['realisasi_tipping'], 0, ',', '.'),
                        'nilai' => $global['kpi_items'][3]['nilai'],
                        'bobot' => 15,
                        'skor' => round(($global['kpi_items'][3]['nilai'] * 15) / 100, 2),
                    ],
                    [
                        'nama' => 'Kinerja & Disiplin SDM',
                        'target' => '100% Kehadiran',
                        'realisasi' => round($nilaiSdm, 1) . '% disiplin hadir',
                        'nilai' => round($nilaiSdm, 1),
                        'bobot' => 10,
                        'skor' => round(($nilaiSdm * 10) / 100, 2),
                    ],
                    [
                        'nama' => 'Laporan Manajemen Tepat Waktu',
                        'target' => '100%',
                        'realisasi' => '100% Tepat Waktu',
                        'nilai' => 100.0,
                        'bobot' => 10,
                        'skor' => 10.0,
                    ],
                ];
                break;

            case 'admin_commercial': // Nita Khoirunisa
                // Input data ritase harian: hitung hari yang ada catatan timbangan
                $hariAdaData = Ritase::whereBetween('waktu_masuk', [$start, $end])
                    ->selectRaw('DATE(waktu_masuk) as tgl')
                    ->groupBy('tgl')
                    ->get()
                    ->count();
                $nilaiInput = min(100.0, ($hariAdaData / $totalDays) * 100);

                // Zero complain invoice
                $adminComplaints = KpiStakeholderComplaint::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                    ->whereIn('stakeholder_type', ['DLH', 'Klien Swasta'])->count();
                $nilaiAdminComplain = max(0.0, 100.0 - ($adminComplaints * 25.0));

                $kpiItems = [
                    [
                        'nama' => 'Akurasi Laporan Operasional',
                        'target' => '100%',
                        'realisasi' => '100% Terverifikasi',
                        'nilai' => 100.0,
                        'bobot' => 20,
                        'skor' => 20.0,
                    ],
                    [
                        'nama' => 'Input Data Digital Harian',
                        'target' => $totalDays . ' hari aktif',
                        'realisasi' => $hariAdaData . ' hari terinput (' . round($nilaiInput, 1) . '%)',
                        'nilai' => round($nilaiInput, 1),
                        'bobot' => 20,
                        'skor' => round(($nilaiInput * 20) / 100, 2),
                    ],
                    [
                        'nama' => 'Tipping Fee Swasta (Rp 20 Jt/Bln)',
                        'target' => 'Rp ' . number_format((20000000 / 30) * $totalDays, 0, ',', '.'),
                        'realisasi' => 'Rp ' . number_format($global['realisasi_tipping'], 0, ',', '.'),
                        'nilai' => $global['kpi_items'][3]['nilai'],
                        'bobot' => 30,
                        'skor' => round(($global['kpi_items'][3]['nilai'] * 30) / 100, 2),
                    ],
                    [
                        'nama' => 'Kelengkapan Administrasi Pelanggan',
                        'target' => '100%',
                        'realisasi' => '100% Lengkap',
                        'nilai' => 100.0,
                        'bobot' => 15,
                        'skor' => 15.0,
                    ],
                    [
                        'nama' => 'Zero Complain Administrasi',
                        'target' => '0 Kasus',
                        'realisasi' => $adminComplaints . ' kasus',
                        'nilai' => round($nilaiAdminComplain, 1),
                        'bobot' => 15,
                        'skor' => round(($nilaiAdminComplain * 15) / 100, 2),
                    ],
                ];
                break;

            case 'equipment_logistik': // Agung
                // Kesiapan Wheel Loader dari log alat
                $wlLogs = KpiMachineActivityLog::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                    ->where(function($q) {
                        $q->where('nama_alat', 'LIKE', '%wheel loader%')
                          ->orWhere('nama_alat', 'LIKE', '%loader%');
                    })->get();
                $wlOperasi = $wlLogs->sum('jam_operasi');
                $wlDowntime = $wlLogs->sum('jam_downtime');
                $wlTotal = $wlOperasi + $wlDowntime;
                $nilaiWl = $wlTotal > 0 ? ($wlOperasi / $wlTotal) * 100 : 100.0;

                // Kebersihan Area Receiving
                $recChecklist = KpiDailyChecklist::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                    ->where('area', 'Receiving')->get();
                $recClean = $recChecklist->where('ada_tumpukan_sampah', false)->count();
                $recTotal = $recChecklist->count();
                $nilaiRec = $recTotal > 0 ? ($recClean / $recTotal) * 100 : 100.0;

                $kpiItems = [
                    [
                        'nama' => 'Kesiapan Wheel Loader (Ready to Operate)',
                        'target' => '100% Siap Operasi',
                        'realisasi' => round($nilaiWl, 1) . '% (Downtime: ' . $wlDowntime . ' jam)',
                        'nilai' => round($nilaiWl, 1),
                        'bobot' => 25,
                        'skor' => round(($nilaiWl * 25) / 100, 2),
                    ],
                    [
                        'nama' => 'Kelancaran Receiving Area',
                        'target' => '100% Lancar',
                        'realisasi' => '100% Lancar',
                        'nilai' => 100.0,
                        'bobot' => 20,
                        'skor' => 20.0,
                    ],
                    [
                        'nama' => 'Support Target Tonase (20 Ton/Hari)',
                        'target' => '20 ton/hari',
                        'realisasi' => $global['rata_tonase_harian'] . ' ton/hari',
                        'nilai' => $global['kpi_items'][0]['nilai'],
                        'bobot' => 20,
                        'skor' => round(($global['kpi_items'][0]['nilai'] * 20) / 100, 2),
                    ],
                    [
                        'nama' => 'Kebersihan Area Receiving',
                        'target' => '100% Standar Bersih',
                        'realisasi' => round($nilaiRec, 1) . '% bersih',
                        'nilai' => round($nilaiRec, 1),
                        'bobot' => 15,
                        'skor' => round(($nilaiRec * 15) / 100, 2),
                    ],
                    [
                        'nama' => 'Safety Alat Berat (Zero Accident)',
                        'target' => '0 Insiden',
                        'realisasi' => '0 Insiden Kecelakaan',
                        'nilai' => 100.0,
                        'bobot' => 20,
                        'skor' => 20.0,
                    ],
                ];
                break;

            case 'operasional_qc': // Ana Maria
                // Produksi RDF
                $rdfMasuk = (float) HasilPilahan::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                    ->where('kategori', 'LIKE', '%RDF%')->sum('tonase');
                $targetRdf = 1.0 * $totalDays; // Misal 1 ton RDF per hari
                $nilaiRdf = $targetRdf > 0 ? min(120.0, ($rdfMasuk / $targetRdf) * 100) : 100.0;

                $kpiItems = [
                    [
                        'nama' => 'Kebersihan Area TPST (Zero Tumpukan)',
                        'target' => '100%',
                        'realisasi' => $global['kpi_items'][1]['realisasi'],
                        'nilai' => $global['kpi_items'][1]['nilai'],
                        'bobot' => 25,
                        'skor' => round(($global['kpi_items'][1]['nilai'] * 25) / 100, 2),
                    ],
                    [
                        'nama' => 'Kontrol Bau Area TPST',
                        'target' => '100 (Tidak Bau)',
                        'realisasi' => $global['kpi_items'][2]['realisasi'],
                        'nilai' => $global['kpi_items'][2]['nilai'],
                        'bobot' => 20,
                        'skor' => round(($global['kpi_items'][2]['nilai'] * 20) / 100, 2),
                    ],
                    [
                        'nama' => 'Produktivitas Pemilah',
                        'target' => 'Target Tercapai',
                        'realisasi' => 'Optimal',
                        'nilai' => 100.0,
                        'bobot' => 20,
                        'skor' => 20.0,
                    ],
                    [
                        'nama' => 'Kualitas Material (Kontaminasi Rendah)',
                        'target' => '100% Bersih',
                        'realisasi' => 'Standar Tercapai',
                        'nilai' => 100.0,
                        'bobot' => 20,
                        'skor' => 20.0,
                    ],
                    [
                        'nama' => 'Produksi RDF Optimal',
                        'target' => number_format($targetRdf, 1) . ' ton RDF',
                        'realisasi' => number_format($rdfMasuk, 2) . ' ton',
                        'nilai' => round($nilaiRdf, 1),
                        'bobot' => 15,
                        'skor' => round(($nilaiRdf * 15) / 100, 2),
                    ],
                ];
                break;
        }

        $totalSkor = array_sum(array_column($kpiItems, 'skor'));
        $predikat = $this->determineGrade($totalSkor);

        return [
            'jabatan' => $jabatan,
            'kpi_items' => $kpiItems,
            'total_skor' => round($totalSkor, 1),
            'predikat' => $predikat,
        ];
    }

    /**
     * Menghitung Leaderboard & Rekapitulasi 12 Tenaga Pemilah
     */
    public function calculatePemilahData($startDate, $endDate, $tenantId = null)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        $totalDays = max(1, $start->diffInDays($end) + 1);

        // Cari karyawan pemilah (salary_type borongan / harian atau role karyawan)
        $pemilahUsers = User::where(function($q) {
            $q->where('salary_type', 'borongan')
              ->orWhere('salary_type', 'harian')
              ->orWhere('role', 'karyawan')
              ->orWhere('position', 'LIKE', '%pemilah%')
              ->orWhere('position', 'LIKE', '%sortir%');
        });
        if ($tenantId) $pemilahUsers->where('tenant_id', $tenantId);
        $pemilahList = $pemilahUsers->get();

        // Ambil harga kategori sampah
        $categories = WasteCategory::all()->keyBy('id');

        $leaderboard = [];
        $targetKgPerMinggu = 50.0;
        $targetKgPeriode = ($targetKgPerMinggu / 7) * $totalDays;

        foreach ($pemilahList as $user) {
            // Ambil output pemilah dari employee_outputs
            $outputs = EmployeeOutput::where('user_id', $user->id)
                ->whereBetween('output_date', [$start->toDateString(), $end->toDateString()])
                ->with('wasteCategory')
                ->get();

            $totalKg = (float) $outputs->sum('quantity');
            $totalNilaiEkonomi = 0;
            $breakdown = [];

            foreach ($outputs as $out) {
                $price = $out->wasteCategory ? (float) $out->wasteCategory->selling_price : 3000;
                $val = $out->quantity * $price;
                $totalNilaiEkonomi += $val;

                $catName = $out->wasteCategory ? $out->wasteCategory->name : 'Lainnya';
                if (!isset($breakdown[$catName])) {
                    $breakdown[$catName] = 0;
                }
                $breakdown[$catName] += $out->quantity;
            }

            // Jika belum ada di employee_outputs, cek apakah ada di attendances
            $hadirCount = Attendance::where('user_id', $user->id)
                ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
                ->where('status', 'present')
                ->count();

            $achievementPct = $targetKgPeriode > 0 ? ($totalKg / $targetKgPeriode) * 100 : 100;
            $nilaiKpi = min(120.0, $achievementPct);

            $leaderboard[] = [
                'user_id' => $user->id,
                'nama' => $user->name,
                'total_kg' => round($totalKg, 1),
                'target_kg' => round($targetKgPeriode, 1),
                'achievement_pct' => round($achievementPct, 1),
                'nilai_kpi' => round($nilaiKpi, 1),
                'nilai_ekonomi' => $totalNilaiEkonomi,
                'hari_hadir' => $hadirCount,
                'breakdown' => $breakdown,
            ];
        }

        // Urutkan berdasarkan total_kg descending (ranking)
        usort($leaderboard, function($a, $b) {
            return $b['total_kg'] <=> $a['total_kg'];
        });

        // Berikan nomor peringkat
        foreach ($leaderboard as $index => &$item) {
            $item['ranking'] = $index + 1;
        }

        return $leaderboard;
    }

    /**
     * Konversi nilai angka ke predikat huruf
     */
    public function determineGrade($score)
    {
        if ($score >= 100) return 'Sangat Baik (A)';
        if ($score >= 85) return 'Baik (B)';
        if ($score >= 70) return 'Cukup (C)';
        return 'Kurang (D)';
    }
}
