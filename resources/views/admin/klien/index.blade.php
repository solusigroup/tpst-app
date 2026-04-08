@extends('layouts.admin')
@section('title', 'Klien')

@section('content')
<div class="page-header">
    <div>
        <h1>Klien</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Klien</li></ol></nav>
    </div>
    <a href="{{ route('admin.klien.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Klien</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nama klien..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach(['DLH','Swasta','Offtaker','Internal'] as $j)
                        <option value="{{ $j }}" {{ request('jenis') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request()->hasAny(['search','jenis']))
                <div class="col-auto"><a href="{{ route('admin.klien.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Nama Klien</th><th>Jenis</th><th>Tarif Bulanan</th><th>Kontak</th><th>Dibuat</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($kliens as $item)
                    <tr>
                        <td><strong><a href="{{ route('admin.klien.show', $item) }}" class="text-decoration-none">{{ $item->nama_klien }}</a></strong></td>
                        <td>
                            @php
                                $badgeColor = match($item->jenis) {
                                    'DLH' => 'bg-info',
                                    'Swasta' => 'bg-primary',
                                    'Offtaker' => 'bg-success',
                                    'Internal' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }}">{{ $item->jenis }}</span>
                        </td>
                        <td>
                            @if($item->jenis == 'Swasta')
                                @if($item->jenis_tarif) <span class="badge bg-light text-dark border me-1">{{ $item->jenis_tarif }}</span> @endif
                                {{ $item->tarif_bulanan ? 'Rp ' . number_format($item->tarif_bulanan, 0, ',', '.') : '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->kontak ?? '-' }}</td>
                        <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.klien.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.klien.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data klien.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($kliens->hasPages()) <div class="card-footer bg-white">{{ $kliens->links() }}</div> @endif
</div>
@endsection
