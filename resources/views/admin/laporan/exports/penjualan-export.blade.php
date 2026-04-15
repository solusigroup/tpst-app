@extends('admin.laporan.exports.layout', ['title' => 'Laporan Penjualan'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN PENJUALAN</h2>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>Klien</th>
            <th>Jenis Produk</th>
            <th class="text-end">Berat (kg)</th>
            <th class="text-end">Harga Satuan</th>
            <th class="text-end">Total Harga</th>
            <th>Status Invoice</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $r)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
            <td>{{ $r->klien->nama_klien ?? '-' }}</td>
            <td>{{ $r->jenis_produk }}</td>
            <td class="text-end">{{ number_format($r->berat_kg, 2, ',', '.') }}</td>
            <td class="text-end">{{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
            <td class="text-end">{{ number_format($r->total_harga, 0, ',', '.') }}</td>
            <td>{{ $r->status_invoice ?? 'Unbilled' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td colspan="4" class="text-end">TOTAL</td>
            <td class="text-end">{{ number_format($totals->total_berat ?? 0, 2, ',', '.') }}</td>
            <td></td>
            <td class="text-end">Rp {{ number_format($totals->total_harga ?? 0, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
@endsection
