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
use Illuminate\Support\Facades\Gate;

class KpiDailyInputController extends Controller
{
    /**
     * Tampilan formulir & riwayat input harian
     */
    public function index(Request $request)
    {
        Gate::authorize('view_kpi_daily_input');

        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $activeTab = $request->get('tab', session('active_tab', 'kebersihan'));

        $checklists = KpiDailyChecklist::with(['user', 'approvedBy'])->where('tanggal', $tanggal)->latest()->get();
        $machineLogs = KpiMachineActivityLog::with(['operator', 'machine', 'approvedBy'])->where('tanggal', $tanggal)->latest()->get();
        $complaints = KpiStakeholderComplaint::with(['handledBy', 'approvedBy'])->where('tanggal', $tanggal)->latest()->get();

        $machines = Machine::all();
        $users = User::where('is_active', true)->get();

        return view('admin.kpi.daily-input.index', compact(
            'tanggal', 'activeTab', 'checklists', 'machineLogs', 'complaints', 'machines', 'users'
        ));
    }

    /**
     * Otorisasi approval (Role: manajemen & super_admin)
     */
    protected function authorizeApprove(): void
    {
        $user = auth()->user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasRole(['manajemen', 'super_admin', 'superadmin']))) {
            abort(403, 'Akses ditolak. Hanya role Manajemen dan Superadmin yang diizinkan melakukan approval.');
        }
    }

    /**
     * Otorisasi hapus (Role: super_admin)
     */
    protected function authorizeDelete(): void
    {
        $user = auth()->user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasRole(['super_admin', 'superadmin']))) {
            abort(403, 'Akses ditolak. Hanya role Superadmin yang diizinkan menghapus data.');
        }
    }

    /**
     * Approve Checklist Kebersihan
     */
    public function approveChecklist(KpiDailyChecklist $checklist)
    {
        $this->authorizeApprove();

        $checklist->update([
            'is_approved' => true,
            'approved_by_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $checklist->tanggal->format('Y-m-d'),
            'tab' => 'kebersihan',
        ])->with('success', "Checklist area \"{$checklist->area}\" berhasil disetujui (Approved).");
    }

    /**
     * Batalkan Approval Checklist Kebersihan
     */
    public function unapproveChecklist(KpiDailyChecklist $checklist)
    {
        $this->authorizeApprove();

        $checklist->update([
            'is_approved' => false,
            'approved_by_id' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $checklist->tanggal->format('Y-m-d'),
            'tab' => 'kebersihan',
        ])->with('success', "Approval checklist area \"{$checklist->area}\" berhasil dibatalkan.");
    }

    /**
     * Hapus Checklist Kebersihan (Superadmin)
     */
    public function destroyChecklist(KpiDailyChecklist $checklist)
    {
        $this->authorizeDelete();

        $tanggal = $checklist->tanggal->format('Y-m-d');
        $area = $checklist->area;

        if ($checklist->foto_bukti && \Illuminate\Support\Facades\Storage::disk('public')->exists($checklist->foto_bukti)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($checklist->foto_bukti);
        }

        $checklist->delete();

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $tanggal,
            'tab' => 'kebersihan',
        ])->with('success', "Data checklist area \"{$area}\" berhasil dihapus.");
    }

    /**
     * Approve Log Mesin
     */
    public function approveMachineLog(KpiMachineActivityLog $machineLog)
    {
        $this->authorizeApprove();

        $machineLog->update([
            'is_approved' => true,
            'approved_by_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $machineLog->tanggal->format('Y-m-d'),
            'tab' => 'mesin',
        ])->with('success', "Log mesin \"{$machineLog->nama_alat}\" berhasil disetujui (Approved).");
    }

    /**
     * Batalkan Approval Log Mesin
     */
    public function unapproveMachineLog(KpiMachineActivityLog $machineLog)
    {
        $this->authorizeApprove();

        $machineLog->update([
            'is_approved' => false,
            'approved_by_id' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $machineLog->tanggal->format('Y-m-d'),
            'tab' => 'mesin',
        ])->with('success', "Approval log mesin \"{$machineLog->nama_alat}\" berhasil dibatalkan.");
    }

    /**
     * Hapus Log Mesin (Superadmin)
     */
    public function destroyMachineLog(KpiMachineActivityLog $machineLog)
    {
        $this->authorizeDelete();

        $tanggal = $machineLog->tanggal->format('Y-m-d');
        $namaAlat = $machineLog->nama_alat;

        $machineLog->delete();

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $tanggal,
            'tab' => 'mesin',
        ])->with('success', "Data log mesin \"{$namaAlat}\" berhasil dihapus.");
    }

    /**
     * Approve Keluhan Stakeholder
     */
    public function approveComplaint(KpiStakeholderComplaint $complaint)
    {
        $this->authorizeApprove();

        $complaint->update([
            'is_approved' => true,
            'approved_by_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $complaint->tanggal->format('Y-m-d'),
            'tab' => 'complaint',
        ])->with('success', "Keluhan stakeholder \"{$complaint->stakeholder_type}\" berhasil disetujui (Approved).");
    }

    /**
     * Batalkan Approval Keluhan Stakeholder
     */
    public function unapproveComplaint(KpiStakeholderComplaint $complaint)
    {
        $this->authorizeApprove();

        $complaint->update([
            'is_approved' => false,
            'approved_by_id' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $complaint->tanggal->format('Y-m-d'),
            'tab' => 'complaint',
        ])->with('success', "Approval keluhan stakeholder \"{$complaint->stakeholder_type}\" berhasil dibatalkan.");
    }

    /**
     * Hapus Keluhan Stakeholder (Superadmin)
     */
    public function destroyComplaint(KpiStakeholderComplaint $complaint)
    {
        $this->authorizeDelete();

        $tanggal = $complaint->tanggal->format('Y-m-d');
        $stakeholder = $complaint->stakeholder_type;

        $complaint->delete();

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $tanggal,
            'tab' => 'complaint',
        ])->with('success', "Catatan keluhan \"{$stakeholder}\" berhasil dihapus.");
    }

    /**
     * Simpan Checklist Kebersihan & Bau Area (PIC: Ana Maria / Agung)
     */
    public function storeChecklist(Request $request)
    {
        if (!Gate::allows('create_kpi_daily_input') && !Gate::allows('view_kpi_daily_input')) {
            abort(403, 'Akses ditolak.');
        }

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

        return redirect()->route('admin.kpi.daily-input.index', ['tanggal' => $request->tanggal, 'tab' => 'kebersihan'])
            ->with('success', 'Checklist kebersihan & bau beserta foto bukti berhasil disimpan.');
    }

    /**
     * Simpan Log Jam Mesin & Wheel Loader (PIC: Agung)
     */
    public function storeMachineLog(Request $request)
    {
        if (!Gate::allows('create_kpi_daily_input') && !Gate::allows('view_kpi_daily_input')) {
            abort(403, 'Akses ditolak.');
        }

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

        return redirect()->route('admin.kpi.daily-input.index', ['tanggal' => $request->tanggal, 'tab' => 'mesin'])
            ->with('success', 'Log aktivitas mesin & alat berat berhasil disimpan.');
    }

    /**
     * Simpan Keluhan Stakeholder (PIC: Nita / Budi)
     */
    public function storeComplaint(Request $request)
    {
        if (!Gate::allows('create_kpi_daily_input') && !Gate::allows('view_kpi_daily_input')) {
            abort(403, 'Akses ditolak.');
        }

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

        return redirect()->route('admin.kpi.daily-input.index', ['tanggal' => $request->tanggal, 'tab' => 'complaint'])
            ->with('success', 'Catatan keluhan stakeholder berhasil disimpan.');
    }

    /**
     * Update penyelesaian keluhan
     */
    public function resolveComplaint(Request $request, KpiStakeholderComplaint $complaint)
    {
        if (!Gate::allows('create_kpi_daily_input') && !Gate::allows('view_kpi_daily_input')) {
            abort(403, 'Akses ditolak.');
        }

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

        return redirect()->route('admin.kpi.daily-input.index', [
            'tanggal' => $complaint->tanggal->format('Y-m-d'),
            'tab' => 'complaint',
        ])->with('success', 'Status tindak lanjut keluhan berhasil diperbarui.');
    }
}
