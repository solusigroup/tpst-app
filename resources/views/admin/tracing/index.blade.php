@extends('layouts.admin')
@section('title', 'Tracing Transaksi')

@section('content')
<div class="page-header">
    <div>
        <h1>Tracing Transaksi & Audit Trail</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Tracing</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.tracing.audit') }}" class="btn btn-outline-warning"><i class="cil-shield-alt me-1"></i> Audit Check</a>
        <form method="POST" action="{{ route('admin.tracing.sync') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-success" onclick="return confirm('Jalankan sinkronisasi otomatis untuk memperbaiki data yang tidak sinkron?')"><i class="cil-sync me-1"></i> Sinkronisasi</button>
        </form>
    </div>
</div>

{{-- Summary Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-start-4 border-start-primary h-100">
            <div class="card-body py-3">
                <div class="text-body-secondary text-truncate small text-uppercase fw-semibold mb-1">Piutang Jasa Swasta</div>
                <div class="fs-5 fw-bold text-primary">Rp {{ number_format($totalPiutangSwasta ?? 0, 0, ',', '.') }}</div>
                <div class="text-body-secondary small">Outstanding saat ini</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-start-4 border-start-info h-100">
            <div class="card-body py-3">
                <div class="text-body-secondary text-truncate small text-uppercase fw-semibold mb-1">Piutang Penjualan Pilahan</div>
                <div class="fs-5 fw-bold text-info">Rp {{ number_format($totalPiutangOfftaker ?? 0, 0, ',', '.') }}</div>
                <div class="text-body-secondary small">Outstanding saat ini</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-start-4 border-start-success h-100">
            <div class="card-body py-3">
                <div class="text-body-secondary text-truncate small text-uppercase fw-semibold mb-1">Total Piutang Lunas</div>
                <div class="fs-5 fw-bold text-success">Rp {{ number_format($totalLunas ?? 0, 0, ',', '.') }}</div>
                <div class="text-body-secondary small">Sudah terbayar</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-start-4 {{ ($discrepancyCount ?? 0) > 0 ? 'border-start-danger' : 'border-start-secondary' }} h-100">
            <div class="card-body py-3">
                <div class="text-body-secondary text-truncate small text-uppercase fw-semibold mb-1">Discrepancy</div>
                <div class="fs-5 fw-bold {{ ($discrepancyCount ?? 0) > 0 ? 'text-danger' : 'text-body-secondary' }}">{{ $discrepancyCount ?? 0 }}</div>
                <div class="text-body-secondary small">Data perlu dicek</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Jenis Transaksi</label>
                <select name="jenis" class="form-select">
                    <option value="semua" {{ request('jenis', 'semua') == 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="piutang_swasta" {{ request('jenis') == 'piutang_swasta' ? 'selected' : '' }}>Piutang Jasa Swasta</option>
                    <option value="penjualan_pilahan" {{ request('jenis') == 'penjualan_pilahan' ? 'selected' : '' }}>Penjualan Hasil Pilahan</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="No. Invoice / Klien..." value="{{ request('search') }}" style="min-width: 200px;">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Status Invoice</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    @foreach(['Draft','Sent','Paid','Canceled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button>
            </div>
            @if(request()->hasAny(['search','status','jenis','dari','sampai']))
            <div class="col-auto">
                <a href="{{ route('admin.tracing.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
            @endif
        </form>
    </div>
</div>

{{-- Results Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Invoice</th>
                        <th>Klien</th>
                        <th>Jenis</th>
                        <th>Total</th>
                        <th class="text-center">Operasional</th>
                        <th class="text-center">Jurnal GL</th>
                        <th class="text-center">Buku Pembantu</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td><strong>{{ $inv->nomor_invoice ?? '-' }}</strong></td>
                        <td>{{ $inv->klien->nama_klien ?? '-' }}</td>
                        <td>
                            @if($inv->has_penjualan && $inv->has_ritase)
                                <span class="badge bg-secondary">Jasa + Pilahan</span>
                            @elseif($inv->has_penjualan)
                                <span class="badge bg-info text-white">Penjualan Pilahan</span>
                            @else
                                <span class="badge bg-primary">Jasa / Tipping</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($inv->has_ritase || $inv->has_penjualan)
                                <i class="cil-check-circle text-success fs-5" title="Ada dokumen operasional"></i>
                            @else
                                <i class="cil-x-circle text-danger fs-5" title="Tidak ada dokumen operasional"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($inv->has_jurnal)
                                <i class="cil-check-circle text-success fs-5" title="Jurnal GL ada"></i>
                            @else
                                <i class="cil-x-circle text-danger fs-5" title="Jurnal GL tidak ditemukan"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($inv->has_buku_pembantu)
                                <i class="cil-check-circle text-success fs-5" title="Buku Pembantu ada"></i>
                            @else
                                <i class="cil-x-circle text-danger fs-5" title="Buku Pembantu tidak ditemukan"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @php $invColors = ['Paid'=>'success','Sent'=>'info','Draft'=>'warning','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $invColors[$inv->status] ?? 'secondary' }}">{{ $inv->status }}</span>
                            @if($inv->bp_status)
                                <br><small class="text-body-secondary">BP: {{ ucfirst($inv->bp_status) }}</small>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.tracing.show', ['type' => 'invoice', 'id' => $inv->id]) }}" class="btn btn-sm btn-outline-primary" title="Lacak Alur">
                                <i class="cil-find-in-page me-1"></i> Lacak
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4 text-body-secondary">Tidak ada data transaksi yang ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer bg-white">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
