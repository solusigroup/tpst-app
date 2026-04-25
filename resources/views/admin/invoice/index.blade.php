@extends('layouts.admin')
@section('title', 'Invoice')

@section('content')
<div class="page-header">
    <div>
        <h1>Invoice</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Invoice</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('admin.invoice.merge-drafts') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-warning text-dark" onclick="return confirm('Apakah Anda yakin ingin menggabungkan semua Invoice Draft dari Klien yang sama? (Termasuk konsolidasi Klien DLH ke Dinas Lingkungan Hidup)')">
                <i class="cil-object-group me-1"></i> Gabung Draft
            </button>
        </form>
        <a href="{{ route('admin.invoice.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Buat Invoice</a>
    </div>
</div>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari No. Invoice / Klien..." value="{{ request('search') }}" style="min-width: 250px;"></div>
            <div class="col-auto">
                <select name="status" class="form-select"><option value="">Semua Status</option>@foreach(['Draft','Sent','Paid','Canceled'] as $s)<option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>@endforeach</select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request()->hasAny(['search','status']))<div class="col-auto"><a href="{{ route('admin.invoice.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>No. Invoice</th><th>Klien</th><th>Jenis</th><th>Periode</th><th>Total</th><th>DP</th><th>Sisa</th><th>Status</th><th>Tgl Invoice</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($invoices as $item)
                    <tr onclick="window.location='{{ route('admin.invoice.show', $item) }}'" style="cursor: pointer;">
                        <td><strong>{{ $item->nomor_invoice ?? '-' }}</strong></td>
                        <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->klien->jenis ?? '-' }}</span></td>
                        <td>{{ $item->periode_bulan }}/{{ $item->periode_tahun }}</td>
                        <td>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format($item->uang_muka, 0, ',', '.') }}</td>
                        <td class="fw-bold">Rp {{ number_format($item->total_tagihan - $item->uang_muka, 0, ',', '.') }}</td>
                        <td>
                            @php $invColors = ['Paid'=>'success','Sent'=>'info','Draft'=>'warning','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $invColors[$item->status] ?? 'secondary' }}">{{ $item->status }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_invoice)->format('d/m/Y') }}</td>
                        <td class="text-end" onclick="event.stopPropagation()">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('invoices.print', $item) }}" target="_blank" class="btn btn-outline-success" title="Cetak"><i class="cil-print"></i></a>
                                <a href="{{ route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\Invoice'), 'ref_id' => $item->id]) }}" class="btn btn-outline-info" title="Buat Jurnal"><i class="cil-book"></i></a>
                                <a href="{{ route('admin.invoice.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.invoice.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages()) <div class="card-footer bg-white">{{ $invoices->links() }}</div> @endif
</div>
@endsection
