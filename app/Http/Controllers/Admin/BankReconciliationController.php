<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JurnalDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BankReconciliationController extends Controller
{
    /**
     * Show the reconciliation form — upload CSV + pick COA.
     */
    public function index()
    {
        Gate::authorize('view_jurnal_kas');

        // Get all Kas & Bank COAs (kode_akun starts with '11')
        $kasBankCoas = Coa::where('kode_akun', 'like', '11%')
            ->orderBy('kode_akun')
            ->get()
            ->map(function ($coa) {
                $saldo = JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                    ->where('jurnal_header.status', 'posted')
                    ->where('jurnal_detail.coa_id', $coa->id)
                    ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
                    ->value('saldo') ?? 0;
                $coa->saldo = $saldo;
                return $coa;
            });

        return view('admin.rekonsiliasi-bank.index', compact('kasBankCoas'));
    }

    /**
     * Process the CSV upload, parse, and match against journal entries.
     */
    public function proses(Request $request)
    {
        Gate::authorize('view_jurnal_kas');

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
            'coa_id' => 'required|exists:coa,id',
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
            'toleransi_hari' => 'nullable|integer|min:0|max:14',
        ]);

        $coaId = $request->coa_id;
        $coa = Coa::findOrFail($coaId);
        $dari = Carbon::parse($request->dari);
        $sampai = Carbon::parse($request->sampai);
        $toleransiHari = (int) ($request->toleransi_hari ?? 3);

        // ───── 1. Parse CSV ─────
        $csvRows = $this->parseCsv($request->file('csv_file'));

        if (empty($csvRows)) {
            return back()->withInput()->withErrors([
                'csv_file' => 'File CSV kosong atau format kolom tidak dikenali. Pastikan file memiliki kolom Tanggal, Keterangan, Debit, dan Kredit.',
            ]);
        }

        // Filter CSV rows within the date range
        $csvRows = array_values(array_filter($csvRows, function ($row) use ($dari, $sampai) {
            $tgl = Carbon::parse($row['tanggal']);
            return $tgl->gte($dari->startOfDay()) && $tgl->lte($sampai->endOfDay());
        }));

        // ───── 2. Get Application Journal Entries ─────
        $bukuRows = JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->where('jurnal_header.status', 'posted')
            ->where('jurnal_detail.coa_id', $coaId)
            ->whereDate('jurnal_header.tanggal', '>=', $dari)
            ->whereDate('jurnal_header.tanggal', '<=', $sampai)
            ->select([
                'jurnal_detail.id',
                'jurnal_header.tanggal',
                'jurnal_header.deskripsi',
                'jurnal_header.nomor_referensi',
                'jurnal_detail.debit',
                'jurnal_detail.kredit',
            ])
            ->orderBy('jurnal_header.tanggal')
            ->orderBy('jurnal_detail.id')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'tanggal' => Carbon::parse($row->tanggal)->format('Y-m-d'),
                    'deskripsi' => $row->deskripsi ?? $row->nomor_referensi,
                    'nomor_referensi' => $row->nomor_referensi,
                    'debit' => (float) $row->debit,
                    'kredit' => (float) $row->kredit,
                    'matched' => false,
                ];
            })
            ->toArray();

        // ───── 3. Matching Engine ─────
        $matched = [];
        $matchedPartial = [];
        $unmatchedBank = [];
        $unmatchedBuku = [];

        // Track which rows have been matched
        $csvMatched = array_fill(0, count($csvRows), false);
        $bukuMatched = array_fill(0, count($bukuRows), false);

        // Pass 1: Exact match (same date, same amount, reversed debit/kredit)
        foreach ($csvRows as $ci => &$csvRow) {
            if ($csvMatched[$ci]) continue;

            foreach ($bukuRows as $bi => &$bukuRow) {
                if ($bukuMatched[$bi]) continue;

                if ($this->isAmountMatch($csvRow, $bukuRow) && $csvRow['tanggal'] === $bukuRow['tanggal']) {
                    $matched[] = [
                        'bank' => $csvRow,
                        'buku' => $bukuRow,
                        'selisih_hari' => 0,
                    ];
                    $csvMatched[$ci] = true;
                    $bukuMatched[$bi] = true;
                    break;
                }
            }
        }
        unset($csvRow, $bukuRow);

        // Pass 2: Partial match (same amount, date within tolerance)
        if ($toleransiHari > 0) {
            foreach ($csvRows as $ci => $csvRow) {
                if ($csvMatched[$ci]) continue;

                foreach ($bukuRows as $bi => $bukuRow) {
                    if ($bukuMatched[$bi]) continue;

                    if ($this->isAmountMatch($csvRow, $bukuRow)) {
                        $bankDate = Carbon::parse($csvRow['tanggal']);
                        $bukuDate = Carbon::parse($bukuRow['tanggal']);
                        $daysDiff = abs($bankDate->diffInDays($bukuDate));

                        if ($daysDiff > 0 && $daysDiff <= $toleransiHari) {
                            $matchedPartial[] = [
                                'bank' => $csvRow,
                                'buku' => $bukuRow,
                                'selisih_hari' => $daysDiff,
                            ];
                            $csvMatched[$ci] = true;
                            $bukuMatched[$bi] = true;
                            break;
                        }
                    }
                }
            }
        }

        // Collect unmatched
        foreach ($csvRows as $ci => $csvRow) {
            if (!$csvMatched[$ci]) {
                $unmatchedBank[] = $csvRow;
            }
        }
        foreach ($bukuRows as $bi => $bukuRow) {
            if (!$bukuMatched[$bi]) {
                $unmatchedBuku[] = $bukuRow;
            }
        }

        // ───── 4. Calculate Statistics ─────
        $totalBankDebit = collect($csvRows)->sum('debit');
        $totalBankKredit = collect($csvRows)->sum('kredit');
        $totalBukuDebit = collect($bukuRows)->sum('debit');
        $totalBukuKredit = collect($bukuRows)->sum('kredit');

        $stats = [
            'total_bank_transaksi' => count($csvRows),
            'total_buku_transaksi' => count($bukuRows),
            'total_bank_debit' => $totalBankDebit,
            'total_bank_kredit' => $totalBankKredit,
            'total_buku_debit' => $totalBukuDebit,
            'total_buku_kredit' => $totalBukuKredit,
            'matched_count' => count($matched),
            'partial_count' => count($matchedPartial),
            'unmatched_bank_count' => count($unmatchedBank),
            'unmatched_buku_count' => count($unmatchedBuku),
            'match_rate' => count($csvRows) > 0
                ? round((count($matched) + count($matchedPartial)) / count($csvRows) * 100, 1)
                : 0,
        ];

        $kasBankCoas = Coa::where('kode_akun', 'like', '11%')->orderBy('kode_akun')->get();

        return view('admin.rekonsiliasi-bank.index', compact(
            'kasBankCoas', 'coa', 'dari', 'sampai', 'toleransiHari',
            'matched', 'matchedPartial', 'unmatchedBank', 'unmatchedBuku', 'stats'
        ));
    }

    /**
     * Check if the CSV row amount matches the journal row amount.
     * Bank Kredit = Buku Debit (uang masuk)
     * Bank Debit = Buku Kredit (uang keluar)
     */
    private function isAmountMatch(array $csvRow, array $bukuRow): bool
    {
        $epsilon = 0.01;

        // Bank kredit (deposit) <=> Buku debit (kas bertambah)
        if ($csvRow['kredit'] > 0 && $bukuRow['debit'] > 0) {
            return abs($csvRow['kredit'] - $bukuRow['debit']) < $epsilon;
        }

        // Bank debit (withdrawal) <=> Buku kredit (kas berkurang)
        if ($csvRow['debit'] > 0 && $bukuRow['kredit'] > 0) {
            return abs($csvRow['debit'] - $bukuRow['kredit']) < $epsilon;
        }

        return false;
    }

    /**
     * Parse CSV file from Bank Jatim.
     * Supports both comma and semicolon delimiters.
     * Detects columns by header name (case-insensitive).
     */
    private function parseCsv($file): array
    {
        $content = file_get_contents($file->getRealPath());

        // Detect BOM and remove it
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $lines = preg_split('/\r\n|\r|\n/', $content);
        $lines = array_filter($lines, fn($l) => trim($l) !== '');

        if (count($lines) < 2) {
            return [];
        }

        // Detect delimiter: semicolon or comma
        $headerLine = $lines[0] ?? '';
        $delimiter = substr_count($headerLine, ';') >= substr_count($headerLine, ',') ? ';' : ',';

        // Parse header
        $headers = str_getcsv(array_shift($lines), $delimiter);
        $headers = array_map(fn($h) => strtolower(trim(str_replace(['"', "'"], '', $h))), $headers);

        // Map column indices
        $colMap = $this->detectColumns($headers);

        if ($colMap['tanggal'] === null || ($colMap['debit'] === null && $colMap['kredit'] === null)) {
            return [];
        }

        $rows = [];
        foreach ($lines as $line) {
            $fields = str_getcsv($line, $delimiter);

            if (count($fields) < max(array_filter($colMap, fn($v) => $v !== null)) + 1) {
                continue;
            }

            $tanggalRaw = trim($fields[$colMap['tanggal']] ?? '');
            $tanggal = $this->parseDate($tanggalRaw);

            if (!$tanggal) {
                continue; // Skip rows with invalid date
            }

            $debit = $this->parseNumber($fields[$colMap['debit']] ?? '0');
            $kredit = $this->parseNumber($fields[$colMap['kredit']] ?? '0');
            $keterangan = trim($fields[$colMap['keterangan']] ?? '');

            // Skip zero rows
            if ($debit == 0 && $kredit == 0) {
                continue;
            }

            $rows[] = [
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'debit' => $debit,
                'kredit' => $kredit,
            ];
        }

        return $rows;
    }

    /**
     * Detect column indices from CSV headers.
     */
    private function detectColumns(array $headers): array
    {
        $map = [
            'tanggal' => null,
            'keterangan' => null,
            'debit' => null,
            'kredit' => null,
        ];

        $patterns = [
            'tanggal' => ['tanggal', 'tgl', 'date', 'posting date', 'value date', 'tanggal transaksi', 'tanggal posting'],
            'keterangan' => ['keterangan', 'uraian', 'description', 'deskripsi', 'remark', 'remarks', 'catatan', 'narasi'],
            'debit' => ['debit', 'debet', 'mutasi debit', 'mutasi debet', 'tarikan', 'withdrawal', 'pengeluaran'],
            'kredit' => ['kredit', 'credit', 'mutasi kredit', 'mutasi credit', 'setoran', 'deposit', 'penerimaan'],
        ];

        foreach ($headers as $index => $header) {
            foreach ($patterns as $field => $keywords) {
                if ($map[$field] !== null) continue;
                foreach ($keywords as $keyword) {
                    if (str_contains($header, $keyword)) {
                        $map[$field] = $index;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * Parse a date string in various Indonesian/bank formats.
     */
    private function parseDate(string $raw): ?string
    {
        $raw = trim($raw);
        if (empty($raw)) return null;

        // Try standard formats
        $formats = [
            'Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y',
            'd/m/y', 'd-m-y', 'Y/m/d',
            'd M Y', 'd F Y', 'd M y',
        ];

        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $raw);
                if ($dt && $dt->year > 2000 && $dt->year < 2100) {
                    return $dt->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Try Carbon's natural parse
        try {
            $dt = Carbon::parse($raw);
            if ($dt->year > 2000 && $dt->year < 2100) {
                return $dt->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // ignore
        }

        return null;
    }

    /**
     * Parse a number string with Indonesian formatting (dot as thousands, comma as decimal).
     * E.g. "1.500.000,00" => 1500000.00
     */
    private function parseNumber(string $raw): float
    {
        $raw = trim($raw);
        if (empty($raw) || $raw === '-') return 0;

        // Remove currency symbols and spaces
        $raw = preg_replace('/[Rp\s]/', '', $raw);

        // Detect Indonesian format: if last separator is a comma with 1-2 digits after, it's decimal
        if (preg_match('/^[\d.]+,\d{1,2}$/', $raw)) {
            // Indonesian format: 1.500.000,50
            $raw = str_replace('.', '', $raw);
            $raw = str_replace(',', '.', $raw);
        } else {
            // Standard format: just remove commas used as thousands separator
            $raw = str_replace(',', '', $raw);
        }

        return (float) $raw;
    }
}
