@extends('layouts.admin')
@section('title', 'Hasil Pilahan')

@section('content')
<div class="page-header">
    <div>
        <h1>Hasil Pilahan Sampah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Hasil Pilahan</li></ol></nav>
    </div>
    <a href="{{ route('admin.hasil-pilahan.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Data</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari jenis/petugas..." value="{{ request('search') }}"></div>
            <div class="col-auto">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach(['Organik','Anorganik','B3','Residu'] as $k)<option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $k }}</option>@endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request()->hasAny(['search','kategori']))<div class="col-auto"><a href="{{ route('admin.hasil-pilahan.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Kategori</th><th>Jenis</th><th>Tonase</th><th>Jml Bal</th><th>Petugas</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($hasilPilahans as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>
                            @php $catColors = ['Organik'=>'success','Anorganik'=>'info','B3'=>'danger','Residu'=>'warning']; @endphp
                            <span class="badge bg-{{ $catColors[$item->kategori] ?? 'secondary' }}">{{ $item->kategori }}</span>
                        </td>
                        <td>{{ $item->jenis }}</td>
                        <td>{{ number_format($item->tonase, 2, ',', '.') }} kg</td>
                        <td>{{ $item->jml_bal ? $item->jml_bal . ' Bal' : '-' }}</td>
                        <td>{{ $item->officer }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.hasil-pilahan.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.hasil-pilahan.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin ingin menghapus data ini?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($hasilPilahans->hasPages()) <div class="card-footer bg-white">{{ $hasilPilahans->links() }}</div> @endif
</div>
@endsection
