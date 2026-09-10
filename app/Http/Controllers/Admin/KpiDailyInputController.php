<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiDailyChecklist;
use App\Models\KpiMachineActivityLog;
use App\Models\KpiStakeholderComplaint;
use App\Models\Machine;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KpiDailyInputController extends Controller
{
    /**
     * Tampilan formulir & riwayat input harian
     */
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        $checklists = KpiDailyChecklist::where('tanggal', $tanggal)->latest()->get();
        $machineLogs = KpiMachineActivityLog::where('tanggal', $tanggal)->latest()->get();
        $complaints = KpiStakeholderComplaint::where('tanggal', $tanggal)->latest()->get();

        $machines = Machine::all();
        $users = User::where('is_active', true)->get();

        return view('admin.kpi.daily-input.index', compact(
            'tanggal', 'checklists', 'machineLogs', 'complaints', 'machines', 'users'
        ));
    }

    /**
     * Simpan Checklist Kebersihan & Bau Area (PIC: Ana Maria / Agung)
     */
    public function storeChecklist(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'area' => 'required|string',
            'status_kebersihan' => 'required|in:Bersih,Kurang Bersih,Kotor',
            'status_bau' => 'required|in:Tidak Bau,Bau Ringan,Bau Berat',
            'ada_tumpukan_sampah' => 'nullable|boolean',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $skorKebersihan = match($request->status_kebersihan) {
            'Bersih' => 100,
            'Kurang Bersih' => 70,
            'Kotor' => 0,
        };

        $skorBau = match($request->status_bau) {
            'Tidak Bau' => 100,
            'Bau Ringan' => 70,
            'Bau Berat' => 0,
        };

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoPath = \App\Helpers\ImageHelper::compressAndStore($request->file('foto_bukti'), 'kpi_checklists');
        }

        KpiDailyChecklist::create([
            'tanggal' => $request->tanggal,
            'area' => $request->area,
            'status_kebersihan' => $request->status_kebersihan,
            'ada_tumpukan_sampah' => $request->boolean('ada_tumpukan_sampah'),
            'status_bau' => $request->status_bau,
            'skor_kebersihan' => $skorKebersihan,
            'skor_bau' => $skorBau,
            'user_id' => auth()->id(),
            'foto_bukti' => $fotoPath,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('admin.kpi.daily-input.index', ['tanggal' => $request->tanggal])
            ->with('success', 'Checklist kebersihan & bau beserta foto bukti berhasil disimpan.');
    }

    /**
     * Simpan Log Jam Mesin & Wheel Loader (PIC: Agung)
     */
    public function storeMachineLog(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_alat' => 'required|string',
            'jam_operasi' => 'required|numeric|min:0|max:24',
            'jam_downtime' => 'required|numeric|min:0|max:24',
            'bbm_liter' => 'nullable|numeric|min:0',
            'status_alat' => 'required|in:Siap Operasi,Dalam Perbaikan,Rusak Total',
            'catatan_kendala' => 'nullable|string',
        ]);

        KpiMachineActivityLog::create([
            'tanggal' => $request->tanggal,
            'machine_id' => $request->machine_id,
            'nama_alat' => $request->nama_alat,
            'jam_start' => $request->jam_start,
            'jam_stop' => $request->jam_stop,
            'jam_operasi' => $request->jam_operasi,
            'jam_downtime' => $request->jam_downtime,
            'bbm_liter' => $request->bbm_liter ?? 0,
            'status_alat' => $request->status_alat,
            'catatan_kendala' => $request->catatan_kendala,
            'operator_id' => $request->operator_id ?? auth()->id(),
        ]);

        return redirect()->route('admin.kpi.daily-input.index', ['tanggal' => $request->tanggal])
            ->with('success', 'Log aktivitas mesin & alat berat berhasil disimpan.');
    }

    /**
     * Simpan Keluhan Stakeholder (PIC: Nita / Budi)
     */
    public function storeComplaint(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'stakeholder_type' => 'required|in:DLH,Penggerobak/Desa,Klien Swasta,Warga/Masyarakat,Lainnya',
            'isi_keluhan' => 'required|string',
            'tingkat_urgensi' => 'required|in:Rendah,Sedang,Tinggi',
        ]);

        KpiStakeholderComplaint::create([
            'tanggal' => $request->tanggal,
            'stakeholder_type' => $request->stakeholder_type,
            'nama_pelapor' => $request->nama_pelapor,
            'kontak_pelapor' => $request->kontak_pelapor,
            'isi_keluhan' => $request->isi_keluhan,
            'tingkat_urgensi' => $request->tingkat_urgensi,
            'status_penanganan' => 'Open',
            'handled_by_id' => auth()->id(),
        ]);

        return redirect()->route('admin.kpi.daily-input.index', ['tanggal' => $request->tanggal])
            ->with('success', 'Catatan keluhan stakeholder berhasil disimpan.');
    }

    /**
     * Update penyelesaian keluhan
     */
    public function resolveComplaint(Request $request, KpiStakeholderComplaint $complaint)
    {
        $request->validate([
            'tindakan_perbaikan' => 'required|string',
            'status_penanganan' => 'required|in:Proses,Resolved',
        ]);

        $complaint->update([
            'tindakan_perbaikan' => $request->tindakan_perbaikan,
            'status_penanganan' => $request->status_penanganan,
            'tanggal_selesai' => $request->status_penanganan === 'Resolved' ? Carbon::today() : null,
            'handled_by_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Status tindak lanjut keluhan berhasil diperbarui.');
    }
}
