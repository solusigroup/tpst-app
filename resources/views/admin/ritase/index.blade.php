@extends('layouts.admin')
@section('title', 'Ritase')

@section('content')
<div class="page-header">
    <div>
        <h1>Ritase</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Ritase</li></ol></nav>
    </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.ritase.export-rekap', request()->all()) }}" class="btn btn-danger" target="_blank"><i class="cil-print me-1"></i> Cetak Rekap (PDF)</a>
        <a href="{{ route('admin.ritase.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Ritase</a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nomor tiket / tiket manual..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <input type="date" name="start_date" class="form-control" title="Tanggal Mulai" value="{{ request('start_date') }}">
            </div>
            <div class="col-auto">
                <input type="date" name="end_date" class="form-control" title="Tanggal Selesai" value="{{ request('end_date') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button>
            </div>
            @if(request()->hasAny(['search', 'start_date', 'end_date']))
                <div class="col-auto"><a href="{{ route('admin.ritase.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            @endif
        </form>
    </div>
    
    <div class="card-body border-bottom bg-primary bg-opacity-10 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold text-primary">
                <i class="cil-weight me-2"></i> TOTAL BERAT NETTO 
                @if(request('start_date') && request('end_date'))
                    ({{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d M Y') }})
                @elseif(request('start_date'))
                    (Melampaui {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y') }})
                @elseif(request('end_date'))
                    (Mendahului {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d M Y') }})
                @else
                    (Semua Waktu)
                @endif
            </div>
            <div class="fs-4 fw-bold text-primary">{{ number_format($totalBeratNetto ?? 0, 2, ',', '.') }} kg</div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Armada</th>
                        <th>Klien</th>
                        <th>Berat Netto</th>
                        <th>Status</th>
                        <th>Waktu Masuk</th>
                        <th>Bukti</th>
                        <th>Foto</th>
                        <th>Approved</th>
                        <th>Status Invoice</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ritase as $item)
                    <tr>
                        <td><strong>{{ $item->nomor_tiket ?? '-' }}</strong></td>
                        <td>{{ $item->armada->plat_nomor ?? '-' }}</td>
                        <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td>{{ number_format($item->berat_netto, 2, ',', '.') }} kg</td>
                        <td>
                            @php $statusColors = ['masuk'=>'info','timbang'=>'warning','keluar'=>'primary','selesai'=>'success']; @endphp
                            <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">{{ ucfirst($item->status) }}</span>
                        </td>
                        <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $item->tiket ?? '-' }}</td>
                        <td>
                            @if($item->foto_tiket)
                                <a href="{{ asset('storage/' . $item->foto_tiket) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="cil-image"></i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->is_approved)
                                <span class="badge bg-success"><i class="cil-check-circle me-1"></i> Approved</span>
                            @else
                                <form method="POST" action="{{ route('admin.ritase.approve', $item) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="cil-check me-1"></i> Approve
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td>
                            @php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $invoiceColors[$item->status_invoice] ?? 'secondary' }}">{{ $item->status_invoice ?? 'Unbilled' }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.ritase.show', $item) }}" class="btn btn-outline-info" title="Lihat"><i class="cil-magnifying-glass"></i></a>
                                <a href="{{ route('admin.ritase.edit', $item) }}" class="btn btn-outline-primary" title="Edit"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.ritase.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Hapus"><i class="cil-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-body-secondary">Belum ada data ritase.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ritase->hasPages())
    <div class="card-footer bg-white">{{ $ritase->links() }}</div>
    @endif
</div>
@endsection
