<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \App\Models\Machine;
use \App\Models\MachineLog;
use \App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;

class MachineLogController extends Controller
{
    public function index()
    {
        // View for operator logbook history
        $logs = MachineLog::with(['machine', 'user'])->latest()->paginate(15);
        return view('admin.machine_logs.index', compact('logs'));
    }

    public function create()
    {
        $machines = Machine::all();
        return view('admin.machine_logs.create', compact('machines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'waktu_cek' => 'required|in:Engine On,Engine Off',
            'status_lampu' => 'required|in:Hijau,Kuning,Biru,Merah',
            'keterangan' => 'nullable|string',
        ]);

        $log = MachineLog::create([
            'machine_id' => $request->machine_id,
            'waktu_cek' => $request->waktu_cek,
            'status_lampu' => $request->status_lampu,
            'keterangan' => $request->keterangan,
            'user_id' => Auth::id(),
        ]);

        // Send WA Notification if Red (Emergency / Breakdown)
        if ($request->status_lampu === 'Merah') {
            $machine = Machine::find($request->machine_id);
            $message = "🚨 *DARURAT MESIN TPST* 🚨\n\n"
                     . "Mesin: *" . $machine->nama_mesin . " (" . $machine->nomor_mesin . ")*\n"
                     . "Waktu: *" . $request->waktu_cek . "*\n"
                     . "Status: 🔴 *MERAH (Breakdown/Emergency)*\n"
                     . "Keterangan: " . ($request->keterangan ?: "Tidak ada keterangan") . "\n\n"
                     . "Diperiksa oleh: " . Auth::user()->name . "\n"
                     . "Waktu Log: " . now()->format('Y-m-d H:i:s');
            
            $waTarget = env('WA_ENGINEER_PHONE', '+6282141643495');
            WhatsAppService::sendMessage($waTarget, $message);
        }

        return redirect()->route('admin.machine-logs.index')->with('success', 'Log mesin berhasil dicatat.');
    }

    public function edit(MachineLog $machineLog)
    {
        // Operator doesn't usually edit, but let's provide it if admin needs it.
        $machines = Machine::all();
        return view('admin.machine_logs.edit', compact('machineLog', 'machines'));
    }

    public function update(Request $request, MachineLog $machineLog)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'waktu_cek' => 'required|in:Engine On,Engine Off',
            'status_lampu' => 'required|in:Hijau,Kuning,Biru,Merah',
            'keterangan' => 'nullable|string',
        ]);

        $machineLog->update($request->only('machine_id', 'waktu_cek', 'status_lampu', 'keterangan'));

        return redirect()->route('admin.machine-logs.index')->with('success', 'Log mesin berhasil diperbarui.');
    }

    public function destroy(MachineLog $machineLog)
    {
        $machineLog->delete();
        return redirect()->route('admin.machine-logs.index')->with('success', 'Log mesin dihapus.');
    }
}
