<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JurnalHeader;
use App\Models\JurnalKas;
use App\Models\Coa;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
            $query->where('status', $request->status);
        }

        $jurnals = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('admin.jurnal.index', compact('jurnals'));
    }

    public function create()
    {
        Gate::authorize('create_jurnal');
        $coas = Coa::orderBy('kode_akun')->get();
        return view('admin.jurnal.form', compact('coas'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_jurnal');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:2048',
            'details' => 'required|array|min:2',
            'details.*.coa_id' => 'required|exists:coa,id',
            'details.*.debit' => 'nullable|numeric|min:0',
            'details.*.kredit' => 'nullable|numeric|min:0',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = $request->file('bukti_transaksi')->store('jurnal-bukti', 'public');
        }

        // Validasi ketersediaan saldo untuk akun Kas & Bank (awalan '11') di sisi Kredit
        $kasCredits = collect($validated['details'])
            ->filter(fn($d) => isset($d['kredit']) && $d['kredit'] > 0)
            ->groupBy('coa_id')
            ->map(fn($group) => $group->sum('kredit'));

        foreach ($kasCredits as $coaId => $kreditInput) {
            $coa = Coa::find($coaId);
            if ($coa && str_starts_with($coa->kode_akun, '11')) { // 11 adalah Kas & Bank
                $saldoDebit = \App\Models\JurnalDetail::where('coa_id', $coaId)->sum('debit');
                $saldoKredit = \App\Models\JurnalDetail::where('coa_id', $coaId)->sum('kredit');
                $saldoAwal = $saldoDebit - $saldoKredit;

                if ($kreditInput > $saldoAwal) {
                    return back()->withInput()->withErrors([
                        'details' => 'Saldo akun ' . $coa->nama_akun . ' (' . $coa->kode_akun . ') tidak mencukupi. Sisa saldo saat ini: Rp ' . number_format($saldoAwal, 0, ',', '.')
                    ]);
                }
            }
        }

        $jurnal = JurnalHeader::create([
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'bukti_transaksi' => $buktiPath,
            'status' => 'unposted',
        ]);

        foreach ($validated['details'] as $detail) {
            $jurnal->jurnalDetails()->create([
                'coa_id' => $detail['coa_id'],
                'debit' => $detail['debit'] ?? 0,
                'kredit' => $detail['kredit'] ?? 0,
            ]);
        }

        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal berhasil dibuat.');
    }

    public function edit(JurnalHeader $jurnal)
    {
        Gate::authorize('update_jurnal');
        $jurnal->load('jurnalDetails.coa');
        $coas = Coa::orderBy('kode_akun')->get();
        return view('admin.jurnal.form', compact('jurnal', 'coas'));
    }

    public function update(Request $request, JurnalHeader $jurnal)
    {
        Gate::authorize('update_jurnal');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => 'nullable|image|max:2048',
            'details' => 'required|array|min:2',
            'details.*.coa_id' => 'required|exists:coa,id',
            'details.*.debit' => 'nullable|numeric|min:0',
            'details.*.kredit' => 'nullable|numeric|min:0',
        ]);

        $data = [
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ];

        if ($request->hasFile('bukti_transaksi')) {
            $data['bukti_transaksi'] = $request->file('bukti_transaksi')->store('jurnal-bukti', 'public');
        }

        // Validasi ketersediaan saldo untuk akun Kas & Bank (awalan '11') di sisi Kredit
        $kasCredits = collect($validated['details'])
            ->filter(fn($d) => isset($d['kredit']) && $d['kredit'] > 0)
            ->groupBy('coa_id')
            ->map(fn($group) => $group->sum('kredit'));

        foreach ($kasCredits as $coaId => $kreditInput) {
            $coa = Coa::find($coaId);
            if ($coa && str_starts_with($coa->kode_akun, '11')) { // 11 adalah Kas & Bank
                $saldoDebit = \App\Models\JurnalDetail::where('coa_id', $coaId)->sum('debit');
                $saldoKredit = \App\Models\JurnalDetail::where('coa_id', $coaId)->sum('kredit');
                $saldoAwal = $saldoDebit - $saldoKredit;

                // Kembalikan saldo sebelum direvisi eksisting
                $jurnalExistingCredit = \App\Models\JurnalDetail::where('jurnal_header_id', $jurnal->id)
                    ->where('coa_id', $coaId)
                    ->sum('kredit');
                $jurnalExistingDebit = \App\Models\JurnalDetail::where('jurnal_header_id', $jurnal->id)
                    ->where('coa_id', $coaId)
                    ->sum('debit');
                
                $saldoRestore = $saldoAwal + $jurnalExistingCredit - $jurnalExistingDebit;

                if ($kreditInput > $saldoRestore) {
                    return back()->withInput()->withErrors([
                        'details' => 'Saldo akun ' . $coa->nama_akun . ' (' . $coa->kode_akun . ') tidak mencukupi. Sisa saldo saat ini: Rp ' . number_format($saldoRestore, 0, ',', '.')
                    ]);
                }
            }
        }

        $jurnal->update($data);

        // Sync details
        $jurnal->jurnalDetails()->delete();
        foreach ($validated['details'] as $detail) {
            $jurnal->jurnalDetails()->create([
                'coa_id' => $detail['coa_id'],
                'debit' => $detail['debit'] ?? 0,
                'kredit' => $detail['kredit'] ?? 0,
            ]);
        }

        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function destroy(JurnalHeader $jurnal)
    {
        Gate::authorize('delete_jurnal');
        $jurnal->jurnalDetails()->delete();
        $jurnal->delete();
        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal berhasil dihapus.');
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
}
