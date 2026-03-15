@extends('layouts.admin')
@section('title', 'Armada')

@section('content')
<div class="page-header">
    <div>
        <h1>Armada</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Armada</li></ol></nav>
    </div>
    <a href="{{ route('admin.armada.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Armada</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari plat nomor..." value="{{ request('search') }}"></div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request('search'))<div class="col-auto"><a href="{{ route('admin.armada.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Plat Nomor</th><th>Klien</th><th>Kapasitas Maks</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($armadas as $item)
                    <tr>
                        <td><strong>{{ $item->plat_nomor }}</strong></td>
                        <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td>{{ number_format($item->kapasitas_maksimal, 0, ',', '.') }} kg</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.armada.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.armada.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-body-secondary">Belum ada data armada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($armadas->hasPages()) <div class="card-footer bg-white">{{ $armadas->links() }}</div> @endif
</div>
@endsection
