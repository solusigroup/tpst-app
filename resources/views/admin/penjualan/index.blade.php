@extends('layouts.admin')
@section('title', 'Penjualan')

@section('content')
<div class="page-header">
    <div>
        <h1>Penjualan</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Penjualan</li></ol></nav>
    </div>
    <a href="{{ route('admin.penjualan.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Penjualan</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari jenis produk..." value="{{ request('search') }}"></div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request('search'))<div class="col-auto"><a href="{{ route('admin.penjualan.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Klien</th><th>Tanggal</th><th>Jenis Produk</th><th>Berat (kg)</th><th>Total Harga</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($penjualans as $item)
                    <tr>
                        <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->jenis_produk }}</td>
                        <td>{{ number_format($item->berat_kg, 2, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.penjualan.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.penjualan.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
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
    @if($penjualans->hasPages()) <div class="card-footer bg-white">{{ $penjualans->links() }}</div> @endif
</div>
@endsection
