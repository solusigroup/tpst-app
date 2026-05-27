@extends('admin.laporan.exports.layout', ['title' => 'Laporan Laba Rugi'])

@section('content')
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">LAPORAN LABA RUGI</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
    @if(isset($penyajian) && $penyajian == 'komparatif')
    <p style="margin: 2px 0 0 0; color: #777;">Pembanding: {{ \Carbon\Carbon::parse($dariPembanding)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampaiPembanding)->format('d M Y') }}</p>
    @endif
</div>

<h3 style="font-size: 13px; color: #198754; margin-bottom: 5px;">PENDAPATAN</h3>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left; background-color: #f8f9fa;">Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right; background-color: #f8f9fa;">Periode Berjalan</th>
            @if(isset($penyajian) && $penyajian == 'komparatif')
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right; background-color: #f8f9fa;">Pembanding</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($pendapatan as $item)
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">Rp {{ (request('export') == 'excel' ? ($item->saldo) : number_format($item->saldo, 0, ',', '.')) }}</td>
            @if(isset($penyajian) && $penyajian == 'komparatif')
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #555;">Rp {{ (request('export') == 'excel' ? ($item->saldo_pembanding) : number_format($item->saldo_pembanding, 0, ',', '.')) }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold;">Total Pendapatan</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #198754; font-weight: bold;">Rp {{ (request('export') == 'excel' ? ($totalPendapatan) : number_format($totalPendapatan, 0, ',', '.')) }}</td>
            @if(isset($penyajian) && $penyajian == 'komparatif')
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #555; font-weight: bold;">Rp {{ (request('export') == 'excel' ? ($totalPendapatanPembanding) : number_format($totalPendapatanPembanding, 0, ',', '.')) }}</td>
            @endif
        </tr>
    </tfoot>
</table>

<h3 style="font-size: 13px; color: #dc3545; margin-bottom: 5px;">BEBAN</h3>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left; background-color: #f8f9fa;">Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right; background-color: #f8f9fa;">Periode Berjalan</th>
            @if(isset($penyajian) && $penyajian == 'komparatif')
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right; background-color: #f8f9fa;">Pembanding</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($beban as $item)
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">Rp {{ (request('export') == 'excel' ? ($item->saldo) : number_format($item->saldo, 0, ',', '.')) }}</td>
            @if(isset($penyajian) && $penyajian == 'komparatif')
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #555;">Rp {{ (request('export') == 'excel' ? ($item->saldo_pembanding) : number_format($item->saldo_pembanding, 0, ',', '.')) }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold;">Total Beban</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #dc3545; font-weight: bold;">Rp {{ (request('export') == 'excel' ? ($totalBeban) : number_format($totalBeban, 0, ',', '.')) }}</td>
            @if(isset($penyajian) && $penyajian == 'komparatif')
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #555; font-weight: bold;">Rp {{ (request('export') == 'excel' ? ($totalBebanPembanding) : number_format($totalBebanPembanding, 0, ',', '.')) }}</td>
            @endif
        </tr>
    </tfoot>
</table>

<table style="width: 100%; border-collapse: collapse; border-top: 2px solid #333; margin-top: 20px;">
    <tr>
        <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold; font-size: 14px;">LABA/RUGI BERSIH</td>
        <td style="border: 1px solid #ddd; padding: 10px; text-align: right; font-weight: bold; font-size: 14px; {{ $labaRugiBersih >= 0 ? 'color: #198754;' : 'color: #dc3545;' }}">
            Rp {{ (request('export') == 'excel' ? ($labaRugiBersih) : number_format($labaRugiBersih, 0, ',', '.')) }}
        </td>
        @if(isset($penyajian) && $penyajian == 'komparatif')
        <td style="border: 1px solid #ddd; padding: 10px; text-align: right; font-weight: bold; font-size: 14px; color: #555;">
            Rp {{ (request('export') == 'excel' ? ($labaRugiBersihPembanding) : number_format($labaRugiBersihPembanding, 0, ',', '.')) }}
        </td>
        @endif
    </tr>
</table>
@endsection
