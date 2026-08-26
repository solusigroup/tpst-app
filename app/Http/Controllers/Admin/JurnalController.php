<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JurnalHeader;
use App\Models\JurnalKas;
use App\Models\Coa;
use App\Models\Invoice;
use App\Models\JurnalTemplate;
use App\Models\WageCalculation;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_jurnal');
        $query = JurnalHeader::with('jurnalDetails.coa');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_referensi', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'unposted') {
                $query->where(function($q) {
                    $q->where('status', 'unposted')->orWhere('status', 'draft');
                });
            } else {
                $query->where('status', $request->status);
            }
        }
        if ($request->filled('nominal')) {
            $nominal = $this->parseNominal($request->nominal);
            if ($nominal !== null) {
                $posisi = strtolower($request->input('posisi', $request->input('sisi', '')));
                $query->where(function ($q) use ($nominal, $posisi) {
                    if ($posisi === 'debit' || $posisi === 'debet') {
                        $q->whereHas('jurnalDetails', function ($d) use ($nominal) {
                            $d->where('debit', $nominal);
                        });
                    } elseif ($posisi === 'kredit') {
                        $q->whereHas('jurnalDetails', function ($d) use ($nominal) {
                            $d->where('kredit', $nominal);
                        });
                    } else {
                        $q->whereHas('jurnalDetails', function ($d) use ($nominal) {
                            $d->where('debit', $nominal)
                              ->orWhere('kredit', $nominal);
                        });
                    }
                });
            }
        }
        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        $jurnals = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('admin.jurnal.index', compact('jurnals'));
    }

    public function show(JurnalHeader $jurnal)
    {
        Gate::authorize('view_jurnal');
        $jurnal->load(['jurnalDetails.coa', 'jurnalDetails.contactable']);
        return view('admin.jurnal.show', compact('jurnal'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create_jurnal');
        $coas = Coa::orderBy('kode_akun')->get();

        $defaultTanggal = $request->filled('tanggal') ? $request->tanggal : '';
        $defaultDeskripsi = $request->input('deskripsi', '');
        $defaultNominal = (float) $request->input('nominal', 0);
        $refType = is_string($request->ref_type) ? urldecode($request->ref_type) : null;
        $refId = $request->ref_id;

        // Auto-lookup Bank Jatim COA or target COA from reconciliation
        $bankJatimCoaId = null;
        if ($request->filled('target_coa_id') || $request->filled('rekonsiliasi_target_coa')) {
            $targetId = $request->target_coa_id ?? $request->rekonsiliasi_target_coa;
            $targetCoa = Coa::find($targetId);
            if ($targetCoa) {
                $bankJatimCoaId = $targetCoa->id;
            }
        }
        if (!$bankJatimCoaId) {
            $bankJatimCoa = Coa::where('nama_akun', 'like', '%Bank Jatim%')->first()
                ?: Coa::where('kode_akun', 'like', '11%')->where('nama_akun', 'like', '%Bank%')->first();
            $bankJatimCoaId = $bankJatimCoa ? $bankJatimCoa->id : null;
        }

        $piutangCoaId = null;
        $klienContactId = null;
        $isPelunasan = false;

        // Inisialisasi baris jurnal default (Row 0 & Row 1)
        $row0CoaId = $bankJatimCoaId;
        $row0Debit = $defaultNominal;
        $row0Kredit = 0;
        $row1CoaId = null;
        $row1Debit = 0;
        $row1Kredit = $defaultNominal;

        if ($refType && $refId) {
            if ($refType === Invoice::class) {
                $invoice = Invoice::with('klien')->find($refId);
                if ($invoice) {
                    $isPelunasan = true;
                    $sisaNominal = (float) ($invoice->total_tagihan - ($invoice->uang_muka ?? 0));
                    $defaultNominal = $sisaNominal > 0 ? $sisaNominal : (float) $invoice->total_tagihan;
                    $defaultDeskripsi = "Penerimaan Pembayaran Pelunasan Invoice {$invoice->nomor_invoice} - " . ($invoice->klien->nama_klien ?? '');

                    $targetCategory = 'piutang_swasta';
                    if ($invoice->klien) {
                        $klienContactId = "App\\Models\\Klien:{$invoice->klien_id}";
                        if ($invoice->klien->jenis === 'Offtaker') {
                            $targetCategory = 'piutang_offtaker';
                        } elseif ($invoice->klien->jenis === 'DLH') {
                            $targetCategory = 'piutang_dlh';
                        }
                    }

                    $piutangCoa = Coa::where('tipe', 'Asset')
                        ->where('kategori_buku_pembantu', $targetCategory)
                        ->first()
                        ?: Coa::where('tipe', 'Asset')->where('nama_akun', 'like', '%Piutang%')->first();

                    $piutangCoaId = $piutangCoa ? $piutangCoa->id : null;

                    $row0CoaId = $bankJatimCoaId;
                    $row0Debit = $defaultNominal;
                    $row0Kredit = 0;
                    $row1CoaId = $piutangCoaId;
                    $row1Debit = 0;
                    $row1Kredit = $defaultNominal;
                }
            } elseif ($refType === WageCalculation::class) {
                $wage = WageCalculation::find($refId);
                if ($wage) {
                    $defaultDeskripsi = "Pembayaran Gaji Karyawan Borongan ID-{$wage->id}";
                    $defaultNominal = $wage->total_wage;
                    $row0Debit = $defaultNominal;
                    $row1Kredit = $defaultNominal;
                }
            }
        } elseif ($request->filled('tipe') || $request->input('source') === 'rekonsiliasi_bank') {
            // Logika khusus dari Rekonsiliasi Bank
            $tipe = strtolower($request->tipe);
            $deskripsiLower = strtolower($defaultDeskripsi);

            if ($tipe === 'keluar' || $tipe === 'pengeluaran' || $tipe === 'debit') {
                // Bank Debit (Uang Keluar / Tarikan / Beban Admin Bank)
                // Di pembukuan: Kas/Bank berkurang (Kredit), Akun Lawan/Beban bertambah (Debet)
                $suggestedLawanCoa = null;
                if (str_contains($deskripsiLower, 'adm') || str_contains($deskripsiLower, 'admin') || str_contains($deskripsiLower, 'biaya')) {
                    $suggestedLawanCoa = Coa::where('kode_akun', '8102')->first()
                        ?: Coa::where('nama_akun', 'like', '%administrasi bank%')->first()
                        ?: Coa::where('nama_akun', 'like', '%biaya admin%')->first();
                }

                $row0CoaId = $suggestedLawanCoa ? $suggestedLawanCoa->id : null;
                $row0Debit = $defaultNominal;
                $row0Kredit = 0;

                $row1CoaId = $bankJatimCoaId;
                $row1Debit = 0;
                $row1Kredit = $defaultNominal;
            } else {
                // Bank Kredit (Uang Masuk / Setoran / Pendapatan Bunga Bank)
                // Di pembukuan: Kas/Bank bertambah (Debet), Akun Lawan/Pendapatan bertambah (Kredit)
                $suggestedLawanCoa = null;
                if (str_contains($deskripsiLower, 'bunga') || str_contains($deskripsiLower, 'interest')) {
                    $suggestedLawanCoa = Coa::where('kode_akun', '7102')->first()
                        ?: Coa::where('nama_akun', 'like', '%pendapatan bunga%')->first()
                        ?: Coa::where('nama_akun', 'like', '%bunga bank%')->first();
                }

                $row0CoaId = $bankJatimCoaId;
                $row0Debit = $defaultNominal;
                $row0Kredit = 0;

                $row1CoaId = $suggestedLawanCoa ? $suggestedLawanCoa->id : null;
                $row1Debit = 0;
                $row1Kredit = $defaultNominal;
            }
        }

        $kliens = \App\Models\Klien::orderBy('nama_klien')->get();
        $vendors = \App\Models\Vendor::orderBy('nama_vendor')->get();
        $templates = JurnalTemplate::orderBy('nama')->get();

        return view('admin.jurnal.form', compact(
            'coas', 'defaultTanggal', 'defaultDeskripsi', 'defaultNominal', 'refType', 'refId',
            'kliens', 'vendors', 'templates', 'bankJatimCoaId', 'piutangCoaId',
            'klienContactId', 'isPelunasan',
            'row0CoaId', 'row0Debit', 'row0Kredit', 'row1CoaId', 'row1Debit', 'row1Kredit'
        ));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_jurnal');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:5120',
            'details' => 'required|array|min:2',
            'details.*.coa_id' => 'required|exists:coa,id',
            'details.*.debit' => 'nullable|numeric|min:0',
            'details.*.kredit' => 'nullable|numeric|min:0',
            'details.*.contactable_type_id' => 'nullable|string',
            'referensi_type' => 'nullable|string',
            'referensi_id' => 'nullable|integer',
        ]);

        // Validasi total debit == total kredit (prinsip double-entry)
        $totalDebit = collect($validated['details'])->sum(fn($d) => (float) ($d['debit'] ?? 0));
        $totalKredit = collect($validated['details'])->sum(fn($d) => (float) ($d['kredit'] ?? 0));
        if (abs($totalDebit - $totalKredit) > 0.01) {
            return back()->withInput()->withErrors([
                'details' => 'Total Debit (Rp ' . number_format($totalDebit, 0, ',', '.') . ') dan Kredit (Rp ' . number_format($totalKredit, 0, ',', '.') . ') harus seimbang.'
            ]);
        }

        // Validasi: setiap baris harus punya debit atau kredit > 0
        foreach ($validated['details'] as $i => $detail) {
            $d = (float) ($detail['debit'] ?? 0);
            $k = (float) ($detail['kredit'] ?? 0);
            if ($d <= 0 && $k <= 0) {
                return back()->withInput()->withErrors([
                    'details' => "Baris jurnal ke-" . ($i + 1) . " harus memiliki nilai Debit atau Kredit lebih dari 0."
                ]);
            }
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = \App\Helpers\ImageHelper::compressAndStore($request->file('bukti_transaksi'), 'jurnal-bukti');
        }

        // Memeriksa apakah jurnal untuk referensi transaksi ini sudah ada (mencegah double jurnal)
        if (!empty($validated['referensi_type']) && !empty($validated['referensi_id'])) {
            $existingJurnal = JurnalHeader::where('referensi_type', $validated['referensi_type'])
                ->where('referensi_id', $validated['referensi_id'])
                ->first();
                
            if ($existingJurnal) {
                return back()->withInput()->withErrors([
                    'referensi_id' => 'Jurnal untuk transaksi ini sudah ada (Nomor Jurnal: ' . $existingJurnal->nomor_referensi . '). Tidak dapat membuat jurnal ganda untuk satu transaksi.'
                ]);
            }
        }

        // Validasi ketersediaan saldo untuk akun Kas & Bank (awalan '11') di sisi Kredit
        $kasCredits = collect($validated['details'])
            ->filter(fn($d) => isset($d['kredit']) && $d['kredit'] > 0)
            ->groupBy('coa_id')
            ->map(fn($group) => $group->sum('kredit'));

        foreach ($kasCredits as $coaId => $kreditInput) {
            $coa = Coa::find($coaId);
            if ($coa && str_starts_with($coa->kode_akun, '11')) { // 11 adalah Kas & Bank
                $saldoAwal = \App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                    ->where('jurnal_header.status', 'posted')
                    ->where('jurnal_detail.coa_id', $coaId)
                    ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
                    ->value('saldo') ?? 0;

                if ($kreditInput > $saldoAwal) {
                    return back()->withInput()->withErrors([
                        'details' => 'Saldo akun ' . $coa->nama_akun . ' (' . $coa->kode_akun . ') tidak mencukupi. Sisa saldo saat ini: Rp ' . number_format($saldoAwal, 0, ',', '.')
                    ]);
                }
            }
        }

        $jurnal = DB::transaction(function () use ($validated, $buktiPath) {
            $jurnal = JurnalHeader::create([
                'tanggal' => $validated['tanggal'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'bukti_transaksi' => $buktiPath,
                'status' => 'unposted',
                'referensi_type' => $validated['referensi_type'] ?? null,
                'referensi_id' => $validated['referensi_id'] ?? null,
            ]);

            foreach ($validated['details'] as $detail) {
                $contactType = null;
                $contactId = null;
                if (!empty($detail['contactable_type_id']) && str_contains($detail['contactable_type_id'], ':')) {
                    [$contactType, $contactId] = explode(':', $detail['contactable_type_id']);
                }

                $jurnal->jurnalDetails()->create([
                    'coa_id' => $detail['coa_id'],
                    'debit' => $detail['debit'] ?? 0,
                    'kredit' => $detail['kredit'] ?? 0,
                    'contactable_type' => $contactType,
                    'contactable_id' => $contactId,
                ]);
            }

            return $jurnal;
        });

        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal berhasil dibuat.');
    }

    public function edit(JurnalHeader $jurnal)
    {
        Gate::authorize('update_jurnal');
        $jurnal->load('jurnalDetails.coa');
        $coas = \App\Models\Coa::orderBy('kode_akun')->get();
        $kliens = \App\Models\Klien::orderBy('nama_klien')->get();
        $vendors = \App\Models\Vendor::orderBy('nama_vendor')->get();

        // Soft warning jika jurnal sudah posted
        $warning = null;
        if ($jurnal->status === 'posted') {
            $warning = 'Perhatian: Jurnal ini sudah di-post. Perubahan akan mempengaruhi laporan keuangan yang sudah final.';
        }

        return view('admin.jurnal.form', compact('jurnal', 'coas', 'kliens', 'vendors', 'warning'));
    }

    public function update(Request $request, JurnalHeader $jurnal)
    {
        Gate::authorize('update_jurnal');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:5120',
            'details' => 'required|array|min:2',
            'details.*.coa_id' => 'required|exists:coa,id',
            'details.*.debit' => 'nullable|numeric|min:0',
            'details.*.kredit' => 'nullable|numeric|min:0',
            'details.*.contactable_type_id' => 'nullable|string',
        ]);

        // Validasi total debit == total kredit (prinsip double-entry)
        $totalDebit = collect($validated['details'])->sum(fn($d) => (float) ($d['debit'] ?? 0));
        $totalKredit = collect($validated['details'])->sum(fn($d) => (float) ($d['kredit'] ?? 0));
        if (abs($totalDebit - $totalKredit) > 0.01) {
            return back()->withInput()->withErrors([
                'details' => 'Total Debit (Rp ' . number_format($totalDebit, 0, ',', '.') . ') dan Kredit (Rp ' . number_format($totalKredit, 0, ',', '.') . ') harus seimbang.'
            ]);
        }

        // Validasi: setiap baris harus punya debit atau kredit > 0
        foreach ($validated['details'] as $i => $detail) {
            $d = (float) ($detail['debit'] ?? 0);
            $k = (float) ($detail['kredit'] ?? 0);
            if ($d <= 0 && $k <= 0) {
                return back()->withInput()->withErrors([
                    'details' => "Baris jurnal ke-" . ($i + 1) . " harus memiliki nilai Debit atau Kredit lebih dari 0."
                ]);
            }
        }

        $data = [
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ];

        if ($request->hasFile('bukti_transaksi')) {
            $data['bukti_transaksi'] = \App\Helpers\ImageHelper::compressAndStore($request->file('bukti_transaksi'), 'jurnal-bukti');
        }

        // Validasi ketersediaan saldo untuk akun Kas & Bank (awalan '11') di sisi Kredit
        $kasCredits = collect($validated['details'])
            ->filter(fn($d) => isset($d['kredit']) && $d['kredit'] > 0)
            ->groupBy('coa_id')
            ->map(fn($group) => $group->sum('kredit'));

        foreach ($kasCredits as $coaId => $kreditInput) {
            $coa = Coa::find($coaId);
            if ($coa && str_starts_with($coa->kode_akun, '11')) { // 11 adalah Kas & Bank
                $saldoAwal = \App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                    ->where('jurnal_header.status', 'posted')
                    ->where('jurnal_detail.coa_id', $coaId)
                    ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
                    ->value('saldo') ?? 0;

                // Kembalikan saldo sebelum direvisi eksisting (hitung efek jurnal lama) — HANYA jika status posted
                $saldoRestore = $saldoAwal;
                if ($jurnal->status === 'posted') {
                    $jurnalExistingCredit = \App\Models\JurnalDetail::where('jurnal_header_id', $jurnal->id)
                        ->where('coa_id', $coaId)
                        ->sum('kredit');
                    $jurnalExistingDebit = \App\Models\JurnalDetail::where('jurnal_header_id', $jurnal->id)
                        ->where('coa_id', $coaId)
                        ->sum('debit');
                    $saldoRestore = $saldoAwal + $jurnalExistingCredit - $jurnalExistingDebit;
                }

                if ($kreditInput > $saldoRestore) {
                    return back()->withInput()->withErrors([
                        'details' => 'Saldo akun ' . $coa->nama_akun . ' (' . $coa->kode_akun . ') tidak mencukupi. Sisa saldo saat ini: Rp ' . number_format($saldoRestore, 0, ',', '.')
                    ]);
                }
            }
        }

        DB::transaction(function () use ($jurnal, $data, $validated) {
            $jurnal->update($data);

            // Sync details — hapus dan buat ulang dalam transaction
            $jurnal->jurnalDetails()->get()->each->delete();
            foreach ($validated['details'] as $detail) {
                $contactType = null;
                $contactId = null;
                if (!empty($detail['contactable_type_id']) && str_contains($detail['contactable_type_id'], ':')) {
                    [$contactType, $contactId] = explode(':', $detail['contactable_type_id']);
                }

                $jurnal->jurnalDetails()->create([
                    'coa_id' => $detail['coa_id'],
                    'debit' => $detail['debit'] ?? 0,
                    'kredit' => $detail['kredit'] ?? 0,
                    'contactable_type' => $contactType,
                    'contactable_id' => $contactId,
                ]);
            }
        });

        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function destroy(JurnalHeader $jurnal)
    {
        Gate::authorize('delete_jurnal');

        // Soft warning: jika posted, tetap boleh hapus tapi beri warning
        $warningMsg = '';
        if ($jurnal->status === 'posted') {
            $warningMsg = ' (Perhatian: Jurnal yang sudah di-post telah dihapus. Laporan keuangan mungkin terpengaruh.)';
        }

        // Cukup panggil $jurnal->delete() — model event deleting di JurnalHeader
        // sudah menangani: hapus jurnalDetails, hapus bukti_transaksi, hapus BukuPembantu
        $jurnal->delete();

        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal berhasil dihapus.' . $warningMsg);
    }

    public function post(JurnalHeader $jurnal)
    {
        Gate::authorize('update_jurnal');
        $jurnal->update(['status' => 'posted']);
        if ($jurnal->referensi_type === JurnalKas::class) {
            $jurnal->referensi?->update(['status' => 'posted']);
        }
        return back()->with('success', 'Jurnal berhasil di-post.');
    }

    public function unpost(JurnalHeader $jurnal)
    {
        Gate::authorize('update_jurnal');
        $jurnal->update(['status' => 'unposted']);
        if ($jurnal->referensi_type === JurnalKas::class) {
            $jurnal->referensi?->update(['status' => 'unposted']);
        }
        return back()->with('warning', 'Jurnal di-unpost.');
    }

    public function storeTemplate(Request $request)
    {
        Gate::authorize('create_jurnal');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'template_details' => 'required|array|min:2',
            'template_details.*.coa_id' => 'required|exists:coa,id',
            'template_details.*.posisi' => 'required|in:debit,kredit',
        ]);

        JurnalTemplate::create([
            'tenant_id' => auth()->user()->getEffectiveTenantId(),
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'details' => $validated['template_details'],
        ]);

        return back()->with('success', 'Template jurnal berhasil disimpan.');
    }

    public function destroyTemplate(JurnalTemplate $jurnalTemplate)
    {
        Gate::authorize('create_jurnal');
        $jurnalTemplate->delete();
        return back()->with('success', 'Template jurnal berhasil dihapus.');
    }

    /**
     * Purge (permanently delete) a single jurnal with complete cascade cleanup.
     */
    public function purge(JurnalHeader $jurnal)
    {
        Gate::authorize('delete_jurnal');

        $nomorRef = $jurnal->nomor_referensi;

        DB::transaction(function () use ($jurnal) {
            // 1. Revert any BukuPembantu settlements made by this jurnal
            \App\Models\BukuPembantu::where('settled_by_jurnal_header_id', $jurnal->id)
                ->update([
                    'settled_by_jurnal_header_id' => null,
                    'terbayar' => 0,
                    'status' => 'pending',
                ]);

            // 2. Delete the jurnal (model's deleting hook handles:
            //    - jurnalDetails cascade delete (triggers JurnalDetailObserver@deleted)
            //    - bukti_transaksi file deletion
            //    - BukuPembantu where jurnal_header_id cleanup)
            $jurnal->delete();
        });

        return redirect()->route('admin.jurnal.index')
            ->with('success', "Jurnal {$nomorRef} berhasil di-purge.");
    }

    /**
     * Purge multiple selected jurnals.
     */
    public function purgeSelected(Request $request)
    {
        Gate::authorize('delete_jurnal');

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:jurnal_header,id',
        ]);

        $count = 0;
        DB::transaction(function () use ($request, &$count) {
            $jurnals = JurnalHeader::whereIn('id', $request->ids)->get();
            foreach ($jurnals as $jurnal) {
                // Revert BukuPembantu settlements
                \App\Models\BukuPembantu::where('settled_by_jurnal_header_id', $jurnal->id)
                    ->update([
                        'settled_by_jurnal_header_id' => null,
                        'terbayar' => 0,
                        'status' => 'pending',
                    ]);

                $jurnal->delete();
                $count++;
            }
        });

        return redirect()->route('admin.jurnal.index')
            ->with('success', "{$count} jurnal berhasil di-purge.");
    }

    /**
     * Parse nominal input from string or number into float.
     * Supports formats like "50000", "50.000", "1.500.000", "Rp 50.000", "100.000,50".
     */
    private function parseNominal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $raw = trim((string) $value);
        $raw = preg_replace('/[^\d.,]/', '', $raw);
        if ($raw === '') {
            return null;
        }

        if (preg_match('/^[\d.]+,\d{1,2}$/', $raw)) {
            $raw = str_replace('.', '', $raw);
            $raw = str_replace(',', '.', $raw);
        } else {
            $raw = str_replace(',', '', $raw);
            if (substr_count($raw, '.') > 1 || preg_match('/^\d{1,3}(\.\d{3})+$/', $raw)) {
                $raw = str_replace('.', '', $raw);
            }
        }

        return is_numeric($raw) ? (float) $raw : null;
    }
}
