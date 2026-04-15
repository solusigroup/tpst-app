@extends('admin.laporan.exports.layout', ['title' => 'Laporan Hasil Pilahan'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN HASIL PILAHAN</h2>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
</div>

<h3 style="margin-bottom: 10px;">Ringkasan Stok Pilahan</h3>
<table class="table" style="margin-bottom: 30px;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th>Kategori</th>
            <th>Jenis</th>
            <th class="text-end">Total Pilahan (kg)</th>
            <th class="text-end">Terjual (kg)</th>
            <th class="text-end">Sisa Stok (kg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stokSummary as $stok)
        <tr>
            <td>{{ $stok->kategori }}</td>
            <td>{{ $stok->jenis }}</td>
            <td class="text-end">{{ number_format($stok->total_pilahan, 2, ',', '.') }}</td>
            <td class="text-end">{{ number_format($stok->total_terjual, 2, ',', '.') }}</td>
            <td class="text-end fw-bold">{{ number_format($stok->sisa_stok, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td colspan="2" class="text-end">TOTAL KESELURUHAN</td>
            <td class="text-end">{{ number_format($summaryTotals->total_pilahan, 2, ',', '.') }}</td>
            <td class="text-end">{{ number_format($summaryTotals->total_terjual, 2, ',', '.') }}</td>
            <td class="text-end">{{ number_format($summaryTotals->sisa_stok, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<h3 style="margin-bottom: 10px;">Riwayat Log Harian</h3>
<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Jenis</th>
            <th>Petugas</th>
            <th class="text-end">Tonase (kg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $r)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
            <td>{{ $r->kategori }}</td>
            <td>{{ $r->jenis }}</td>
            <td>{{ $r->officer }}</td>
            <td class="text-end">{{ number_format($r->tonase, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td colspan="5" class="text-end">TOTAL LOG</td>
            <td class="text-end">{{ number_format($totals->total_tonase ?? 0, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
@endsection
