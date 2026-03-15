@extends('layouts.admin')
@section('title', 'Laporan Hasil Pilahan')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none"><div><h1>Laporan Hasil Pilahan Sampah</h1></div><button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button></div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="{{ $dari }}"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">-- Semua --</option>
                @foreach(['Organik','Anorganik','B3','Residu'] as $c)<option value="{{ $c }}" {{ $kategori == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Tanggal</th><th>Kategori</th><th>Jenis</th><th>Petugas</th><th class="text-end">Tonase</th></tr></thead>
                <tbody>
                    @forelse($rows as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                        <td>
                            @php $catColors = ['Organik'=>'success','Anorganik'=>'info','B3'=>'danger','Residu'=>'warning']; @endphp
                            <span class="badge bg-{{ $catColors[$r->kategori] ?? 'secondary' }}">{{ $r->kategori }}</span>
                        </td>
                        <td>{{ $r->jenis }}</td>
                        <td>{{ $r->officer }}</td>
                        <td class="text-end">{{ number_format($r->tonase, 2, ',', '.') }} kg</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data hasil pilahan.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr><td colspan="4" class="text-end">TOTAL ({{ number_format($totals->total_rows ?? 0, 0, ',', '.') }} Catatan)</td><td class="text-end">{{ number_format($totals->total_tonase ?? 0, 2, ',', '.') }} kg</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
    @if($rows->hasPages()) <div class="card-footer bg-white">{{ $rows->links() }}</div> @endif
</div>
@endsection
