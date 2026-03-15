@extends('admin.laporan.exports.layout', ['title' => 'Perubahan Ekuitas'])

@section('content')
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">LAPORAN PERUBAHAN EKUITAS</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Akun Ekuitas</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Saldo Awal</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Penambahan</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Pengurangan</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $r)
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ $r['kode_akun'] }} - {{ $r['nama_akun'] }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">{{ number_format($r['saldoAwal'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #198754;">{{ number_format($r['penambahan'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #dc3545;">{{ number_format($r['pengurangan'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; font-weight: bold;">{{ number_format($r['saldoAkhir'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr style="background-color: #f8f9fa;">
            <td style="border: 1px solid #ddd; padding: 5px;">Laba / (Rugi) Bersih Periode Berjalan</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">-</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; {{ $labaRugi >= 0 ? 'color: #198754;' : '' }}">{{ $labaRugi >= 0 ? number_format($labaRugi, 0, ',', '.') : '-' }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; {{ $labaRugi < 0 ? 'color: #dc3545;' : '' }}">{{ $labaRugi < 0 ? number_format(abs($labaRugi), 0, ',', '.') : '-' }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; font-weight: bold; {{ $labaRugi >= 0 ? 'color: #198754;' : 'color: #dc3545;' }}">{{ number_format($labaRugi, 0, ',', '.') }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold;">TOTAL EKUITAS</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">Rp {{ number_format($totalSaldoAwal, 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right; color: #198754;">Rp {{ number_format($totalPenambahan + ($labaRugi >= 0 ? $labaRugi : 0), 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right; color: #dc3545;">Rp {{ number_format($totalPengurangan + ($labaRugi < 0 ? abs($labaRugi) : 0), 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">Rp {{ number_format($totalSaldoAkhir, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
@endsection
