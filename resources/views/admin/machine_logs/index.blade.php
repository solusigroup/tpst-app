@extends('layouts.admin')

@section('title', 'Logbook Mesin')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Logbook Operasional Mesin</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Logbook Mesin</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.machine-logs.create') }}" class="btn btn-primary">
        <i class="cil-pen"></i> Isi Logbook Baru
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th>Waktu Log</th>
                        <th>Waktu Cek</th>
                        <th>Mesin</th>
                        <th>Status Lampu</th>
                        <th>Keterangan</th>
                        <th>Operator</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td><span class="badge bg-secondary">{{ $log->waktu_cek }}</span></td>
                        <td><strong>{{ $log->machine->nama_mesin }}</strong> <br><small class="text-muted">{{ $log->machine->nomor_mesin }}</small></td>
                        <td>
                            @if($log->status_lampu == 'Hijau')
                                <span class="badge bg-success"><i class="cil-check-circle"></i> Hijau (Normal)</span>
                            @elseif($log->status_lampu == 'Kuning')
                                <span class="badge bg-warning"><i class="cil-warning"></i> Kuning (Attention)</span>
                            @elseif($log->status_lampu == 'Biru')
                                <span class="badge bg-info"><i class="cil-settings"></i> Biru (Maintenance)</span>
                            @elseif($log->status_lampu == 'Merah')
                                <span class="badge bg-danger"><i class="cil-x-circle"></i> Merah (Emergency)</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($log->keterangan ?? '-', 50) }}</td>
                        <td>{{ $log->user->name ?? 'Sistem' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.machine-logs.edit', $log->id) }}" class="btn btn-outline-info" title="Edit">
                                    <i class="cil-pencil"></i>
                                </a>
                                <form action="{{ route('admin.machine-logs.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin menghapus log ini?');">
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
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat logbook mesin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
