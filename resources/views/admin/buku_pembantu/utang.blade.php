@extends('layouts.admin')
@section('title', 'Buku Pembantu Utang Lancar')

@section('content')
<div class="page-header">
    <div>
        <h1>Buku Pembantu Utang Lancar</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Buku Pembantu Utang Lancar</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nama vendor..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="cil-search me-1"></i> Cari
                </button>
            </div>
            @if(request()->hasAny(['search', 'status']))
                <div class="col-auto">
                    <a href="{{ route('admin.buku-pembantu.utang') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Vendor</th>
                        <th>Keterangan</th>
                        <th class="text-end">Jumlah</th>
                        <th>Jatuh Tempo</th>
                        <th>Bukti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td><strong>{{ $item->contactable->nama_vendor ?? $item->contactable->nama_klien ?? 'N/A' }}</strong></td>
                        <td class="small">{{ $item->keterangan }}</td>
                        <td class="text-end fw-bold text-danger">
                            <div>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                            @if($item->terbayar > 0 && $item->status == 'pending')
                                <div class="text-muted small">Sisa: Rp {{ number_format($item->jumlah - $item->terbayar, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="{{ $item->tanggal_jatuh_tempo < now() && $item->status == 'pending' ? 'text-danger fw-bold' : '' }}">
                            {{ $item->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td>
                            @if($item->jurnalHeader?->bukti_transaksi)
                                <a href="{{ asset('storage/' . $item->jurnalHeader->bukti_transaksi) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="cil-image"></i>
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->status == 'lunas' ? 'success' : 'warning' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data utang lancar.</td>
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
