<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\JurnalHeader;
use App\Models\JurnalDetail;
use App\Models\JurnalTemplate;
use App\Models\BukuPembantu;
use App\Models\Klien;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AiToolHandler
{
    /**
     * Dispatch tool call to the appropriate handler.
     */
    public function dispatch(string $toolName, array $args): array
    {
        $method = 'handle' . Str::studly($toolName);
        
        if (method_exists($this, $method)) {
            try {
                return $this->$method($args);
            } catch (\Exception $e) {
                return ['error' => 'Error executing tool: ' . $e->getMessage()];
            }
        }

        return ['error' => "Tool handler for '{$toolName}' not found."];
    }

    /**
     * Tool 1: parse_transaction
     * Input: { text: string }
     */
    public function handleParseTransaction(array $args): array
    {
        $text = strtolower($args['text'] ?? '');
        
        // 1. Determine type (Penerimaan / Pengeluaran)
        $type = 'Pengeluaran';
        $penerimaanKeywords = ['terima', 'dapat', 'masuk', 'pendapatan', 'bayar piutang', 'pelunasan'];
        foreach ($penerimaanKeywords as $kw) {
            if (str_contains($text, $kw)) {
                $type = 'Penerimaan';
                break;
            }
        }

        // 2. Extract nominal
        $nominal = 0;
        // Match numbers like 150000, 150.000, 150rb, 1,5jt
        if (preg_match('/(?:rp\s*)?(?:\d+[.,]?\d*)\s*(?:rb|ribu|jt|juta|m|milyar)?/i', $text, $matches)) {
            $valStr = str_replace(',', '.', $matches[0]);
            $valStr = preg_replace('/[^0-9.]/', '', $valStr);
            $val = (float)$valStr;
            
            if (str_contains($matches[0], 'rb') || str_contains($matches[0], 'ribu')) {
                $val *= 1000;
            } elseif (str_contains($matches[0], 'jt') || str_contains($matches[0], 'juta')) {
                $val *= 1000000;
            } elseif (str_contains($matches[0], 'm') || str_contains($matches[0], 'milyar')) {
                $val *= 1000000000;
            }
            
            // If the number doesn't have suffix and is less than 1000, maybe it's just a regular number,
            // but in Indonesia usually nominals are thousands. We'll just trust the regex for now.
            // A more robust check for purely digits:
            if (preg_match('/(?:rp\s*)?(?:[0-9]{1,3}(?:\.[0-9]{3})+)/i', $text, $digitMatches)) {
                $nominal = (float)preg_replace('/[^0-9]/', '', $digitMatches[0]);
            } else {
                $nominal = $val;
            }
        }

        // 3. Find Kas COA
        $coas = Coa::all();
        $kasCoas = $coas->filter(fn($c) => str_starts_with($c->kode_akun, '11'));
        $matchedKas = null;
        foreach ($kasCoas as $c) {
            $nama = strtolower($c->nama_akun);
            if (str_contains($text, $nama)) {
                $matchedKas = $c;
                break;
            }
        }
        if (!$matchedKas) {
            // Default to Kas Kecil or Kas if found
            $matchedKas = $kasCoas->firstWhere('nama_akun', 'Kas Kecil') ?? $kasCoas->first();
        }

        // 4. Find Lawan COA
        $lawanCoas = $coas->reject(fn($c) => str_starts_with($c->kode_akun, '11'));
        $matchedLawan = null;
        foreach ($lawanCoas as $c) {
            $nama = strtolower($c->nama_akun);
            if (str_contains($text, $nama)) {
                $matchedLawan = $c;
                break;
            }
        }
        // Fallback for common terms
        if (!$matchedLawan) {
            if (str_contains($text, 'bensin') || str_contains($text, 'bbm')) {
                $matchedLawan = $lawanCoas->filter(fn($c) => stripos($c->nama_akun, 'BBM') !== false || stripos($c->nama_akun, 'Bahan Bakar') !== false)->first();
            } elseif (str_contains($text, 'makan') || str_contains($text, 'konsumsi')) {
                $matchedLawan = $lawanCoas->filter(fn($c) => stripos($c->nama_akun, 'Konsumsi') !== false)->first();
            } elseif (str_contains($text, 'listrik')) {
                $matchedLawan = $lawanCoas->filter(fn($c) => stripos($c->nama_akun, 'Listrik') !== false)->first();
            }
        }

        // 5. Deskripsi
        $deskripsi = ucfirst($args['text']);

        return [
            'type' => $type,
            'coa_kas' => $matchedKas ? ['kode' => $matchedKas->kode_akun, 'nama' => $matchedKas->nama_akun] : null,
            'coa_lawan' => $matchedLawan ? ['kode' => $matchedLawan->kode_akun, 'nama' => $matchedLawan->nama_akun] : null,
            'nominal' => $nominal,
            'deskripsi' => $deskripsi,
            'tanggal' => date('Y-m-d'),
            'partner' => null
        ];
    }

    /**
     * Tool 2: lookup_coa
     * Input: { query: string }
     */
    public function handleLookupCoa(array $args): array
    {
        $query = $args['query'] ?? '';
        
        $coas = Coa::where('nama_akun', 'LIKE', "%{$query}%")
            ->orWhere('kode_akun', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['kode_akun', 'nama_akun', 'tipe', 'klasifikasi']);

        if ($coas->isEmpty()) {
            return ['message' => 'No COA found matching the query.'];
        }

        return [
            'results' => $coas->toArray()
        ];
    }

    /**
     * Tool 3: navigate_to_page
     * Input: { intent: string }
     */
    public function handleNavigateToPage(array $args): array
    {
        $intent = strtolower($args['intent'] ?? '');
        $routes = [
            'dashboard' => ['url' => '/admin', 'name' => 'Dashboard'],
            'ritase' => ['url' => '/admin/ritase', 'name' => 'Ritase & Timbangan'],
            'timbangan' => ['url' => '/admin/ritase', 'name' => 'Ritase & Timbangan'],
            'klien' => ['url' => '/admin/klien', 'name' => 'Klien'],
            'penjualan' => ['url' => '/admin/penjualan', 'name' => 'Penjualan'],
            'jurnal umum' => ['url' => '/admin/jurnal', 'name' => 'Jurnal Umum'],
            'jurnal' => ['url' => '/admin/jurnal', 'name' => 'Jurnal Umum'],
            'jurnal kas' => ['url' => '/admin/jurnal-kas', 'name' => 'Jurnal Kas'],
            'kas masuk' => ['url' => '/admin/jurnal-kas', 'name' => 'Jurnal Kas'],
            'kas keluar' => ['url' => '/admin/jurnal-kas', 'name' => 'Jurnal Kas'],
            'buat jurnal kas' => ['url' => '/admin/jurnal-kas/create', 'name' => 'Buat Jurnal Kas'],
            'transfer kas' => ['url' => '/admin/transfer-kas', 'name' => 'Transfer Kas'],
            'transfer bank' => ['url' => '/admin/transfer-kas', 'name' => 'Transfer Kas'],
            'invoice' => ['url' => '/admin/invoice', 'name' => 'Invoice'],
            'coa' => ['url' => '/admin/coa', 'name' => 'Chart of Account'],
            'chart of account' => ['url' => '/admin/coa', 'name' => 'Chart of Account'],
            'akun' => ['url' => '/admin/coa', 'name' => 'Chart of Account'],
            'vendor' => ['url' => '/admin/vendor', 'name' => 'Vendor'],
            'rekonsiliasi bank' => ['url' => '/admin/rekonsiliasi-bank', 'name' => 'Rekonsiliasi Bank'],
            'buku pembantu piutang' => ['url' => '/admin/buku-pembantu/piutang', 'name' => 'Buku Pembantu Piutang'],
            'buku pembantu utang' => ['url' => '/admin/buku-pembantu/utang', 'name' => 'Buku Pembantu Utang'],
            'laporan laba rugi' => ['url' => '/admin/laporan/laba-rugi', 'name' => 'Laporan Laba Rugi'],
            'laba rugi' => ['url' => '/admin/laporan/laba-rugi', 'name' => 'Laporan Laba Rugi'],
            'laporan neraca' => ['url' => '/admin/laporan/neraca-saldo', 'name' => 'Laporan Neraca Saldo'],
            'neraca' => ['url' => '/admin/laporan/neraca-saldo', 'name' => 'Laporan Neraca Saldo'],
            'laporan posisi keuangan' => ['url' => '/admin/laporan/posisi-keuangan', 'name' => 'Laporan Posisi Keuangan'],
            'posisi keuangan' => ['url' => '/admin/laporan/posisi-keuangan', 'name' => 'Laporan Posisi Keuangan'],
            'laporan arus kas' => ['url' => '/admin/laporan/arus-kas', 'name' => 'Laporan Arus Kas'],
            'arus kas' => ['url' => '/admin/laporan/arus-kas', 'name' => 'Laporan Arus Kas'],
            'buku besar' => ['url' => '/admin/laporan/buku-besar', 'name' => 'Buku Besar'],
            'buku kas' => ['url' => '/admin/laporan/buku-kas', 'name' => 'Buku Kas'],
            'kehadiran' => ['url' => '/admin/hrd/attendance', 'name' => 'Kehadiran Karyawan'],
            'absensi' => ['url' => '/admin/hrd/attendance', 'name' => 'Kehadiran Karyawan'],
            'karyawan' => ['url' => '/admin/hrd/employee', 'name' => 'Karyawan'],
            'employee' => ['url' => '/admin/hrd/employee', 'name' => 'Karyawan'],
            'upah' => ['url' => '/admin/hrd/wage-calculation', 'name' => 'Perhitungan Gaji'],
            'gaji' => ['url' => '/admin/hrd/wage-calculation', 'name' => 'Perhitungan Gaji'],
            'hasil pilahan' => ['url' => '/admin/hasil-pilahan', 'name' => 'Hasil Pilahan'],
            'pengangkutan residu' => ['url' => '/admin/pengangkutan-residu', 'name' => 'Pengangkutan Residu'],
            'pengaturan perusahaan' => ['url' => '/admin/company-settings', 'name' => 'Pengaturan Perusahaan'],
            'pengaturan' => ['url' => '/admin/company-settings', 'name' => 'Pengaturan Perusahaan'],
        ];

        $matched = null;
        foreach ($routes as $key => $route) {
            if (str_contains($intent, $key)) {
                $matched = $route;
                break;
            }
        }

        if ($matched) {
            return [
                'url' => $matched['url'],
                'page_name' => $matched['name'],
                'instructions' => "Berikan tautan markdown ke pengguna: [{$matched['name']}]({$matched['url']})"
            ];
        }

        return [
            'error' => 'Halaman tidak ditemukan. Beritahu pengguna halaman yang tersedia di sistem.',
            'available_pages' => array_values(array_unique(array_map(fn($r) => $r['name'], $routes)))
        ];
    }

    /**
     * Tool 4: get_financial_summary
     * Input: { type: 'saldo_kas'|'piutang'|'utang'|'pendapatan'|'beban' }
     */
    public function handleGetFinancialSummary(array $args): array
    {
        $type = $args['type'] ?? '';
        $amount = 0;
        $details = [];
        $period = date('F Y');

        switch ($type) {
            case 'saldo_kas':
                // saldo_kas: SUM(debit) - SUM(kredit) for COA starting with '11'
                if (class_exists(JurnalDetail::class) && class_exists(JurnalHeader::class)) {
                    $detailsData = DB::table('jurnal_details')
                        ->join('jurnal_headers', 'jurnal_details.jurnal_header_id', '=', 'jurnal_headers.id')
                        ->join('coas', 'jurnal_details.coa_id', '=', 'coas.id')
                        ->where('jurnal_headers.status', 'posted')
                        ->where('coas.kode_akun', 'LIKE', '11%')
                        ->selectRaw('coas.nama_akun, SUM(debit) - SUM(kredit) as saldo')
                        ->groupBy('coas.id', 'coas.nama_akun')
                        ->get();

                    $amount = $detailsData->sum('saldo');
                    $details = $detailsData->toArray();
                }
                break;
            
            case 'piutang':
                if (class_exists(BukuPembantu::class)) {
                    $amount = BukuPembantu::where('tipe', 'piutang')
                        ->where('status', 'pending')
                        ->sum('sisa_tagihan') ?? BukuPembantu::where('tipe', 'piutang')->where('status', 'pending')->sum('nominal');
                }
                break;

            case 'utang':
                if (class_exists(BukuPembantu::class)) {
                    $amount = BukuPembantu::where('tipe', 'utang')
                        ->where('status', 'pending')
                        ->sum('sisa_tagihan') ?? BukuPembantu::where('tipe', 'utang')->where('status', 'pending')->sum('nominal');
                }
                break;

            case 'pendapatan':
                if (class_exists(JurnalDetail::class) && class_exists(JurnalHeader::class)) {
                    $detailsData = DB::table('jurnal_details')
                        ->join('jurnal_headers', 'jurnal_details.jurnal_header_id', '=', 'jurnal_headers.id')
                        ->join('coas', 'jurnal_details.coa_id', '=', 'coas.id')
                        ->where('jurnal_headers.status', 'posted')
                        ->where('coas.tipe', 'Revenue')
                        ->whereMonth('jurnal_headers.tanggal', date('m'))
                        ->whereYear('jurnal_headers.tanggal', date('Y'))
                        ->selectRaw('coas.nama_akun, SUM(kredit) - SUM(debit) as saldo')
                        ->groupBy('coas.id', 'coas.nama_akun')
                        ->get();

                    $amount = $detailsData->sum('saldo');
                    $details = $detailsData->toArray();
                }
                break;

            case 'beban':
                if (class_exists(JurnalDetail::class) && class_exists(JurnalHeader::class)) {
                    $detailsData = DB::table('jurnal_details')
                        ->join('jurnal_headers', 'jurnal_details.jurnal_header_id', '=', 'jurnal_headers.id')
                        ->join('coas', 'jurnal_details.coa_id', '=', 'coas.id')
                        ->where('jurnal_headers.status', 'posted')
                        ->where('coas.tipe', 'Expense')
                        ->whereMonth('jurnal_headers.tanggal', date('m'))
                        ->whereYear('jurnal_headers.tanggal', date('Y'))
                        ->selectRaw('coas.nama_akun, SUM(debit) - SUM(kredit) as saldo')
                        ->groupBy('coas.id', 'coas.nama_akun')
                        ->get();

                    $amount = $detailsData->sum('saldo');
                    $details = $detailsData->toArray();
                }
                break;

            default:
                return ['error' => 'Tipe ringkasan finansial tidak didukung. Gunakan: saldo_kas, piutang, utang, pendapatan, atau beban.'];
        }

        return [
            'type' => $type,
            'amount' => (float)$amount,
            'formatted' => 'Rp ' . number_format((float)$amount, 0, ',', '.'),
            'period' => $period,
            'details' => $details
        ];
    }

    /**
     * Tool 5: suggest_journal_template
     * Input: { description: string }
     */
    public function handleSuggestJournalTemplate(array $args): array
    {
        $description = $args['description'] ?? '';
        
        if (!class_exists(JurnalTemplate::class)) {
            return ['error' => 'Modul template jurnal belum tersedia.'];
        }

        $templates = JurnalTemplate::where('nama_template', 'LIKE', "%{$description}%")
            ->orWhere('deskripsi', 'LIKE', "%{$description}%")
            ->with('details.coa')
            ->limit(5)
            ->get();

        if ($templates->isEmpty()) {
            return ['message' => 'Tidak ditemukan template jurnal yang cocok dengan deskripsi tersebut.'];
        }

        $result = $templates->map(function($t) {
            return [
                'id' => $t->id,
                'nama' => $t->nama_template,
                'tipe' => $t->tipe,
                'details' => $t->details->map(function($d) {
                    return [
                        'coa' => $d->coa ? $d->coa->nama_akun : null,
                        'posisi' => $d->posisi
                    ];
                })->toArray()
            ];
        });

        return [
            'results' => $result->toArray()
        ];
    }
}
