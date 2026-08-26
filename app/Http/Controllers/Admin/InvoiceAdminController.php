<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Klien;
use App\Models\BukuPembantu;
use App\Models\JurnalHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class InvoiceAdminController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_invoice');

        try {
            $query = Invoice::with('klien');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor_invoice', 'like', '%' . $search . '%')
                      ->orWhereHas('klien', function($qKlien) use ($search) {
                          $qKlien->where('nama_klien', 'like', '%' . $search . '%');
                      });
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('jenis')) {
                $query->whereHas('klien', function($q) use ($request) {
                    $q->where('jenis', $request->jenis);
                });
            }

            $invoices = $query->orderByDesc('tanggal_invoice')->paginate(15)->withQueryString();
            $dlhClients = Klien::where('jenis', 'DLH')->orderBy('nama_klien')->get();
            $masterDlh = Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first() ?? $dlhClients->first();

            return view('admin.invoice.index', compact('invoices', 'dlhClients', 'masterDlh'));
        } catch (\Throwable $e) {
            \Log::error('Error loading invoices index: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response(
                '<div style="font-family:sans-serif; padding:40px; max-width:800px; margin:0 auto;">' .
                '<h2 style="color:#e53e3e;">Terjadi Kesalahan Saat Memuat Invoice</h2>' .
                '<p><strong>Pesan Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>' .
                '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>' .
                '<hr><p><a href="' . route('admin.dashboard') . '" style="color:#3182ce;">Kembali ke Dashboard</a></p>' .
                '</div>',
                500
            );
        }
    }

    public function create()
    {
        Gate::authorize('create_invoice');
        $kliens = Klien::orderBy('nama_klien')->get();
        $coas = \App\Models\Coa::where('tipe', 'Asset')
            ->where(function ($q) {
                $q->where('kode_akun', 'like', '11%')
                  ->orWhere('nama_akun', 'like', '%Kas%')
                  ->orWhere('nama_akun', 'like', '%Bank%');
            })
            ->where('klasifikasi', 'Aset Lancar')
            ->orderBy('kode_akun')
            ->get();
        return view('admin.invoice.form', compact('kliens', 'coas'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_invoice');

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'periode_bulan' => 'required|string',
            'periode_tahun' => 'required|string',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'total_tagihan' => 'required|numeric|min:0',
            'uang_muka' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Sent,Paid,Canceled',
            'keterangan' => 'nullable|string',
            'deskripsi_layanan' => 'nullable|string',
            'selected_ritase' => 'nullable|array',
            'selected_ritase.*' => 'exists:ritase,id',
            'selected_penjualan' => 'nullable|array',
            'selected_penjualan.*' => 'exists:penjualan,id',
            'coa_pembayaran_id' => 'nullable|exists:coa,id',
        ]);

        $invoiceData = collect($validated)->except(['selected_ritase', 'selected_penjualan'])->toArray();
        $invoice = Invoice::create($invoiceData);

        // Attach Ritase
        if (!empty($validated['selected_ritase'])) {
            \App\Models\Ritase::whereIn('id', $validated['selected_ritase'])->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);
        }

        // Attach Penjualan
        if (!empty($validated['selected_penjualan'])) {
            \App\Models\Penjualan::whereIn('id', $validated['selected_penjualan'])->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);
        }

        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function show(Invoice $invoice)
    {
        Gate::authorize('view_invoice');
        $invoice->load(['klien', 'ritase', 'penjualan']);
        return view('admin.invoice.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        Gate::authorize('update_invoice');
        $kliens = Klien::orderBy('nama_klien')->get();
        $coas = \App\Models\Coa::where('tipe', 'Asset')
            ->where(function ($q) {
                $q->where('kode_akun', 'like', '11%')
                  ->orWhere('nama_akun', 'like', '%Kas%')
                  ->orWhere('nama_akun', 'like', '%Bank%');
            })
            ->where('klasifikasi', 'Aset Lancar')
            ->orderBy('kode_akun')
            ->get();
        return view('admin.invoice.form', compact('invoice', 'kliens', 'coas'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        Gate::authorize('update_invoice');

        $validated = $request->validate([
            'klien_id' => 'required|exists:klien,id',
            'periode_bulan' => 'required|string',
            'periode_tahun' => 'required|string',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'total_tagihan' => 'required|numeric|min:0',
            'uang_muka' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Sent,Paid,Canceled',
            'keterangan' => 'nullable|string',
            'deskripsi_layanan' => 'nullable|string',
            'selected_ritase' => 'nullable|array',
            'selected_ritase.*' => 'exists:ritase,id',
            'selected_penjualan' => 'nullable|array',
            'selected_penjualan.*' => 'exists:penjualan,id',
            'coa_pembayaran_id' => 'nullable|exists:coa,id',
        ]);

        $invoiceData = collect($validated)->except(['selected_ritase', 'selected_penjualan'])->toArray();
        $invoice->update($invoiceData);

        // Sync Ritase: nullify old attachments first
        \App\Models\Ritase::where('invoice_id', $invoice->id)->update(['invoice_id' => null, 'status_invoice' => 'Draft']);
        if (!empty($validated['selected_ritase'])) {
            \App\Models\Ritase::whereIn('id', $validated['selected_ritase'])->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);
        }

        // Sync Penjualan: nullify old attachments first
        \App\Models\Penjualan::where('invoice_id', $invoice->id)->update(['invoice_id' => null, 'status_invoice' => 'Draft']);
        if (!empty($validated['selected_penjualan'])) {
            \App\Models\Penjualan::whereIn('id', $validated['selected_penjualan'])->update([
                'invoice_id' => $invoice->id,
                'status_invoice' => $invoice->status,
            ]);
        }

        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice)
    {
        Gate::authorize('delete_invoice');
        $invoice->delete();
        return redirect()->route('admin.invoice.index')->with('success', 'Invoice berhasil dihapus.');
    }

    public function mergeDrafts()
    {
        Gate::authorize('update_invoice');
        
        $masterDLH = Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first();
        $allDrafts = Invoice::with('klien')->where('status', 'Draft')->get();
        
        // 1. First, redirect all DLH-type invoices to the Master DLH client ID
        if ($masterDLH) {
            foreach ($allDrafts as $draft) {
                if ($draft->klien && $draft->klien->jenis === 'DLH' && $draft->klien_id != $masterDLH->id) {
                    // Update this draft to belong to Master DLH so it gets grouped in the next step
                    $draft->update(['klien_id' => $masterDLH->id]);
                }
            }
            // Refresh drafts after redirection
            $allDrafts = Invoice::with('klien')->where('status', 'Draft')->get();
        }

        // 2. Group by klien_id, periode_bulan, and periode_tahun to ensure we don't merge across periods
        $groupedDrafts = $allDrafts->groupBy(function($item) {
            // Normalize month to integer string to handle '04' vs '4'
            $month = (int)$item->periode_bulan;
            return $item->klien_id . '-' . $month . '-' . $item->periode_tahun;
        });
        
        $mergedCount = 0;
        DB::transaction(function () use ($groupedDrafts, &$mergedCount) {
            foreach ($groupedDrafts as $key => $invoices) {
                if ($invoices->count() > 1) {
                    $master = $invoices->first();
                    $others = $invoices->slice(1);
                    
                    foreach ($others as $other) {
                        \App\Models\Ritase::where('invoice_id', $other->id)->update([
                            'invoice_id' => $master->id
                        ]);
                        
                        \App\Models\Penjualan::where('invoice_id', $other->id)->update([
                            'invoice_id' => $master->id
                        ]);
                        
                        $other->delete();
                        $mergedCount++;
                    }
                    
                    // Force recalculate and update totals
                    $master->recalculateTotals();

                    $master->update([
                        'coa_pembayaran_id' => $master->coa_pembayaran_id ?? \App\Models\Coa::where('kode_akun', '1130')->value('id'),
                        'keterangan' => empty($master->keterangan) ? 'Merged automatically' : $master->keterangan . ' (Merged)'
                    ]);
                }
            }
        });

        if ($mergedCount > 0) {
            return redirect()->route('admin.invoice.index')->with('success', "$mergedCount invoice draft berhasil digabungkan.");
        }
        return redirect()->route('admin.invoice.index')->with('info', 'Tidak ada draft invoice untuk klien yang sama yang perlu digabungkan.');
    }
    public function syncDlhItems($id)
    {
        Gate::authorize('update_invoice');
        
        $invoice = Invoice::with('klien')->findOrFail($id);
        
        if (!$invoice->klien || $invoice->klien->jenis !== 'DLH') {
            return back()->with('error', 'Hanya invoice DLH yang dapat disinkronkan otomatis.');
        }

        $count = 0;
        DB::transaction(function () use ($invoice, &$count) {
            // Find all ritase from DLH type clients in the same month/year
            $missingRitase = \App\Models\Ritase::whereHas('klien', function($q) {
                    $q->where('jenis', 'DLH');
                })
                ->whereYear('waktu_masuk', $invoice->periode_tahun)
                ->whereMonth('waktu_masuk', $invoice->periode_bulan)
                ->where(function($q) use ($invoice) {
                    $q->whereNull('invoice_id')->orWhere('invoice_id', '!=', $invoice->id);
                })
                ->where('status_invoice', '!=', 'Paid') // Don't steal from paid invoices
                ->where('is_approved', 1)
                ->get();

            foreach ($missingRitase as $ritase) {
                $ritase->update([
                    'invoice_id' => $invoice->id,
                    'status_invoice' => $invoice->status,
                    'status' => 'selesai' // Ensure status is consistent
                ]);
                $count++;
            }

            // Also check Penjualan
            $missingPenjualan = \App\Models\Penjualan::whereHas('klien', function($q) {
                    $q->where('jenis', 'DLH');
                })
                ->whereYear('tanggal', $invoice->periode_tahun)
                ->whereMonth('tanggal', $invoice->periode_bulan)
                ->where(function($q) use ($invoice) {
                    $q->whereNull('invoice_id')->orWhere('invoice_id', '!=', $invoice->id);
                })
                ->get();

            foreach ($missingPenjualan as $p) {
                $p->update(['invoice_id' => $invoice->id]);
                $count++;
            }

            if ($count > 0) {
                $invoice->recalculateTotals();
            }
        });

        return back()->with('success', "Berhasil menyinkronkan $count data baru ke invoice ini.");
    }

    public function recalculate(Invoice $invoice)
    {
        Gate::authorize('update_invoice');
        
        DB::transaction(function () use ($invoice) {
            $invoice->recalculateTotals();
        });

        return back()->with('success', 'Biaya invoice berhasil dihitung ulang.');
    }

    public function sendWhatsappReminder(Invoice $invoice)
    {
        Gate::authorize('view_invoice'); // Or create a specific permission for this
        
        $invoice->load('klien');
        $klien = $invoice->klien;

        if (!$klien) {
            return back()->with('error', 'Klien tidak ditemukan untuk invoice ini.');
        }

        if (!in_array($klien->jenis, ['Swasta', 'Offtaker'])) {
            return back()->with('error', 'Pengingat WA hanya dapat dikirim ke klien Swasta dan Offtaker.');
        }

        if (in_array($invoice->status, ['Paid', 'Canceled'])) {
            return back()->with('error', 'Tidak dapat mengirim pengingat karena status invoice sudah ' . $invoice->status . '.');
        }

        if (empty($klien->kontak)) {
            return back()->with('error', 'Nomor kontak klien belum diisi. Silakan lengkapi data klien terlebih dahulu.');
        }

        $bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
        $periode = ($bulan[$invoice->periode_bulan] ?? $invoice->periode_bulan) . ' ' . $invoice->periode_tahun;
        $totalTagihan = 'Rp ' . number_format($invoice->total_tagihan, 0, ',', '.');
        $tglJatuhTempo = \Carbon\Carbon::parse($invoice->tanggal_jatuh_tempo)->translatedFormat('d F Y');
        
        $pesan = "Halo {$klien->nama_klien},\n\n";
        $pesan .= "Ini adalah pengingat pembayaran Invoice No *{$invoice->nomor_invoice}* untuk periode *{$periode}* sebesar *{$totalTagihan}*.\n\n";
        $pesan .= "Harap segera melakukan pembayaran sebelum *{$tglJatuhTempo}*.\n\n";
        $pesan .= "Terima kasih.";

        // Format phone number to international format (replace leading 0 with 62)
        $phone = preg_replace('/[^0-9]/', '', $klien->kontak);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $url = "https://api.whatsapp.com/send?phone={$phone}&text=" . urlencode($pesan);

        return redirect()->away($url);
    }

    public function swastaLunas(Request $request)
    {
        Gate::authorize('view_invoice');
        
        $tab = $request->input('tab', 'clients');
        $search = $request->input('search');
        
        // Calculate summary metrics for the header cards
        // 1. Total Swasta clients with paid invoices
        $totalPaidClients = Klien::where('jenis', 'Swasta')
            ->whereHas('invoices', function ($q) {
                $q->where('status', 'Paid');
            })->count();
            
        // 2. Total Paid Invoices from Swasta clients
        $totalPaidInvoices = Invoice::where('status', 'Paid')
            ->whereHas('klien', function ($q) {
                $q->where('jenis', 'Swasta');
            })->count();
            
        // 3. Total amount collected from Swasta clients
        $totalCollected = Invoice::where('status', 'Paid')
            ->whereHas('klien', function ($q) {
                $q->where('jenis', 'Swasta');
            })->sum('total_tagihan');

        // 4. Total active receivables (piutang) from Sent invoices of Swasta clients
        $totalOutstanding = Invoice::where('status', 'Sent')
            ->whereHas('klien', function ($q) {
                $q->where('jenis', 'Swasta');
            })->sum(DB::raw('total_tagihan - uang_muka'));

        $clients = null;
        $invoices = null;

        if ($tab === 'invoices') {
            $query = Invoice::with('klien')
                ->whereHas('klien', function ($q) {
                    $q->where('jenis', 'Swasta');
                })
                ->where('status', 'Paid');

            if ($request->filled('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('nomor_invoice', 'like', '%' . $search . '%')
                      ->orWhereHas('klien', function($qk) use ($search) {
                          $qk->where('nama_klien', 'like', '%' . $search . '%');
                      });
                });
            }

            if ($request->export === 'excel') {
                $invoiceList = $query->orderByDesc('tanggal_invoice')->get();
            } else {
                $invoices = $query->orderByDesc('tanggal_invoice')->paginate(15)->withQueryString();
                $invoiceList = $invoices->getCollection();
            }

            // Efficiently populate tanggal_pelunasan for invoiceList (preventing N+1)
            $invoiceIds = $invoiceList->pluck('id');

            $payJhs = JurnalHeader::where('referensi_type', Invoice::class)
                ->whereIn('referensi_id', $invoiceIds)
                ->where('deskripsi', 'like', '%Penerimaan Pembayaran%')
                ->get()
                ->keyBy('referensi_id');

            $bps = BukuPembantu::whereHas('jurnalHeader', function($q) use ($invoiceIds) {
                    $q->where('referensi_type', Invoice::class)
                      ->whereIn('referensi_id', $invoiceIds);
                })
                ->whereNotNull('settled_by_jurnal_header_id')
                ->with('jurnalHeader')
                ->get()
                ->keyBy(function($item) {
                    return $item->jurnalHeader->referensi_id;
                });

            $settledJhIds = $bps->pluck('settled_by_jurnal_header_id')->filter();
            $settledJhs = JurnalHeader::whereIn('id', $settledJhIds)->get()->keyBy('id');

            foreach ($invoiceList as $inv) {
                $tgl = null;
                if (isset($payJhs[$inv->id]) && $payJhs[$inv->id]->tanggal) {
                    $tgl = $payJhs[$inv->id]->tanggal;
                } elseif (isset($bps[$inv->id])) {
                    $sId = $bps[$inv->id]->settled_by_jurnal_header_id;
                    if (isset($settledJhs[$sId]) && $settledJhs[$sId]->tanggal) {
                        $tgl = $settledJhs[$sId]->tanggal;
                    }
                }
                if (!$tgl) {
                    $tgl = $inv->updated_at;
                }
                $inv->tanggal_pelunasan = \Carbon\Carbon::parse($tgl);
            }

            if ($request->export === 'excel') {
                $data = compact('tab', 'invoiceList', 'totalPaidClients', 'totalPaidInvoices', 'totalCollected', 'totalOutstanding');
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\LaporanExcelExport('admin.invoice.exports.swasta-lunas-export', $data),
                    'Klien_Swasta_Lunas_Invoice_' . date('Ymd_His') . '.xlsx'
                );
            }
        } else {
            // Default to 'clients' tab
            $query = Klien::where('jenis', 'Swasta')
                ->whereHas('invoices', function ($q) {
                    $q->where('status', 'Paid');
                });

            if ($request->filled('search')) {
                $query->where('nama_klien', 'like', '%' . $search . '%');
            }

            $query->withCount(['invoices' => function ($q) {
                    $q->where('status', 'Paid');
                }])
                ->withSum(['invoices' => function ($q) {
                    $q->where('status', 'Paid');
                }], 'total_tagihan')
                ->orderByDesc('invoices_sum_total_tagihan');

            if ($request->export === 'excel') {
                $clientList = $query->get();
            } else {
                $clients = $query->paginate(15)->withQueryString();
                $clientList = $clients->getCollection();
            }

            // Load active outstanding receivables for each client (preventing N+1)
            $clientIds = $clientList->pluck('id');
            $outstandings = Invoice::whereIn('klien_id', $clientIds)
                ->where('status', 'Sent')
                ->groupBy('klien_id')
                ->select('klien_id', DB::raw('SUM(total_tagihan - uang_muka) as total_outstanding'))
                ->get()
                ->pluck('total_outstanding', 'klien_id');

            foreach ($clientList as $client) {
                $client->outstanding_piutang = $outstandings->get($client->id, 0);
            }

            if ($request->export === 'excel') {
                $data = compact('tab', 'clientList', 'totalPaidClients', 'totalPaidInvoices', 'totalCollected', 'totalOutstanding');
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\LaporanExcelExport('admin.invoice.exports.swasta-lunas-export', $data),
                    'Klien_Swasta_Lunas_Klien_' . date('Ymd_His') . '.xlsx'
                );
            }
        }

        return view('admin.invoice.swasta-lunas', compact(
            'tab', 'clients', 'invoices', 
            'totalPaidClients', 'totalPaidInvoices', 'totalCollected', 'totalOutstanding'
        ));
    }

    /**
     * Purge (permanently delete) a single invoice with complete cascade cleanup.
     */
    public function purge(Invoice $invoice)
    {
        Gate::authorize('delete_invoice');

        $nomorInvoice = $invoice->nomor_invoice;

        DB::transaction(function () use ($invoice) {
            // 1. Detach Ritase — reset to Draft so they can be re-invoiced
            \App\Models\Ritase::where('invoice_id', $invoice->id)->update([
                'invoice_id' => null,
                'status_invoice' => 'Draft',
            ]);

            // 2. Detach Penjualan — reset to Draft
            \App\Models\Penjualan::where('invoice_id', $invoice->id)->update([
                'invoice_id' => null,
                'status_invoice' => 'Draft',
            ]);

            // 3. Delete related JurnalHeaders (the model's deleting hook
            //    will cascade-delete JurnalDetails, BukuPembantu, and bukti_transaksi)
            JurnalHeader::where('referensi_type', Invoice::class)
                ->where('referensi_id', $invoice->id)
                ->get()
                ->each->delete();

            // 4. Clean up any remaining BukuPembantu referencing this invoice's journals
            //    (safety net — should already be handled by JurnalHeader::deleting)

            // 5. Delete the invoice itself
            $invoice->delete();
        });

        return redirect()->route('admin.invoice.index')
            ->with('success', "Invoice {$nomorInvoice} berhasil di-purge. Ritase & Penjualan dikembalikan ke status Draft.");
    }

    /**
     * Purge multiple selected invoices.
     */
    public function purgeSelected(Request $request)
    {
        Gate::authorize('delete_invoice');

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:invoices,id',
        ]);

        $count = 0;
        DB::transaction(function () use ($request, &$count) {
            $invoices = Invoice::whereIn('id', $request->ids)->get();
            foreach ($invoices as $invoice) {
                // Detach Ritase
                \App\Models\Ritase::where('invoice_id', $invoice->id)->update([
                    'invoice_id' => null,
                    'status_invoice' => 'Draft',
                ]);

                // Detach Penjualan
                \App\Models\Penjualan::where('invoice_id', $invoice->id)->update([
                    'invoice_id' => null,
                    'status_invoice' => 'Draft',
                ]);

                // Delete related JurnalHeaders (cascade via model hook)
                JurnalHeader::where('referensi_type', Invoice::class)
                    ->where('referensi_id', $invoice->id)
                    ->get()
                    ->each->delete();

                $invoice->delete();
                $count++;
            }
        });

        return redirect()->route('admin.invoice.index')
            ->with('success', "{$count} invoice berhasil di-purge.");
    }

    /**
     * Rebuild journal(s) for a single invoice.
     */
    public function rebuildJournal(Invoice $invoice)
    {
        Gate::authorize('update_invoice');

        DB::transaction(function () use ($invoice) {
            $invoice->loadMissing(['klien', 'ritase', 'penjualan']);
            $observer = new \App\Observers\InvoiceObserver();
            $observer->saved($invoice);
        });

        return back()->with('success', "Jurnal untuk Invoice {$invoice->nomor_invoice} berhasil dibangun ulang sesuai aturan COA terbaru.");
    }

    /**
     * Rebuild all invoice journals and sync Buku Pembantu.
     */
    public function rebuildAllJournals()
    {
        Gate::authorize('update_invoice');

        \Illuminate\Support\Facades\Artisan::call('app:rebuild-invoice-journals', ['--force' => true]);

        return back()->with('success', 'Seluruh jurnal invoice dan Buku Pembantu berhasil diperbarui dan disinkronkan sesuai aturan COA terbaru.');
    }

    /**
     * Preview DLH approved ritase data for the selected month/year.
     */
    public function previewMonthlyDlh(Request $request)
    {
        Gate::authorize('view_invoice');

        $month = (int)$request->input('periode_bulan', now()->month);
        $year = (int)$request->input('periode_tahun', now()->year);

        $masterDLH = $request->filled('klien_id') 
            ? Klien::find($request->klien_id) 
            : (Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first() ?? Klien::where('jenis', 'DLH')->first());

        $query = \App\Models\Ritase::whereHas('klien', fn($q) => $q->where('jenis', 'DLH'))
            ->whereYear('waktu_masuk', $year)
            ->whereMonth('waktu_masuk', $month)
            ->where('is_approved', true)
            ->where(function($q) {
                $q->whereNull('status_invoice')
                  ->orWhere('status_invoice', '!=', 'Paid');
            });

        $count = (clone $query)->count();
        $totalNetto = (clone $query)->sum('berat_netto');
        $totalTipping = (clone $query)->sum('biaya_tipping');

        // Check if an existing invoice already exists
        $existingInvoice = null;
        if ($masterDLH) {
            $existingInvoice = Invoice::where('klien_id', $masterDLH->id)
                ->where('periode_bulan', $month)
                ->where('periode_tahun', $year)
                ->first();
        }

        return response()->json([
            'success' => true,
            'count' => $count,
            'total_netto_ton' => number_format($totalNetto / 1000, 2, ',', '.'),
            'total_tipping_rp' => 'Rp ' . number_format($totalTipping, 0, ',', '.'),
            'total_tipping_raw' => $totalTipping,
            'klien_nama' => $masterDLH->nama_klien ?? 'Dinas Lingkungan Hidup',
            'has_existing' => $existingInvoice !== null,
            'existing_nomor' => $existingInvoice->nomor_invoice ?? null,
            'existing_status' => $existingInvoice->status ?? null,
        ]);
    }

    /**
     * Generate or consolidate the single monthly invoice for DLH client.
     */
    public function generateMonthlyDlh(Request $request)
    {
        Gate::authorize('create_invoice');

        $request->validate([
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2020|max:2099',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_invoice',
            'klien_id' => 'nullable|exists:klien,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Validate due date: maximum 30 days from invoice date
        $invDate = \Carbon\Carbon::parse($request->tanggal_invoice);
        $dueDate = \Carbon\Carbon::parse($request->tanggal_jatuh_tempo);
        $diffDays = $invDate->diffInDays($dueDate, false);

        if ($diffDays > 30) {
            return back()->withInput()->with('error', 'Jatuh tempo maksimal 30 hari dari tanggal invoice (selisih saat ini: ' . $diffDays . ' hari).');
        }

        $month = (int)$request->periode_bulan;
        $year = (int)$request->periode_tahun;

        $masterDLH = $request->filled('klien_id') 
            ? Klien::find($request->klien_id) 
            : (Klien::where('nama_klien', 'Dinas Lingkungan Hidup')->first() ?? Klien::where('jenis', 'DLH')->first());

        if (!$masterDLH) {
            return back()->withInput()->with('error', 'Master Klien DLH (Dinas Lingkungan Hidup) tidak ditemukan di database.');
        }

        // Query all approved DLH ritase for that month/year that are not on a Paid invoice
        $ritases = \App\Models\Ritase::whereHas('klien', fn($q) => $q->where('jenis', 'DLH'))
            ->whereYear('waktu_masuk', $year)
            ->whereMonth('waktu_masuk', $month)
            ->where('is_approved', true)
            ->where(function($q) {
                $q->whereNull('status_invoice')
                  ->orWhere('status_invoice', '!=', 'Paid');
            })
            ->get();

        if ($ritases->isEmpty()) {
            return back()->withInput()->with('error', "Tidak ditemukan ritase DLH yang sudah di-approve pada periode " . \App\Helpers\DateHelper::indonesianMonthName($month) . " {$year}.");
        }

        $invoice = null;
        $isNew = false;

        try {
            DB::transaction(function () use ($request, $masterDLH, $month, $year, $ritases, &$invoice, &$isNew) {
                $existing = Invoice::where('klien_id', $masterDLH->id)
                    ->where('periode_bulan', $month)
                    ->where('periode_tahun', $year)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'Paid') {
                        throw new \Exception("Invoice DLH untuk periode " . \App\Helpers\DateHelper::indonesianMonthName($month) . " {$year} sudah berstatus Lunas (Paid) dan tidak dapat dimodifikasi.");
                    }

                    $existing->update([
                        'tanggal_invoice' => $request->tanggal_invoice,
                        'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                        'keterangan' => $request->keterangan ?? $existing->keterangan,
                    ]);
                    $invoice = $existing;
                } else {
                    $tenantId = $masterDLH->tenant_id 
                        ?? (auth()->check() ? auth()->user()->getEffectiveTenantId() : null)
                        ?? \App\Models\Tenant::first()?->id;

                    $invoice = Invoice::create([
                        'tenant_id' => $tenantId,
                        'klien_id' => $masterDLH->id,
                        'periode_bulan' => $month,
                        'periode_tahun' => $year,
                        'tanggal_invoice' => $request->tanggal_invoice,
                        'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                        'total_tagihan' => 0,
                        'status' => 'Draft',
                        'keterangan' => $request->keterangan ?? ('Tagihan Rekapitulasi Jasa Pengelolaan Sampah (Tipping Fee) Periode ' . \App\Helpers\DateHelper::indonesianMonthName($month) . ' ' . $year),
                    ]);
                    $isNew = true;
                }

                // Link all ritase
                foreach ($ritases as $r) {
                    $r->update([
                        'invoice_id' => $invoice->id,
                        'status_invoice' => $invoice->status,
                        'status' => 'selesai',
                    ]);
                }

                $invoice->recalculateTotals();
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error generating monthly DLH invoice: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return back()->withInput()->with('error', 'Gagal memproses Invoice DLH: ' . $e->getMessage());
        }

        $actionText = $isNew ? 'dibuat' : 'diperbarui';
        $monthName = \App\Helpers\DateHelper::indonesianMonthName($month);
        $totalFormatted = 'Rp ' . number_format($invoice->total_tagihan, 0, ',', '.');

        return redirect()->route('admin.invoice.show', $invoice->id)
            ->with('success', "Invoice Rekap Bulanan DLH Periode {$monthName} {$year} berhasil {$actionText}. Sebanyak {$ritases->count()} tiket ritase telah direkap dengan total tagihan {$totalFormatted}.");
    }
}
