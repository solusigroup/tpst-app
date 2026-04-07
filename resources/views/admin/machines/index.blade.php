@extends('layouts.admin')

@section('title', 'Data Mesin')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Master Data Mesin</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mesin</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.machines.create') }}" class="btn btn-primary">
        <i class="cil-plus"></i> Tambah Mesin
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode/Nomor Mesin</th>
                        <th>Nama Mesin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machines as $machine)
                    <tr>
                        <td>{{ $loop->iteration + $machines->firstItem() - 1 }}</td>
                        <td><span class="badge bg-secondary">{{ $machine->nomor_mesin }}</span></td>
                        <td>{{ $machine->nama_mesin }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.machines.edit', $machine->id) }}" class="btn btn-outline-info" title="Edit">
                                    <i class="cil-pencil"></i>
                                </a>
                                <form action="{{ route('admin.machines.destroy', $machine->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mesin ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="cil-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data mesin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $machines->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
