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

        // Menghitung Saldo Kas saat ini
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
            'bukti_transaksi' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
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
            'bukti_transaksi' => ($jurnalKas->bukti_transaksi ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:5120',
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
            $saldoKas = \App\Models\JurnalDetail::join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
                ->where('jurnal_header.status', 'posted')
                ->where('jurnal_detail.coa_id', $kas->id)
                ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) - COALESCE(SUM(jurnal_detail.kredit), 0) as saldo')
                ->value('saldo') ?? 0;
            
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
