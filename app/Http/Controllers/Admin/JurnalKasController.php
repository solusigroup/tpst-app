<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JurnalKas;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class JurnalKasController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_jurnal_kas');
        
        $kasCoas = Coa::where('kode_akun', 'like', '11%')->where('nama_akun', 'like', '%Kas%')->pluck('id')->toArray();

        $query = \App\Models\JurnalHeader::with(['jurnalDetails.coa', 'referensi'])
            ->where(function($q) use ($kasCoas, $request) {
                // 1. From Jurnal Kas
                $q->where('referensi_type', \App\Models\JurnalKas::class);
                
                if ($request->filled('jenis')) {
                    $q->whereHasMorph('referensi', [\App\Models\JurnalKas::class], function($jurnalKasQuery) use ($request) {
                        $jurnalKasQuery->where('tipe', $request->jenis == 'masuk' ? 'Penerimaan' : 'Pengeluaran');
                    });
                }

                // 2. From General Journal (Only Kas Masuk)
                // General journals are included ONLY if we are not filtering exclusively for 'keluar'
                if ($request->jenis != 'keluar') {
                    $q->orWhere(function($subQ) use ($kasCoas) {
                        $subQ->where(function($q3) {
                            $q3->where('referensi_type', '!=', \App\Models\JurnalKas::class)
                               ->orWhereNull('referensi_type');
                        })
                        ->whereHas('jurnalDetails', function($detailQ) use ($kasCoas) {
                            $detailQ->whereIn('coa_id', $kasCoas)->where('debit', '>', 0);
                        });
                    });
                }
            });

        if ($request->filled('search')) {
            $query->where('deskripsi', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('jumlah')) {
            $jumlah = $request->jumlah;
            $query->where(function($q) use ($jumlah) {
                // Search in JurnalKas nominal
                $q->whereHasMorph('referensi', [\App\Models\JurnalKas::class], function($jkQuery) use ($jumlah) {
                    $jkQuery->where('nominal', $jumlah);
                })
                // Also search in JurnalDetail debit/kredit for general journal entries
                ->orWhereHas('jurnalDetails', function($detailQ) use ($jumlah) {
                    $detailQ->where('debit', $jumlah)->orWhere('kredit', $jumlah);
                });
            });
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $sortDirection = $request->input('sort') === 'asc' ? 'asc' : 'desc';
        $paginator = $query->orderBy('tanggal', $sortDirection)->paginate(15)->withQueryString();

        $paginator->getCollection()->transform(function ($header) use ($kasCoas) {
            if ($header->referensi_type === \App\Models\JurnalKas::class && $header->referensi) {
                $kas = $header->referensi;
                $kas->is_jurnal_umum = false;
                $kas->loadMissing('coaLawan');
                return $kas;
            } else {
                $kasDetail = $header->jurnalDetails->first(fn($d) => in_array($d->coa_id, $kasCoas) && $d->debit > 0);
                $lawanDetails = $header->jurnalDetails->filter(fn($d) => !in_array($d->coa_id, $kasCoas) || $d->kredit > 0);
                $lawanDetail = $lawanDetails->first();
                
                $virtualKas = new \App\Models\JurnalKas();
                $virtualKas->id = $header->id; // ID JurnalHeader!
                $virtualKas->tanggal = $header->tanggal;
                $virtualKas->tipe = 'Penerimaan';
                $virtualKas->nominal = $kasDetail ? $kasDetail->debit : 0;
                $virtualKas->deskripsi = $header->deskripsi;
                $virtualKas->status = $header->status;
                $virtualKas->bukti_transaksi = $header->bukti_transaksi;
                $virtualKas->is_jurnal_umum = true;

                $virtualKas->setRelation('coaLawan', $lawanDetails->count() > 1 ? (object)['nama_akun' => 'Multiple Accounts'] : ($lawanDetail ? $lawanDetail->coa : null));

                return $virtualKas;
            }
        });

        $jurnalKas = $paginator;

        // Menghitung Saldo Kas saat ini (hanya posted)
        $kas = Coa::where('kode_akun', 'like', '11%')->where('nama_akun', 'like', '%Kas%')->first();
        $saldoKas = 0;
        if ($kas) {
            $saldoKas = \App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('jurnal_detail.coa_id', $kas->id)
                ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
                ->value('saldo') ?? 0;
        }

        return view('admin.jurnal-kas.index', compact('jurnalKas', 'saldoKas'));
    }

    public function create()
    {
        Gate::authorize('create_jurnal_kas');

        if ($this->getSaldoKas() < 0) {
            return redirect()->route('admin.jurnal-kas.index')
                ->with('error_saldo_negatif', true);
        }

        $targetCoaId = request('rekonsiliasi_target_coa');
        if (!$targetCoaId) {
            $kas = Coa::where('kode_akun', 'like', '11%')->where('nama_akun', 'like', '%Kas%')->first();
            $targetCoaId = $kas ? $kas->id : null;
        }

        $coas = Coa::where('id', '!=', $targetCoaId)->orderBy('kode_akun')->get();
        $kliens = \App\Models\Klien::orderBy('nama_klien')->get();
        $vendors = \App\Models\Vendor::orderBy('nama_vendor')->get();
        return view('admin.jurnal-kas.form', compact('coas', 'kliens', 'vendors'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_jurnal_kas');

        if ($this->getSaldoKas() < 0) {
            return redirect()->route('admin.jurnal-kas.index')
                ->with('error_saldo_negatif', true);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'coa_id' => 'required|exists:coa,id',
            'jumlah' => 'required|numeric|gt:0',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'contactable_type_id' => 'nullable|string',
            'rekonsiliasi_target_coa' => 'nullable|exists:coa,id',
        ]);

        $data = $validated;
        unset($data['coa_id']);
        unset($data['contactable_type_id']);
        unset($data['rekonsiliasi_target_coa']);

        if (!empty($validated['contactable_type_id']) && str_contains($validated['contactable_type_id'], ':')) {
            [$data['contactable_type'], $data['contactable_id']] = explode(':', $validated['contactable_type_id']);
        }
        $data['coa_lawan_id'] = $validated['coa_id'];
        
        $kas = null;
        if ($request->filled('rekonsiliasi_target_coa')) {
            $kas = Coa::find($request->rekonsiliasi_target_coa);
        }
        if (!$kas) {
            $kas = Coa::where('kode_akun', 'like', '11%')->where('nama_akun', 'like', '%Kas%')->first();
        }
        if (!$kas) {
            return back()->withInput()->withErrors(['coa_id' => 'Akun Kas tidak ditemukan. Pastikan COA dengan kode awalan 11 dan nama mengandung "Kas" sudah dibuat.']);
        }
        $data['coa_kas_id'] = $kas->id;
        $data['nominal'] = $validated['jumlah'];
        $data['tipe'] = $validated['jenis'] == 'masuk' ? 'Penerimaan' : 'Pengeluaran';

        if ($data['tipe'] === 'Pengeluaran') {
            $saldoKas = \App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('jurnal_detail.coa_id', $kas->id)
                ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
                ->value('saldo') ?? 0;

            if ($data['nominal'] > $saldoKas) {
                return back()->withInput()->withErrors(['jumlah' => 'Saldo Kas tidak mencukupi untuk pengeluaran ini. Sisa saldo saat ini: Rp ' . number_format($saldoKas, 0, ',', '.')]);
            }
        }

        if ($request->hasFile('bukti_transaksi')) {
            $path = \App\Helpers\ImageHelper::compressAndStore($request->file('bukti_transaksi'), 'uploads/jurnal_kas');
            $data['bukti_transaksi'] = $path;
        }

        JurnalKas::create($data);

        return redirect()->route('admin.jurnal-kas.index')->with('success', 'Jurnal Kas berhasil ditambahkan.');
    }

    public function edit(JurnalKas $jurnalKas)
    {
        Gate::authorize('update_jurnal_kas');

        if ($this->getSaldoKas() < 0) {
            return redirect()->route('admin.jurnal-kas.index')
                ->with('error_saldo_negatif', true);
        }

        $coas = Coa::where('id', '!=', $jurnalKas->coa_kas_id)->orderBy('kode_akun')->get();
        $kliens = \App\Models\Klien::orderBy('nama_klien')->get();
        $vendors = \App\Models\Vendor::orderBy('nama_vendor')->get();

        // Soft warning jika jurnal kas sudah posted
        $warning = null;
        if ($jurnalKas->status === 'posted') {
            $warning = 'Perhatian: Jurnal Kas ini sudah di-post. Perubahan akan mempengaruhi laporan keuangan yang sudah final.';
        }

        return view('admin.jurnal-kas.form', compact('jurnalKas', 'coas', 'kliens', 'vendors', 'warning'));
    }

    public function update(Request $request, JurnalKas $jurnalKas)
    {
        Gate::authorize('update_jurnal_kas');

        if ($this->getSaldoKas() < 0) {
            return redirect()->route('admin.jurnal-kas.index')
                ->with('error_saldo_negatif', true);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'coa_id' => 'required|exists:coa,id',
            'jumlah' => 'required|numeric|gt:0',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => ($jurnalKas->bukti_transaksi ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'contactable_type_id' => 'nullable|string',
            'rekonsiliasi_target_coa' => 'nullable|exists:coa,id',
        ]);

        $data = $validated;
        unset($data['coa_id']);
        unset($data['contactable_type_id']);
        unset($data['rekonsiliasi_target_coa']);

        if (!empty($validated['contactable_type_id']) && str_contains($validated['contactable_type_id'], ':')) {
            [$data['contactable_type'], $data['contactable_id']] = explode(':', $validated['contactable_type_id']);
        } else {
            $data['contactable_type'] = null;
            $data['contactable_id'] = null;
        }
        $data['coa_lawan_id'] = $validated['coa_id'];
        
        $kas = null;
        if ($request->filled('rekonsiliasi_target_coa')) {
            $kas = Coa::find($request->rekonsiliasi_target_coa);
        }
        if (!$kas) {
            $kas = Coa::find($jurnalKas->coa_kas_id);
        }
        if (!$kas) {
            $kas = Coa::where('kode_akun', 'like', '11%')->where('nama_akun', 'like', '%Kas%')->first();
        }
        if (!$kas) {
            return back()->withInput()->withErrors(['coa_id' => 'Akun Kas tidak ditemukan. Pastikan COA dengan kode awalan 11 dan nama mengandung "Kas" sudah dibuat.']);
        }
        $data['coa_kas_id'] = $kas->id;
        $data['nominal'] = $validated['jumlah'];
        $data['tipe'] = $validated['jenis'] == 'masuk' ? 'Penerimaan' : 'Pengeluaran';

        if ($data['tipe'] === 'Pengeluaran') {
            $saldoKas = \App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('jurnal_detail.coa_id', $kas->id)
                ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
                ->value('saldo') ?? 0;
            
            // Mengembalikan efek dari mutasi sebelumnya ke saldo — HANYA jika jurnal lama posted
            if ($jurnalKas->status === 'posted') {
                if ($jurnalKas->tipe === 'Pengeluaran') {
                    $saldoKas += $jurnalKas->nominal;
                } else {
                    $saldoKas -= $jurnalKas->nominal;
                }
            }

            if ($data['nominal'] > $saldoKas) {
                return back()->withInput()->withErrors(['jumlah' => 'Saldo Kas tidak mencukupi untuk pengeluaran ini. Sisa saldo saat ini: Rp ' . number_format($saldoKas, 0, ',', '.')]);
            }
        }

        if ($request->hasFile('bukti_transaksi')) {
            if ($jurnalKas->bukti_transaksi) {
                Storage::disk('public')->delete($jurnalKas->bukti_transaksi);
            }
            $path = \App\Helpers\ImageHelper::compressAndStore($request->file('bukti_transaksi'), 'uploads/jurnal_kas');
            $data['bukti_transaksi'] = $path;
        }

        $jurnalKas->update($data);

        return redirect()->route('admin.jurnal-kas.index')->with('success', 'Jurnal Kas berhasil diperbarui.');
    }

    public function destroy(JurnalKas $jurnalKas)
    {
        Gate::authorize('delete_jurnal_kas');

        if ($this->getSaldoKas() < 0) {
            return redirect()->route('admin.jurnal-kas.index')
                ->with('error_saldo_negatif', true);
        }

        // Soft warning jika posted
        $warningMsg = '';
        if ($jurnalKas->status === 'posted') {
            $warningMsg = ' (Perhatian: Jurnal Kas yang sudah di-post telah dihapus. Laporan keuangan mungkin terpengaruh.)';
        }

        if ($jurnalKas->bukti_transaksi) {
            Storage::disk('public')->delete($jurnalKas->bukti_transaksi);
        }
        $jurnalKas->delete();
        return redirect()->route('admin.jurnal-kas.index')->with('success', 'Jurnal Kas berhasil dihapus.' . $warningMsg);
    }

    public function transfer()
    {
        Gate::authorize('create_jurnal_kas');

        // Get all Kas & Bank accounts (COA starting with 11)
        $kasBankCoas = Coa::where('kode_akun', 'like', '11%')->orderBy('kode_akun')->get();

        // Calculate saldo for each account (hanya posted)
        $kasBank = $kasBankCoas->map(function ($coa) {
            $saldo = \App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('jurnal_detail.coa_id', $coa->id)
                ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
                ->value('saldo') ?? 0;
            $coa->saldo = $saldo;
            return $coa;
        });

        // COA Biaya Admin Bank (8102)
        $coaBiayaAdmin = Coa::where('kode_akun', '8102')->first();

        return view('admin.jurnal-kas.transfer', compact('kasBank', 'coaBiayaAdmin'));
    }

    public function storeTransfer(Request $request)
    {
        Gate::authorize('create_jurnal_kas');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'dari_coa_id' => 'required|exists:coa,id',
            'ke_coa_id' => 'required|exists:coa,id|different:dari_coa_id',
            'jumlah' => 'required|numeric|min:1',
            'biaya_admin' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $jumlah = $validated['jumlah'];
        $biayaAdmin = $validated['biaya_admin'] ?? 0;
        $totalKredit = $jumlah + $biayaAdmin;

        if ($biayaAdmin > 0) {
            $coaBiayaAdmin = Coa::where('kode_akun', '8102')->first();
            if (!$coaBiayaAdmin) {
                return back()->withInput()->withErrors([
                    'biaya_admin' => 'Akun Biaya Admin Bank (8102) belum terdaftar di COA. Harap daftarkan terlebih dahulu.'
                ]);
            }
        }

        // Check balance of source account (hanya posted)
        $dariCoa = Coa::findOrFail($validated['dari_coa_id']);
        $keCoa = Coa::findOrFail($validated['ke_coa_id']);

        $saldoSumber = \App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->where('jurnal_header.status', 'posted')
            ->where('jurnal_detail.coa_id', $dariCoa->id)
            ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
            ->value('saldo') ?? 0;

        if ($totalKredit > $saldoSumber) {
            return back()->withInput()->withErrors([
                'jumlah' => 'Saldo ' . $dariCoa->nama_akun . ' tidak mencukupi. Saldo saat ini: Rp ' . number_format($saldoSumber, 0, ',', '.') . '. Total yang dibutuhkan (termasuk biaya admin): Rp ' . number_format($totalKredit, 0, ',', '.')
            ]);
        }

        // Upload bukti
        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = \App\Helpers\ImageHelper::compressAndStore($request->file('bukti_transaksi'), 'uploads/transfer');
        }

        // Build deskripsi
        $deskripsi = $validated['deskripsi'] ?: "Transfer dari {$dariCoa->kode_akun} {$dariCoa->nama_akun} ke {$keCoa->kode_akun} {$keCoa->nama_akun}";

        $jurnalHeader = DB::transaction(function () use ($validated, $deskripsi, $buktiPath, $jumlah, $biayaAdmin, $totalKredit, $keCoa, $dariCoa) {
            // Create JurnalHeader
            $jurnalHeader = \App\Models\JurnalHeader::create([
                'tanggal' => $validated['tanggal'],
                'deskripsi' => $deskripsi,
                'bukti_transaksi' => $buktiPath,
                'status' => 'unposted',
            ]);

            // Detail 1: Debit target account
            $jurnalHeader->jurnalDetails()->create([
                'coa_id' => $keCoa->id,
                'debit' => $jumlah,
                'kredit' => 0,
            ]);

            // Detail 2: Debit biaya admin (if any)
            if ($biayaAdmin > 0) {
                $coaBiayaAdmin = Coa::where('kode_akun', '8102')->first();
                if ($coaBiayaAdmin) {
                    $jurnalHeader->jurnalDetails()->create([
                        'coa_id' => $coaBiayaAdmin->id,
                        'debit' => $biayaAdmin,
                        'kredit' => 0,
                    ]);
                }
            }

            // Detail 3: Kredit source account
            $jurnalHeader->jurnalDetails()->create([
                'coa_id' => $dariCoa->id,
                'debit' => 0,
                'kredit' => $totalKredit,
            ]);

            return $jurnalHeader;
        });

        return redirect()->route('admin.jurnal-kas.index')->with('success', 'Transfer berhasil! Jurnal ' . $jurnalHeader->nomor_referensi . ' telah dibuat.');
    }

    /**
     * Get the current posted petty cash (Kas Kecil) balance.
     */
    private function getSaldoKas(): float
    {
        $kas = Coa::where('kode_akun', 'like', '11%')
            ->where('nama_akun', 'like', '%Kas%')
            ->first();

        if (!$kas) {
            return 0;
        }

        return (float) (\App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->where('jurnal_header.status', 'posted')
            ->where('jurnal_detail.coa_id', $kas->id)
            ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
            ->value('saldo') ?? 0);
    }
}
