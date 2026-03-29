<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\Ritase;
use App\Models\Penjualan;
use App\Models\HasilPilahan;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class LaporanController extends Controller
{
    // ─── Laporan Keuangan ───

    public function labaRugi(Request $request)
    {
        try {
        Gate::authorize('view_laporan_keuangan');
        
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $query = Coa::query()
            ->select([
                'coa.id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi',
                DB::raw("CASE
                    WHEN coa.tipe = 'Revenue' THEN COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0)
                    WHEN coa.tipe = 'Expense' THEN COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0)
                    ELSE 0
                END as saldo"),
            ])
            ->leftJoin('jurnal_detail as jd', 'coa.id', '=', 'jd.coa_id')
            ->leftJoin('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->whereIn('coa.tipe', ['Revenue', 'Expense'])
            ->where('jh.status', 'posted')
            ->when($dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $sampai))
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi')
            ->orderBy('coa.kode_akun')
            ->get();

        $pendapatan = $query->where('tipe', 'Revenue');
        $beban = $query->where('tipe', 'Expense');
        $totalPendapatan = $pendapatan->sum('saldo');
        $totalBeban = $beban->sum('saldo');
        $labaRugiBersih = $totalPendapatan - $totalBeban;
        $data = compact('pendapatan', 'beban', 'totalPendapatan', 'totalBeban', 'labaRugiBersih', 'dari', 'sampai');

        if ($request->export === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.exports.laba-rugi-export', $data);
            return $pdf->download('Laba_Rugi_' . $dari . '_' . $sampai . '.pdf');
        } elseif ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanExcelExport('admin.laporan.exports.laba-rugi-export', $data), 
                'Laba_Rugi_' . $dari . '_' . $sampai . '.xlsx'
            );
        }

        return view('admin.laporan.laba-rugi', $data);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5)->map(fn($t) => ($t['file'] ?? '') . ':' . ($t['line'] ?? ''))->toArray(),
            ], 500);
        }
    }

    public function neracaSaldo(Request $request)
    {
        Gate::authorize('view_laporan_keuangan');

        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $rows = Coa::query()
            ->select([
                'coa.*',
                DB::raw('COALESCE(SUM(jd.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(jd.kredit), 0) as total_kredit'),
                DB::raw('COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0) as saldo'),
            ])
            ->leftJoin('jurnal_detail as jd', 'coa.id', '=', 'jd.coa_id')
            ->leftJoin('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->where('jh.status', 'posted')
            ->when($dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $sampai))
            ->groupBy('coa.id', 'coa.tenant_id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi', 'coa.created_at', 'coa.updated_at', 'coa.deleted_at')
            ->orderBy('coa.kode_akun')
            ->get();

        $totalDebit = $rows->sum('total_debit');
        $totalKredit = $rows->sum('total_kredit');
        $data = compact('rows', 'totalDebit', 'totalKredit', 'dari', 'sampai');

        if ($request->export === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.exports.neraca-saldo-export', $data);
            return $pdf->download('Neraca_Saldo_' . $dari . '_' . $sampai . '.pdf');
        } elseif ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanExcelExport('admin.laporan.exports.neraca-saldo-export', $data), 
                'Neraca_Saldo_' . $dari . '_' . $sampai . '.xlsx'
            );
        }

        return view('admin.laporan.neraca-saldo', $data);
    }

    public function posisiKeuangan(Request $request)
    {
        Gate::authorize('view_laporan_keuangan');

        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $query = Coa::query()
            ->select([
                'coa.id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi',
                DB::raw("CASE
                    WHEN coa.tipe = 'Asset' THEN COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0)
                    ELSE COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0)
                END as saldo"),
            ])
            ->leftJoin('jurnal_detail as jd', 'coa.id', '=', 'jd.coa_id')
            ->leftJoin('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->whereIn('coa.tipe', ['Asset', 'Liability', 'Equity'])
            ->where('jh.status', 'posted')
            ->when($sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $sampai))
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun', 'coa.tipe', 'coa.klasifikasi')
            ->orderBy('coa.kode_akun')
            ->get();

        $asetLancar = $query->where('klasifikasi', 'Aset Lancar');
        $asetTidakLancar = $query->where('klasifikasi', 'Aset Tidak Lancar');
        $liabilitasJP = $query->where('klasifikasi', 'Liabilitas Jangka Pendek');
        $liabilitasJPj = $query->where('klasifikasi', 'Liabilitas Jangka Panjang');
        $ekuitas = $query->where('klasifikasi', 'Ekuitas');

        // Menghitung Laba/Rugi Berjalan untuk diseimbangkan ke Ekuitas
        $labaRugi = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->join('coa', 'jd.coa_id', '=', 'coa.id')
            ->where('jh.status', 'posted')
            ->whereIn('coa.tipe', ['Revenue', 'Expense'])
            ->when($sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $sampai))
            ->selectRaw("COALESCE(SUM(CASE WHEN coa.tipe = 'Revenue' THEN jd.kredit - jd.debit ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN coa.tipe = 'Expense' THEN jd.debit - jd.kredit ELSE 0 END), 0) as laba_rugi")
            ->value('laba_rugi') ?? 0;

        $totalAsetLancar = $asetLancar->sum('saldo');
        $totalAsetTidakLancar = $asetTidakLancar->sum('saldo');
        $totalAset = $totalAsetLancar + $totalAsetTidakLancar;

        $totalLiabilitasJP = $liabilitasJP->sum('saldo');
        $totalLiabilitasJPj = $liabilitasJPj->sum('saldo');
        $totalLiabilitas = $totalLiabilitasJP + $totalLiabilitasJPj;
        $totalEkuitas = $ekuitas->sum('saldo') + $labaRugi;
        $totalLiabilitasEkuitas = $totalLiabilitas + $totalEkuitas;

        $data = compact(
            'asetLancar', 'asetTidakLancar', 'liabilitasJP', 'liabilitasJPj', 'ekuitas', 'labaRugi',
            'totalAsetLancar', 'totalAsetTidakLancar', 'totalAset',
            'totalLiabilitasJP', 'totalLiabilitasJPj', 'totalLiabilitas',
            'totalEkuitas', 'totalLiabilitasEkuitas', 'sampai'
        );

        if ($request->export === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.exports.posisi-keuangan-export', $data);
            return $pdf->download('Posisi_Keuangan_' . $sampai . '.pdf');
        } elseif ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanExcelExport('admin.laporan.exports.posisi-keuangan-export', $data), 
                'Posisi_Keuangan_' . $sampai . '.xlsx'
            );
        }

        return view('admin.laporan.posisi-keuangan', $data);
    }

    public function arusKas(Request $request)
    {
        Gate::authorize('view_laporan_keuangan');

        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $kasAccounts = Coa::where('tipe', 'Asset')
            ->where('klasifikasi', 'Aset Lancar')
            ->where('kode_akun', 'like', '11%')
            ->pluck('id');

        $operasi = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->join('coa', 'jd.coa_id', '=', 'coa.id')
            ->where('jh.status', 'posted')
            ->whereIn('jd.coa_id', $kasAccounts)
            ->when($dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $sampai))
            ->select([
                'coa.kode_akun', 'coa.nama_akun',
                DB::raw('SUM(jd.debit) as kas_masuk'),
                DB::raw('SUM(jd.kredit) as kas_keluar'),
                DB::raw('SUM(jd.debit) - SUM(jd.kredit) as kas_bersih'),
            ])
            ->groupBy('coa.id', 'coa.kode_akun', 'coa.nama_akun')
            ->get();

        $totalKasMasuk = $operasi->sum('kas_masuk');
        $totalKasKeluar = $operasi->sum('kas_keluar');
        $totalKasBersih = $operasi->sum('kas_bersih');

        $saldoAwal = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->where('jh.status', 'posted')
            ->whereIn('jd.coa_id', $kasAccounts)
            ->when($dari, fn ($q) => $q->whereDate('jh.tanggal', '<', $dari))
            ->selectRaw('COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0) as saldo')
            ->value('saldo') ?? 0;

        $saldoAkhir = $saldoAwal + $totalKasBersih;
        $data = compact('operasi', 'totalKasMasuk', 'totalKasKeluar', 'totalKasBersih', 'saldoAwal', 'saldoAkhir', 'dari', 'sampai');

        if ($request->export === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.exports.arus-kas-export', $data);
            return $pdf->download('Arus_Kas_' . $dari . '_' . $sampai . '.pdf');
        } elseif ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanExcelExport('admin.laporan.exports.arus-kas-export', $data), 
                'Arus_Kas_' . $dari . '_' . $sampai . '.xlsx'
            );
        }

        return view('admin.laporan.arus-kas', $data);
    }

    public function perubahanEkuitas(Request $request)
    {
        Gate::authorize('view_laporan_keuangan');

        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $ekuitasAccounts = Coa::where('tipe', 'Equity')->orderBy('kode_akun')->get();

        $rows = [];
        $totalSaldoAwal = 0; $totalPenambahan = 0; $totalPengurangan = 0; $totalSaldoAkhir = 0;

        foreach ($ekuitasAccounts as $akun) {
            $saldoAwal = DB::table('jurnal_detail as jd')
                ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
                ->where('jh.status', 'posted')->where('jd.coa_id', $akun->id)
                ->when($dari, fn ($q) => $q->whereDate('jh.tanggal', '<', $dari))
                ->selectRaw('COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0) as saldo')
                ->value('saldo') ?? 0;

            $mutasi = DB::table('jurnal_detail as jd')
                ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
                ->where('jh.status', 'posted')->where('jd.coa_id', $akun->id)
                ->when($dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $dari))
                ->when($sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $sampai))
                ->selectRaw('COALESCE(SUM(jd.kredit), 0) as penambahan, COALESCE(SUM(jd.debit), 0) as pengurangan')
                ->first();

            $penambahan = $mutasi->penambahan ?? 0;
            $pengurangan = $mutasi->pengurangan ?? 0;
            $saldoAkhir = $saldoAwal + $penambahan - $pengurangan;

            $totalSaldoAwal += $saldoAwal; $totalPenambahan += $penambahan;
            $totalPengurangan += $pengurangan; $totalSaldoAkhir += $saldoAkhir;

            $rows[] = compact('saldoAwal', 'penambahan', 'pengurangan', 'saldoAkhir') + [
                'kode_akun' => $akun->kode_akun, 'nama_akun' => $akun->nama_akun
            ];
        }

        $labaRugi = DB::table('jurnal_detail as jd')
            ->join('jurnal_header as jh', 'jd.jurnal_header_id', '=', 'jh.id')
            ->join('coa', 'jd.coa_id', '=', 'coa.id')
            ->where('jh.status', 'posted')->whereIn('coa.tipe', ['Revenue', 'Expense'])
            ->when($dari, fn ($q) => $q->whereDate('jh.tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('jh.tanggal', '<=', $sampai))
            ->selectRaw("COALESCE(SUM(CASE WHEN coa.tipe = 'Revenue' THEN jd.kredit - jd.debit ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN coa.tipe = 'Expense' THEN jd.debit - jd.kredit ELSE 0 END), 0) as laba_rugi")
            ->value('laba_rugi') ?? 0;

        $totalSaldoAkhir += $labaRugi;
        $data = compact('rows', 'labaRugi', 'totalSaldoAwal', 'totalPenambahan', 'totalPengurangan', 'totalSaldoAkhir', 'dari', 'sampai');

        if ($request->export === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.exports.perubahan-ekuitas-export', $data);
            return $pdf->download('Perubahan_Ekuitas_' . $dari . '_' . $sampai . '.pdf');
        } elseif ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanExcelExport('admin.laporan.exports.perubahan-ekuitas-export', $data), 
                'Perubahan_Ekuitas_' . $dari . '_' . $sampai . '.xlsx'
            );
        }

        return view('admin.laporan.perubahan-ekuitas', $data);
    }

    public function bukuBesar(Request $request)
    {
        Gate::authorize('view_laporan_keuangan');

        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $coaId = $request->get('coa_id');

        $query = JurnalDetail::query()
            ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->join('coa', 'jurnal_detail.coa_id', '=', 'coa.id')
            ->where('jurnal_header.status', 'posted')
            ->when($dari, fn ($q) => $q->whereDate('jurnal_header.tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('jurnal_header.tanggal', '<=', $sampai))
            ->when($coaId, fn ($q) => $q->where('jurnal_detail.coa_id', $coaId))
            ->select([
                'jurnal_detail.*',
                'jurnal_header.tanggal', 'jurnal_header.deskripsi',
                'coa.kode_akun', 'coa.nama_akun',
            ])
            ->orderByDesc('jurnal_header.tanggal');

        $coas = Coa::orderBy('kode_akun')->get();

        if ($request->export === 'pdf' || $request->export === 'excel') {
            $rows = $query->get();
            $data = compact('rows', 'coas', 'dari', 'sampai', 'coaId');
            
            if ($request->export === 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.exports.buku-besar-export', $data);
                return $pdf->download('Buku_Besar_' . $dari . '_' . $sampai . '.pdf');
            } elseif ($request->export === 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\LaporanExcelExport('admin.laporan.exports.buku-besar-export', $data), 
                    'Buku_Besar_' . $dari . '_' . $sampai . '.xlsx'
                );
            }
        }

        $rows = $query->paginate(20)->withQueryString();
        return view('admin.laporan.buku-besar', compact('rows', 'coas', 'dari', 'sampai', 'coaId'));
    }

    // ─── Laporan Operasional ───

    public function laporanRitase(Request $request)
    {
        Gate::authorize('view_laporan_operasional');

        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $klienId = $request->get('klien_id');
        $status = $request->get('status');

        $query = Ritase::with(['armada', 'klien'])
            ->when($dari, fn ($q) => $q->whereDate('waktu_masuk', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('waktu_masuk', '<=', $sampai))
            ->when($klienId, fn ($q) => $q->where('klien_id', $klienId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('waktu_masuk');

        $rows = $query->paginate(20)->withQueryString();
        $kliens = \App\Models\Klien::orderBy('nama_klien')->get();

        $totals = (clone $query)->reorder()->selectRaw('SUM(berat_netto) as total_netto, SUM(biaya_tipping) as total_tipping, COUNT(*) as total_rows')->first();

        return view('admin.laporan.ritase', compact('rows', 'kliens', 'dari', 'sampai', 'klienId', 'status', 'totals'));
    }

    public function laporanPenjualan(Request $request)
    {
        Gate::authorize('view_laporan_operasional');

        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $query = Penjualan::with('klien')
            ->when($dari, fn ($q) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal', '<=', $sampai))
            ->orderByDesc('tanggal');

        $rows = $query->paginate(20)->withQueryString();
        $totals = (clone $query)->reorder()->selectRaw('SUM(berat_kg) as total_berat, SUM(total_harga) as total_harga, COUNT(*) as total_rows')->first();

        return view('admin.laporan.penjualan', compact('rows', 'dari', 'sampai', 'totals'));
    }

    public function laporanHasilPilahan(Request $request)
    {
        Gate::authorize('view_laporan_operasional');

        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $kategori = $request->get('kategori');

        $query = HasilPilahan::query()
            ->when($dari, fn ($q) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal', '<=', $sampai))
            ->when($kategori, fn ($q) => $q->where('kategori', $kategori))
            ->orderByDesc('tanggal');

        $rows = $query->paginate(20)->withQueryString();
        $totals = (clone $query)->reorder()->selectRaw('SUM(tonase) as total_tonase, COUNT(*) as total_rows')->first();

        // Stock Summary Logic
        $pilahanAgg = HasilPilahan::selectRaw('kategori, jenis, SUM(tonase) as gross_tonase')
            ->when($dari, fn ($q) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal', '<=', $sampai))
            ->when($kategori, fn ($q) => $q->where('kategori', $kategori))
            ->groupBy('kategori', 'jenis')
            ->get();

        $penjualanAgg = DB::table('penjualan')
            ->selectRaw('jenis_produk, SUM(berat_kg) as total_keluar')
            ->when($dari, fn ($q) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal', '<=', $sampai))
            ->groupBy('jenis_produk')
            ->get()
            ->keyBy('jenis_produk');

        $stokSummary = [];
        $totalPilahanAll = 0;
        $totalTerjualAll = 0;
        $totalSisaAll = 0;

        foreach ($pilahanAgg as $item) {
            $jual = isset($penjualanAgg[$item->jenis]) ? $penjualanAgg[$item->jenis]->total_keluar : 0;
            $sisa = $item->gross_tonase - $jual;
            
            $stokSummary[] = (object)[
                'kategori' => $item->kategori,
                'jenis' => $item->jenis,
                'total_pilahan' => $item->gross_tonase,
                'total_terjual' => $jual,
                'sisa_stok' => $sisa
            ];

            $totalPilahanAll += $item->gross_tonase;
            $totalTerjualAll += $jual;
            $totalSisaAll += $sisa;
        }

        $summaryTotals = (object)[
            'total_pilahan' => $totalPilahanAll,
            'total_terjual' => $totalTerjualAll,
            'sisa_stok' => $totalSisaAll
        ];

        return view('admin.laporan.hasil-pilahan', compact('rows', 'dari', 'sampai', 'kategori', 'totals', 'stokSummary', 'summaryTotals'));
    }
}
