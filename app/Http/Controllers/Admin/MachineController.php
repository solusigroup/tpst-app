<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Machine;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::latest()->paginate(10);
        return view('admin.machines.index', compact('machines'));
    }

    public function create()
    {
        return view('admin.machines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_mesin' => 'required|string|max:255',
            'nama_mesin' => 'required|string|max:255',
        ]);

        Machine::create([
            'nomor_mesin' => $request->nomor_mesin,
            'nama_mesin' => $request->nama_mesin,
        ]);

        return redirect()->route('admin.machines.index')->with('success', 'Data mesin berhasil ditambahkan.');
    }

    public function edit(Machine $machine)
    {
        return view('admin.machines.edit', compact('machine'));
    }

    public function update(Request $request, Machine $machine)
    {
        $request->validate([
            'nomor_mesin' => 'required|string|max:255',
            'nama_mesin' => 'required|string|max:255',
        ]);

        $machine->update([
            'nomor_mesin' => $request->nomor_mesin,
            'nama_mesin' => $request->nama_mesin,
        ]);

        return redirect()->route('admin.machines.index')->with('success', 'Data mesin berhasil diperbarui.');
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();
        return redirect()->route('admin.machines.index')->with('success', 'Data mesin berhasil dihapus.');
    }
}
