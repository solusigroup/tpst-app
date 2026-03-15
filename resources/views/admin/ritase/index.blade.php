@extends('layouts.admin')
@section('title', 'Ritase')

@section('content')
<div class="page-header">
    <div>
        <h1>Ritase</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Ritase</li></ol></nav>
    </div>
    <a href="{{ route('admin.ritase.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Ritase</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nomor tiket..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button>
            </div>
            @if(request()->hasAny(['search']))
                <div class="col-auto"><a href="{{ route('admin.ritase.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Armada</th>
                        <th>Klien</th>
                        <th>Berat Netto</th>
                        <th>Status</th>
                        <th>Waktu Masuk</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ritase as $item)
                    <tr>
                        <td><strong>{{ $item->nomor_tiket ?? '-' }}</strong></td>
                        <td>{{ $item->armada->plat_nomor ?? '-' }}</td>
                        <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td>{{ number_format($item->berat_netto, 2, ',', '.') }} kg</td>
                        <td>
                            @php $statusColors = ['masuk'=>'info','timbang'=>'warning','keluar'=>'primary','selesai'=>'success']; @endphp
                            <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">{{ ucfirst($item->status) }}</span>
                        </td>
                        <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.ritase.edit', $item) }}" class="btn btn-outline-primary" title="Edit"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.ritase.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Hapus"><i class="cil-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data ritase.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ritase->hasPages())
    <div class="card-footer bg-white">{{ $ritase->links() }}</div>
    @endif
</div>
@endsection
