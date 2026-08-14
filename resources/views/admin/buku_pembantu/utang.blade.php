@extends('layouts.admin')
@section('title', 'Buku Pembantu Utang / Liabilitas')

@section('content')
<div class="page-header">
    <div>
        <h1>Buku Pembantu Utang / Liabilitas</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Buku Pembantu Utang / Liabilitas</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary bg-opacity-10 h-100">
            <div class="card-body">
                <div class="text-uppercase small fw-bold text-primary mb-1">Total Utang Awal</div>
                <div class="fs-4 fw-bold text-primary">Rp {{ number_format($totalHutangAwal ?? 0, 0, ',', '.') }}</div>
                <div class="small text-muted mt-1">Akumulasi seluruh nilai terbentuknya utang</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success bg-opacity-10 h-100">
            <div class="card-body">
                <div class="text-uppercase small fw-bold text-success mb-1">Total Sudah Dibayar</div>
                <div class="fs-4 fw-bold text-success">Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}</div>
                <div class="small text-muted mt-1">Total pembayaran yang telah terverifikasi</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger bg-opacity-10 h-100">
            <div class="card-body">
                <div class="text-uppercase small fw-bold text-danger mb-1">Sisa Utang (Outstanding)</div>
                <div class="fs-4 fw-bold text-danger">Rp {{ number_format($totalJumlah ?? 0, 0, ',', '.') }}</div>
                <div class="small text-muted mt-1">Sisa kewajiban yang belum dilunasi</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Pencarian</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Pemilik Hutang / Keterangan / COA..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Akun COA Liabilitas</label>
                <select name="coa_id" class="form-select form-select-sm">
                    <option value="">-- Semua Akun Liabilitas --</option>
                    @foreach($liabilityCoas as $coa)
                        <option value="{{ $coa->id }}" {{ request('coa_id') == $coa->id ? 'selected' : '' }}>
                            {{ $coa->kode_akun }} - {{ $coa->nama_akun }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status Pelunasan</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100" type="submit">
                    <i class="cil-search me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'coa_id', 'status']))
                    <a href="{{ route('admin.buku-pembantu.utang') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th>Tgl Terbentuk</th>
                        <th>Pemilik Hutang</th>
                        <th>Akun COA Liabilitas</th>
                        <th>Keterangan</th>
                        <th class="text-end">Jumlah Utang</th>
                        <th class="text-end">Jmlh Dibayar</th>
                        <th>Tgl Dibayar</th>
                        <th class="text-end">Sisa (Outstanding)</th>
                        <th>Jatuh Tempo</th>
                        <th class="text-center">Bukti</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $item)
                    <tr>
                        <td class="text-nowrap">{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $item->contactable->nama_vendor ?? $item->contactable->nama_klien ?? $item->contactable->name ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            @if($item->coa)
                                <span class="badge bg-secondary bg-opacity-10 text-dark border">
                                    {{ $item->coa->kode_akun }} - {{ $item->coa->nama_akun }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="small text-wrap" style="max-width: 200px;">{{ $item->keterangan }}</td>
                        <td class="text-end fw-bold text-dark text-nowrap">
                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-semibold text-success text-nowrap">
                            Rp {{ number_format($item->terbayar, 0, ',', '.') }}
                        </td>
                        <td class="text-nowrap small">
                            @if($item->settledByJurnalHeader)
                                <span title="Via Jurnal JV-{{ $item->settledByJurnalHeader->nomor_referensi }}">
                                    {{ $item->settledByJurnalHeader->tanggal->format('d/m/Y') }}
                                </span>
                            @elseif($item->status == 'lunas')
                                {{ $item->updated_at->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-nowrap {{ $item->status == 'lunas' ? 'text-success' : 'text-danger' }}">
                            @if($item->status == 'lunas')
                                Rp 0
                            @else
                                Rp {{ number_format($item->jumlah - $item->terbayar, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="text-nowrap {{ $item->tanggal_jatuh_tempo < now() && $item->status == 'pending' ? 'text-danger fw-bold' : '' }}">
                            {{ $item->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="text-center text-nowrap">
                            @if($item->jurnalHeader?->bukti_transaksi)
                                <a href="{{ asset('storage/' . $item->jurnalHeader->bukti_transaksi) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat Bukti Transaksi">
                                    <i class="cil-image"></i>
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            <span class="badge bg-{{ $item->status == 'lunas' ? 'success' : 'warning' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-4 text-body-secondary">
                            <i class="cil-info me-1"></i> Belum ada data buku pembantu utang / liabilitas.
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($entries->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $entries->links() }}
        </div>
    @endif
</div>
@endsection
