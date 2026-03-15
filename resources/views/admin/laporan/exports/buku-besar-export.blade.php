@extends('admin.laporan.exports.layout', ['title' => 'Buku Besar'])

@section('content')
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">BUKU BESAR</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
    @if($coaId)
        <p style="margin: 5px 0 0 0; color: #555;">Akun: {{ $coas->where('id', $coaId)->first()->kode_akun ?? '' }} - {{ $coas->where('id', $coaId)->first()->nama_akun ?? '' }}</p>
    @else
        <p style="margin: 5px 0 0 0; color: #555;">Akun: Semua Akun</p>
    @endif
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px;">Tanggal</th>
            <th style="border: 1px solid #ddd; padding: 5px;">Kode Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px;">Nama Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px;">Keterangan</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Debit</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Kredit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $r)
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ $r->kode_akun }}</td>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ $r->nama_akun }}</td>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ $r->deskripsi }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">{{ $r->debit > 0 ? number_format($r->debit, 0, ',', '.') : '-' }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">{{ $r->kredit > 0 ? number_format($r->kredit, 0, ',', '.') : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
