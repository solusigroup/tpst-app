@extends('layouts.admin')
@section('title', 'Jurnal')

@section('content')
<div class="page-header">
    <div>
        <h1>Jurnal</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Jurnal</li></ol></nav>
    </div>
    <a href="{{ route('admin.jurnal.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Jurnal</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari referensi/deskripsi..." value="{{ request('search') }}"></div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                    <option value="unposted" {{ request('status') == 'unposted' ? 'selected' : '' }}>Unposted</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request()->hasAny(['search','status']))<div class="col-auto"><a href="{{ route('admin.jurnal.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Tanggal</th><th>No. Referensi</th><th>Deskripsi</th><th>Status</th><th>Bukti</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($jurnals as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td><strong>{{ $item->nomor_referensi ?? '-' }}</strong></td>
                        <td>{{ \Illuminate\Support\Str::limit($item->deskripsi, 50) }}</td>
                        <td><span class="badge bg-{{ $item->status === 'posted' ? 'success' : 'warning' }}">{{ ucfirst($item->status) }}</span></td>
                        <td>
                            @if($item->bukti_transaksi)
                                <img src="{{ asset('storage/' . $item->bukti_transaksi) }}" class="rounded" style="width:32px;height:32px;object-fit:cover;" alt="bukti">
                            @else -
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                @if($item->status !== 'posted')
                                    <form method="POST" action="{{ route('admin.jurnal.post', $item) }}" class="d-inline" onsubmit="return confirm('Post jurnal ini?')">@csrf<button class="btn btn-outline-success" title="Post"><i class="cil-check-circle"></i></button></form>
                                @else
                                    <form method="POST" action="{{ route('admin.jurnal.unpost', $item) }}" class="d-inline" onsubmit="return confirm('Unpost jurnal ini?')">@csrf<button class="btn btn-outline-warning" title="Unpost"><i class="cil-x-circle"></i></button></form>
                                @endif
                                <a href="{{ route('admin.jurnal.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.jurnal.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jurnals->hasPages()) <div class="card-footer bg-white">{{ $jurnals->links() }}</div> @endif
</div>
@endsection
