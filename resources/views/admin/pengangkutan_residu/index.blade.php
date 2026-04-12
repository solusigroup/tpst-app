@extends('layouts.admin')
@section('title', 'Pengangkutan Residu')

@section('content')
<div class="page-header">
    <div>
        <h1>Pengangkutan Residu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengangkutan Residu</li>
            </ol>
        </nav>
    </div>
    <div class="btn-toolbar">
        <a href="{{ route('admin.pengangkutan-residu.create') }}" class="btn btn-primary">
            <i class="cil-plus me-1"></i> Catat Residu
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="No. Tiket / Plat..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="cil-search me-1"></i> Cari
                </button>
            </div>
            @if(request('search'))
                <div class="col-auto">
                    <a href="{{ route('admin.pengangkutan-residu.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Tanggal</th>
                        <th>Armada</th>
                        <th>Netto (Kg)</th>
                        <th>Biaya</th>
                        <th>Tujuan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $item)
                    <tr>
                        <td><strong>{{ $item->nomor_tiket }}</strong></td>
                        <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <div>{{ $item->armada->plat_nomor }}</div>
                            <small class="text-body-secondary">{{ $item->armada->nama_armada }}</small>
                        </td>
                        <td class="fw-bold">{{ number_format($item->berat_netto, 0, ',', '.') }}</td>
                        <td class="text-danger fw-semibold">Rp {{ number_format($item->biaya_retribusi, 0, ',', '.') }}</td>
                        <td>{{ $item->tujuan }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.pengangkutan-residu.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="cil-find-in-page"></i>
                                </a>
                                <a href="{{ route('admin.pengangkutan-residu.edit', $item) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="cil-pencil"></i>
                                </a>
                                <form action="{{ route('admin.pengangkutan-residu.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
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
                        <td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data pengangkutan residu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($entries->hasPages())
    <div class="card-footer bg-white">
        {{ $entries->links() }}
    </div>
    @endif
</div>
@endsection
