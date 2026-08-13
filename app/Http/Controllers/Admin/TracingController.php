<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Penjualan;
use App\Models\Ritase;
use App\Models\JurnalHeader;
use App\Models\JurnalDetail;
use App\Models\BukuPembantu;
use App\Models\Klien;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class TracingController extends Controller
{
    /**
     * Display the tracing portal dashboard.
     */
    public function index(Request $request)
    {
        Gate::authorize('view_buku_pembantu');

        $jenis = $request->input('jenis', 'semua'); // piutang_swasta, penjualan_pilahan, semua
        $search = $request->input('search');
        $status = $request->input('status');
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        // --- Metrics ---
        $totalPiutangSwasta = BukuPembantu::where('tipe', 'piutang')
            ->where('status', 'pending')
            ->whereHasMorph('contactable', [Klien::class], function ($q) {
                $q->where('jenis', 'Swasta');
            })
            ->selectRaw('SUM(jumlah - terbayar) as total')
            ->value('total') ?? 0;

        $totalPiutangOfftaker = BukuPembantu::where('tipe', 'piutang')
            ->where('status', 'pending')
            ->whereHasMorph('contactable', [Klien::class], function ($q) {
                $q->where('jenis', 'Offtaker');
            })
            ->selectRaw('SUM(jumlah - terbayar) as total')
            ->value('total') ?? 0;

        $totalLunas = BukuPembantu::where('tipe', 'piutang')
            ->where('status', 'lunas')
            ->sum('jumlah');

        // --- Main Query: Invoice-based tracing ---
        $query = Invoice::with(['klien', 'ritase', 'penjualan'])
            ->whereHas('klien', function ($q) use ($jenis) {
                if ($jenis === 'piutang_swasta') {
                    $q->where('jenis', 'Swasta');
                } elseif ($jenis === 'penjualan_pilahan') {
                    $q->whereIn('jenis', ['Offtaker', 'Swasta']);
                }
            });

        // For penjualan_pilahan, only show invoices that have penjualan items
        if ($jenis === 'penjualan_pilahan') {
            $query->whereHas('penjualan');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_invoice', 'like', "%{$search}%")
                  ->orWhereHas('klien', function ($qk) use ($search) {
                      $qk->where('nama_klien', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($dari) {
            $query->whereDate('tanggal_invoice', '>=', $dari);
        }
        if ($sampai) {
            $query->whereDate('tanggal_invoice', '<=', $sampai);
        }

        $invoices = $query->orderByDesc('tanggal_invoice')->paginate(15)->withQueryString();

        // Enrich each invoice with tracing status markers
        $invoiceIds = $invoices->pluck('id');
        $jurnalHeaders = JurnalHeader::where('referensi_type', Invoice::class)
            ->whereIn('referensi_id', $invoiceIds)
            ->get()
            ->keyBy('referensi_id');

        $bukuPembantus = BukuPembantu::whereHas('jurnalHeader', function ($q) use ($invoiceIds) {
                $q->where('referensi_type', Invoice::class)
                  ->whereIn('referensi_id', $invoiceIds);
            })
            ->with('jurnalHeader')
            ->get()
            ->keyBy(function ($item) {
                return $item->jurnalHeader->referensi_id ?? null;
            });

        foreach ($invoices as $inv) {
            $inv->has_jurnal = isset($jurnalHeaders[$inv->id]);
            $inv->jurnal_header_id = $jurnalHeaders[$inv->id]->id ?? null;
            $inv->has_buku_pembantu = isset($bukuPembantus[$inv->id]);
            $bp = $bukuPembantus[$inv->id] ?? null;
            $inv->bp_status = $bp ? $bp->status : null;
            $inv->bp_terbayar = $bp ? $bp->terbayar : 0;
            $inv->bp_jumlah = $bp ? $bp->jumlah : 0;
            $inv->has_ritase = $inv->ritase->count() > 0;
            $inv->has_penjualan = $inv->penjualan->count() > 0;
        }

        // Discrepancy count
        $discrepancyCount = $this->countDiscrepancies();

        return view('admin.tracing.index', compact(
            'invoices', 'jenis', 'totalPiutangSwasta', 'totalPiutangOfftaker',
            'totalLunas', 'discrepancyCount'
        ));
    }

    /**
     * Show detailed audit trail for a specific transaction.
     */
    public function show(string $type, int $id)
    {
        Gate::authorize('view_buku_pembantu');

        $data = [];

        if ($type === 'invoice') {
            $invoice = Invoice::with(['klien', 'ritase.klien', 'penjualan.klien'])->findOrFail($id);

            // Step 1: Operational Documents
            $data['operational'] = [
                'ritase' => $invoice->ritase,
                'penjualan' => $invoice->penjualan,
            ];

            // Step 2: Invoice
            $data['invoice'] = $invoice;

            // Step 3: Journal (GL)
            $jurnalHeader = JurnalHeader::where('referensi_type', Invoice::class)
                ->where('referensi_id', $invoice->id)
                ->with('jurnalDetails.coa')
                ->first();
            $data['jurnal'] = $jurnalHeader;

            // Step 4: Subsidiary Ledger (Buku Pembantu)
            $bukuPembantu = null;
            if ($jurnalHeader) {
                $bukuPembantu = BukuPembantu::where('jurnal_header_id', $jurnalHeader->id)
                    ->with('contactable')
                    ->first();
            }
            $data['buku_pembantu'] = $bukuPembantu;

            // Step 5: Payment / Settlement Journal
            $paymentJurnal = null;
            if ($bukuPembantu && $bukuPembantu->settled_by_jurnal_header_id) {
                $paymentJurnal = JurnalHeader::with('jurnalDetails.coa')
                    ->find($bukuPembantu->settled_by_jurnal_header_id);
            }
            // Also look for payment journal by description pattern
            if (!$paymentJurnal && $invoice) {
                $paymentJurnal = JurnalHeader::where('deskripsi', 'like', '%Penerimaan Pembayaran%' . $invoice->nomor_invoice . '%')
                    ->with('jurnalDetails.coa')
                    ->first();
            }
            $data['payment'] = $paymentJurnal;

            // Integrity checks
            $data['checks'] = $this->runIntegrityChecks($invoice, $jurnalHeader, $bukuPembantu);

        } elseif ($type === 'penjualan') {
            $penjualan = Penjualan::with(['klien', 'invoice.klien'])->findOrFail($id);

            $data['operational'] = [
                'ritase' => collect(),
                'penjualan' => collect([$penjualan]),
            ];

            $data['invoice'] = $penjualan->invoice;

            $jurnalHeader = null;
            if ($penjualan->invoice) {
                $jurnalHeader = JurnalHeader::where('referensi_type', Invoice::class)
                    ->where('referensi_id', $penjualan->invoice_id)
                    ->with('jurnalDetails.coa')
                    ->first();
            }
            $data['jurnal'] = $jurnalHeader;

            $bukuPembantu = null;
            if ($jurnalHeader) {
                $bukuPembantu = BukuPembantu::where('jurnal_header_id', $jurnalHeader->id)
                    ->with('contactable')
                    ->first();
            }
            $data['buku_pembantu'] = $bukuPembantu;

            $paymentJurnal = null;
            if ($bukuPembantu && $bukuPembantu->settled_by_jurnal_header_id) {
                $paymentJurnal = JurnalHeader::with('jurnalDetails.coa')
                    ->find($bukuPembantu->settled_by_jurnal_header_id);
            }
            $data['payment'] = $paymentJurnal;

            $data['checks'] = $penjualan->invoice
                ? $this->runIntegrityChecks($penjualan->invoice, $jurnalHeader, $bukuPembantu)
                : ['invoice_missing' => true];

        } elseif ($type === 'buku-pembantu') {
            $bp = BukuPembantu::with(['contactable', 'jurnalHeader.jurnalDetails.coa', 'jurnalHeader.referensi'])->findOrFail($id);

            $invoice = null;
            if ($bp->jurnalHeader && $bp->jurnalHeader->referensi_type === Invoice::class) {
                $invoice = Invoice::with(['klien', 'ritase', 'penjualan'])->find($bp->jurnalHeader->referensi_id);
            }

            $data['operational'] = [
                'ritase' => $invoice ? $invoice->ritase : collect(),
                'penjualan' => $invoice ? $invoice->penjualan : collect(),
            ];

            $data['invoice'] = $invoice;
            $data['jurnal'] = $bp->jurnalHeader;
            $data['buku_pembantu'] = $bp;

            $paymentJurnal = null;
            if ($bp->settled_by_jurnal_header_id) {
                $paymentJurnal = JurnalHeader::with('jurnalDetails.coa')
                    ->find($bp->settled_by_jurnal_header_id);
            }
            $data['payment'] = $paymentJurnal;

            $data['checks'] = $invoice
                ? $this->runIntegrityChecks($invoice, $bp->jurnalHeader, $bp)
                : [];
        } else {
            abort(404, 'Tipe tracing tidak dikenali.');
        }

        return view('admin.tracing.show', compact('data', 'type', 'id'));
    }

    /**
     * Run audit / diagnostic checks.
     */
    public function auditCheck()
    {
        Gate::authorize('view_buku_pembantu');

        $issues = [];

        // 1. Invoices with status Sent/Paid but NO journal
        $noJournalInvoices = Invoice::whereIn('status', ['Sent', 'Paid'])
            ->whereDoesntHave('jurnalHeaders')
            ->with('klien')
            ->get();
        foreach ($noJournalInvoices as $inv) {
            $issues[] = [
                'type' => 'missing_journal',
                'severity' => 'danger',
                'message' => 'Invoice ' . $inv->nomor_invoice . ' (' . ($inv->klien->nama_klien ?? '?') . ') status ' . $inv->status . ' belum memiliki jurnal umum.',
                'link' => route('admin.tracing.show', ['type' => 'invoice', 'id' => $inv->id]),
            ];
        }

        // 2. Journals for invoices but no Buku Pembantu
        // bukuPembantu relation may not exist on JurnalHeader, so do manual query
        $jhIdsWithBP = BukuPembantu::whereNotNull('jurnal_header_id')->pluck('jurnal_header_id')->unique();
        $invoiceJournals = JurnalHeader::where('referensi_type', Invoice::class)->get();
        foreach ($invoiceJournals as $jh) {
            if (!$jhIdsWithBP->contains($jh->id)) {
                $inv = Invoice::with('klien')->find($jh->referensi_id);
                if ($inv && in_array($inv->status, ['Sent', 'Paid'])) {
                    $issues[] = [
                        'type' => 'missing_buku_pembantu',
                        'severity' => 'warning',
                        'message' => "Jurnal {$jh->nomor_referensi} untuk Invoice {$inv->nomor_invoice} belum tercatat di Buku Pembantu.",
                        'link' => route('admin.tracing.show', ['type' => 'invoice', 'id' => $inv->id]),
                    ];
                }
            }
        }

        // 3. Penjualan without invoice
        $uninvoicedPenjualan = Penjualan::whereNull('invoice_id')
            ->with('klien')
            ->get();
        foreach ($uninvoicedPenjualan as $p) {
            $issues[] = [
                'type' => 'uninvoiced_penjualan',
                'severity' => 'info',
                'message' => 'Penjualan ' . $p->jenis_produk . ' (' . $p->tanggal->format('d/m/Y') . ') ke ' . ($p->klien->nama_klien ?? '?') . ' sebesar Rp ' . number_format($p->total_harga, 0, ',', '.') . ' belum di-invoice.',
                'link' => route('admin.penjualan.show', $p->id),
            ];
        }

        // 4. Invoice total mismatch with journal debit
        $sentPaidInvoices = Invoice::whereIn('status', ['Sent', 'Paid'])
            ->with('klien')
            ->get();
        foreach ($sentPaidInvoices as $inv) {
            $jh = JurnalHeader::where('referensi_type', Invoice::class)
                ->where('referensi_id', $inv->id)
                ->first();
            if ($jh) {
                $totalDebit = JurnalDetail::where('jurnal_header_id', $jh->id)->sum('debit');
                if (abs($totalDebit - $inv->total_tagihan) > 1) {
                    $issues[] = [
                        'type' => 'amount_mismatch',
                        'severity' => 'danger',
                        'message' => "Selisih nominal: Invoice {$inv->nomor_invoice} = Rp " . number_format($inv->total_tagihan, 0, ',', '.') . " vs Jurnal Debit = Rp " . number_format($totalDebit, 0, ',', '.'),
                        'link' => route('admin.tracing.show', ['type' => 'invoice', 'id' => $inv->id]),
                    ];
                }
            }
        }

        // 5. Buku Pembantu with status 'pending' but Invoice is 'Paid'
        $bpPendingPaid = BukuPembantu::where('tipe', 'piutang')
            ->where('status', 'pending')
            ->whereHas('jurnalHeader', function ($q) {
                $q->where('referensi_type', Invoice::class);
            })
            ->with('jurnalHeader')
            ->get();
        foreach ($bpPendingPaid as $bp) {
            $inv = Invoice::find($bp->jurnalHeader->referensi_id);
            if ($inv && $inv->status === 'Paid') {
                $issues[] = [
                    'type' => 'status_mismatch',
                    'severity' => 'warning',
                    'message' => "Buku Pembantu masih 'pending' tapi Invoice {$inv->nomor_invoice} sudah 'Paid'. Perlu sinkronisasi.",
                    'link' => route('admin.tracing.show', ['type' => 'invoice', 'id' => $inv->id]),
                ];
            }
        }

        // Sort by severity
        $severityOrder = ['danger' => 0, 'warning' => 1, 'info' => 2];
        usort($issues, fn($a, $b) => ($severityOrder[$a['severity']] ?? 3) <=> ($severityOrder[$b['severity']] ?? 3));

        return view('admin.tracing.audit', compact('issues'));
    }

    /**
     * Sync discrepancies: fix out-of-sync BukuPembantu statuses.
     */
    public function syncDiscrepancies()
    {
        Gate::authorize('view_buku_pembantu');

        $fixed = 0;

        DB::transaction(function () use (&$fixed) {
            // Fix 1: BP pending but jumlah <= terbayar
            $stale = BukuPembantu::where('status', 'pending')
                ->whereColumn('terbayar', '>=', 'jumlah')
                ->get();
            foreach ($stale as $bp) {
                $bp->save(); // Triggers saving hook that auto-corrects status
                $fixed++;
            }

            // Fix 2: BP pending but linked Invoice is Paid (re-trigger observer)
            $bpPendingPaid = BukuPembantu::where('tipe', 'piutang')
                ->where('status', 'pending')
                ->whereHas('jurnalHeader', function ($q) {
                    $q->where('referensi_type', Invoice::class);
                })
                ->with('jurnalHeader')
                ->get();

            foreach ($bpPendingPaid as $bp) {
                $inv = Invoice::find($bp->jurnalHeader->referensi_id);
                if ($inv && $inv->status === 'Paid') {
                    $bp->update([
                        'status' => 'lunas',
                        'terbayar' => $bp->jumlah,
                    ]);
                    $fixed++;
                }
            }

            // Fix 3: Regenerate missing journals for Sent/Paid invoices
            $noJournalInvoices = Invoice::whereIn('status', ['Sent', 'Paid'])
                ->whereDoesntHave('jurnalHeaders')
                ->get();
            foreach ($noJournalInvoices as $inv) {
                // Re-saving the invoice triggers InvoiceObserver which recreates the journal
                $inv->save();
                $fixed++;
            }
        });

        return back()->with('success', "Sinkronisasi selesai. {$fixed} data berhasil diperbaiki.");
    }

    /**
     * Run integrity checks for a specific invoice.
     */
    private function runIntegrityChecks(?Invoice $invoice, ?JurnalHeader $jurnal, ?BukuPembantu $bp): array
    {
        $checks = [];

        if (!$invoice) {
            $checks['invoice_missing'] = true;
            return $checks;
        }

        // Check 1: Journal exists
        $checks['has_jurnal'] = $jurnal !== null;

        // Check 2: Buku Pembantu exists
        $checks['has_bp'] = $bp !== null;

        // Check 3: Amount match between Invoice and Journal
        if ($jurnal) {
            $totalDebit = JurnalDetail::where('jurnal_header_id', $jurnal->id)->sum('debit');
            $checks['amount_match'] = abs($totalDebit - $invoice->total_tagihan) <= 1;
            $checks['jurnal_debit'] = $totalDebit;
        }

        // Check 4: Status consistency
        if ($bp) {
            if ($invoice->status === 'Paid') {
                $checks['status_consistent'] = $bp->status === 'lunas';
            } elseif ($invoice->status === 'Sent') {
                $checks['status_consistent'] = $bp->status === 'pending';
            } else {
                $checks['status_consistent'] = true;
            }
        }

        return $checks;
    }

    /**
     * Count total discrepancies for dashboard metric.
     */
    private function countDiscrepancies(): int
    {
        $count = 0;

        // Invoices Sent/Paid without journal
        $count += Invoice::whereIn('status', ['Sent', 'Paid'])
            ->whereDoesntHave('jurnalHeaders')
            ->count();

        // BP pending but terbayar >= jumlah
        $count += BukuPembantu::where('status', 'pending')
            ->whereColumn('terbayar', '>=', 'jumlah')
            ->where('jumlah', '>', 0)
            ->count();

        return $count;
    }
}
