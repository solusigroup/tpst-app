<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JurnalKas;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class JurnalKasController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_jurnal_kas');
        $query = JurnalKas::with('coaLawan');

        if ($request->filled('search')) {
            $query->where('deskripsi', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('jenis')) {
            $query->where('tipe', $request->jenis == 'masuk' ? 'Penerimaan' : 'Pengeluaran');
        }

        $jurnalKas = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('admin.jurnal-kas.index', compact('jurnalKas'));
    }

    public function create()
    {
        Gate::authorize('create_jurnal_kas');
        $coas = Coa::where('kode_akun', '!=', '1101')->orderBy('kode_akun')->get();
        $kliens = \App\Models\Klien::orderBy('nama_klien')->get();
        $vendors = \App\Models\Vendor::orderBy('nama_vendor')->get();
        return view('admin.jurnal-kas.form', compact('coas', 'kliens', 'vendors'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_jurnal_kas');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'coa_id' => 'required|exists:coa,id',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'contactable_type_id' => 'nullable|string',
        ]);

        $data = $validated;
        unset($data['coa_id']);
        unset($data['contactable_type_id']);

        if (!empty($validated['contactable_type_id'])) {
            [$data['contactable_type'], $data['contactable_id']] = explode(':', $validated['contactable_type_id']);
        }
        $data['coa_lawan_id'] = $validated['coa_id'];
        
        $kas = Coa::where('kode_akun', 'like', '11%')->where('nama_akun', 'like', '%Kas%')->first();
        $data['coa_kas_id'] = $kas ? $kas->id : 1;
        $data['nominal'] = $validated['jumlah'];
        $data['tipe'] = $validated['jenis'] == 'masuk' ? 'Penerimaan' : 'Pengeluaran';

        if ($data['tipe'] === 'Pengeluaran' && $kas) {
            $saldoDebit = \App\Models\JurnalDetail::where('coa_id', $kas->id)->sum('debit');
            $saldoKredit = \App\Models\JurnalDetail::where('coa_id', $kas->id)->sum('kredit');
            $saldoKas = $saldoDebit - $saldoKredit;

            if ($data['nominal'] > $saldoKas) {
                return back()->withInput()->withErrors(['jumlah' => 'Saldo Kas tidak mencukupi untuk pengeluaran ini. Sisa saldo saat ini: Rp ' . number_format($saldoKas, 0, ',', '.')]);
            }
        }

        if ($request->hasFile('bukti_transaksi')) {
            $path = $request->file('bukti_transaksi')->store('uploads/jurnal_kas', 'public');
            $data['bukti_transaksi'] = $path;
        }

        JurnalKas::create($data);

        return redirect()->route('admin.jurnal-kas.index')->with('success', 'Jurnal Kas berhasil ditambahkan.');
    }

    public function edit(JurnalKas $jurnalKas)
    {
        Gate::authorize('update_jurnal_kas');
        $coas = Coa::where('kode_akun', '!=', '1101')->orderBy('kode_akun')->get();
        $kliens = \App\Models\Klien::orderBy('nama_klien')->get();
        $vendors = \App\Models\Vendor::orderBy('nama_vendor')->get();
        return view('admin.jurnal-kas.form', compact('jurnalKas', 'coas', 'kliens', 'vendors'));
    }

    public function update(Request $request, JurnalKas $jurnalKas)
    {
        Gate::authorize('update_jurnal_kas');

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'coa_id' => 'required|exists:coa,id',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => ($jurnalKas->bukti_transaksi ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'contactable_type_id' => 'nullable|string',
        ]);

        $data = $validated;
        unset($data['coa_id']);
        unset($data['contactable_type_id']);

        if (!empty($validated['contactable_type_id'])) {
            [$data['contactable_type'], $data['contactable_id']] = explode(':', $validated['contactable_type_id']);
        } else {
            $data['contactable_type'] = null;
            $data['contactable_id'] = null;
        }
        $data['coa_lawan_id'] = $validated['coa_id'];
        
        $kas = Coa::where('kode_akun', 'like', '11%')->where('nama_akun', 'like', '%Kas%')->first();
        $data['coa_kas_id'] = $kas ? $kas->id : 1;
        $data['nominal'] = $validated['jumlah'];
        $data['tipe'] = $validated['jenis'] == 'masuk' ? 'Penerimaan' : 'Pengeluaran';

        if ($data['tipe'] === 'Pengeluaran' && $kas) {
            $saldoDebit = \App\Models\JurnalDetail::where('coa_id', $kas->id)->sum('debit');
            $saldoKredit = \App\Models\JurnalDetail::where('coa_id', $kas->id)->sum('kredit');
            $saldoKas = $saldoDebit - $saldoKredit;
            
            // Mengembalikan efek dari mutasi sebelumnya ke saldo awal
            if ($jurnalKas->tipe === 'Pengeluaran') {
                $saldoKas += $jurnalKas->nominal;
            } else {
                $saldoKas -= $jurnalKas->nominal;
            }

            if ($data['nominal'] > $saldoKas) {
                return back()->withInput()->withErrors(['jumlah' => 'Saldo Kas tidak mencukupi untuk pengeluaran ini. Sisa saldo saat ini: Rp ' . number_format($saldoKas, 0, ',', '.')]);
            }
        }

        if ($request->hasFile('bukti_transaksi')) {
            if ($jurnalKas->bukti_transaksi) {
                Storage::disk('public')->delete($jurnalKas->bukti_transaksi);
            }
            $path = $request->file('bukti_transaksi')->store('uploads/jurnal_kas', 'public');
            $data['bukti_transaksi'] = $path;
        }

        $jurnalKas->update($data);

        return redirect()->route('admin.jurnal-kas.index')->with('success', 'Jurnal Kas berhasil diperbarui.');
    }

    public function destroy(JurnalKas $jurnalKas)
    {
        Gate::authorize('delete_jurnal_kas');
        if ($jurnalKas->bukti_transaksi) {
            Storage::disk('public')->delete($jurnalKas->bukti_transaksi);
        }
        $jurnalKas->delete();
        return redirect()->route('admin.jurnal-kas.index')->with('success', 'Jurnal Kas berhasil dihapus.');
    }
}
