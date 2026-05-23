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
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari', $dari ?? '') }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai', $sampai ?? '') }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Pembeli / Klien</label>
                <select name="klien_id" class="form-select">
                    <option value="">Semua Pembeli</option>
                    @foreach($kliens ?? [] as $k)
                        <option value="{{ $k->id }}" {{ request('klien_id', $klienId ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Jenis Produk</label>
                <input type="text" name="search" class="form-control" placeholder="Cari jenis produk..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit"><i class="cil-search me-1"></i> Terapkan Filter</button>
            </div>
            @if(request('search') || request('klien_id') || request('dari') || request('sampai'))
            <div class="col-auto">
                <a href="{{ route('admin.penjualan.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Pembeli</th><th>Tanggal</th><th>Jenis Produk</th><th>Berat (kg)</th><th>Harga Satuan</th><th>Total Harga</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($penjualans as $item)
                    <tr>
                        <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->jenis_produk }}</td>
                        <td>{{ number_format($item->berat_kg, 2, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.penjualan.show', $item) }}" class="btn btn-outline-info" title="Lihat"><i class="cil-search"></i></a>
                                <a href="{{ route('admin.penjualan.edit', $item) }}" class="btn btn-outline-primary" title="Edit"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.penjualan.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger" title="Hapus"><i class="cil-trash"></i></button></form>
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
