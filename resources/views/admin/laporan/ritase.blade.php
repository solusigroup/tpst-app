@extends('layouts.admin')
@section('title', 'Laporan Ritase')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none"><div><h1>Laporan Ritase</h1></div><button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button></div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="{{ $dari }}"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Klien</label>
            <select name="klien_id" class="form-select">
                <option value="">-- Semua Klien --</option>
                @foreach($kliens as $k)<option value="{{ $k->id }}" {{ $klienId == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Status</label>
            <select name="status" class="form-select">
                <option value="">-- Semua --</option>
                @foreach(['masuk','timbang','keluar','selesai'] as $s)<option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Tanggal</th><th>No Tiket</th><th>Armada</th><th>Klien</th><th class="text-end">Berat Netto</th><th class="text-end">Biaya Tipping</th><th>Status Tiket</th><th>Status Invoice</th></tr></thead>
                <tbody>
                    @forelse($rows as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->waktu_masuk)->format('d M Y') }}</td>
                        <td><strong>{{ $r->nomor_tiket }}</strong></td>
                        <td>{{ $r->armada->plat_nomor ?? '-' }}</td>
                        <td>{{ $r->klien->nama_klien ?? '-' }}</td>
                        <td class="text-end">{{ number_format($r->berat_netto, 2, ',', '.') }} kg</td>
                        <td class="text-end">Rp {{ number_format($r->biaya_tipping, 0, ',', '.') }}</td>
                        <td>
                            @php $statusColors = ['masuk'=>'warning','timbang'=>'info','keluar'=>'primary','selesai'=>'success']; @endphp
                            <span class="badge bg-{{ $statusColors[$r->status] ?? 'secondary' }}">{{ ucfirst($r->status) }}</span>
                        </td>
                        <td>
                            @php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $invoiceColors[$r->status_invoice] ?? 'secondary' }}">{{ $r->status_invoice ?? 'Unbilled' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-body-secondary">Belum ada data ritase.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr><td colspan="4" class="text-end">TOTAL ({{ number_format($totals->total_rows ?? 0, 0, ',', '.') }} Ritase)</td><td class="text-end">{{ number_format($totals->total_netto ?? 0, 2, ',', '.') }} kg</td><td class="text-end">Rp {{ number_format($totals->total_tipping ?? 0, 0, ',', '.') }}</td><td colspan="2"></td></tr>
                </tfoot>
            </table>
        </div>
    </div>
    @if($rows->hasPages()) <div class="card-footer bg-white">{{ $rows->links() }}</div> @endif
</div>
@endsection
