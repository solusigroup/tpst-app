@extends('admin.laporan.exports.layout', ['title' => 'Posisi Keuangan'])

@section('content')
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">POSISI KEUANGAN</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Per Tanggal: {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
    @if(isset($penyajian) && $penyajian == 'komparatif')
    <p style="margin: 2px 0 0 0; color: #777;">Pembanding: {{ \Carbon\Carbon::parse($sampaiPembanding)->format('d M Y') }}</p>
    @endif
</div>

<table style="width: 100%; border-collapse: collapse; border: none;">
    <tr>
        <td style="width: 50%; vertical-align: top; padding-right: 10px; border: none;">
            <h3 style="font-size: 14px; margin-bottom: 5px; color: #fff; background-color: #0d6efd; padding: 5px;">ASET</h3>
            
            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Aset Lancar</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: left; background-color: #f8f9fa;">Akun</th>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Berjalan</th>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Pembanding</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($asetLancar as $item)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($item->saldo) : number_format($item->saldo, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($item->saldo_pembanding) : number_format($item->saldo_pembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Aset Lancar</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($totalAsetLancar) : number_format($totalAsetLancar, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($totalAsetLancarPembanding) : number_format($totalAsetLancarPembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>

            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Aset Tidak Lancar</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: left; background-color: #f8f9fa;">Akun</th>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Berjalan</th>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Pembanding</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($asetTidakLancar as $item)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($item->saldo) : number_format($item->saldo, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($item->saldo_pembanding) : number_format($item->saldo_pembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Aset Tidak Lancar</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($totalAsetTidakLancar) : number_format($totalAsetTidakLancar, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($totalAsetTidakLancarPembanding) : number_format($totalAsetTidakLancarPembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>

            <table style="width: 100%; border-collapse: collapse; border-top: 2px solid #333; margin-top: 15px;">
                <tr>
                    <td style="font-weight: bold; font-size: 13px; padding-top: 5px;">TOTAL ASET</td>
                    <td style="font-weight: bold; font-size: 13px; text-align: right; padding-top: 5px;">Rp {{ (request('export') == 'excel' ? ($totalAset) : number_format($totalAset, 0, ',', '.')) }}</td>
                    @if(isset($penyajian) && $penyajian == 'komparatif')
                    <td style="font-weight: bold; font-size: 13px; text-align: right; padding-top: 5px; color: #555;">Rp {{ (request('export') == 'excel' ? ($totalAsetPembanding) : number_format($totalAsetPembanding, 0, ',', '.')) }}</td>
                    @endif
                </tr>
            </table>
        </td>

        <td style="width: 50%; vertical-align: top; padding-left: 10px; border: none;">
            <h3 style="font-size: 14px; margin-bottom: 5px; color: #000; background-color: #ffc107; padding: 5px;">LIABILITAS &amp; EKUITAS</h3>
            
            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Liabilitas Jangka Pendek</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: left; background-color: #f8f9fa;">Akun</th>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Berjalan</th>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Pembanding</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($liabilitasJP as $item)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($item->saldo) : number_format($item->saldo, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($item->saldo_pembanding) : number_format($item->saldo_pembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Liabilitas JP</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($totalLiabilitasJP) : number_format($totalLiabilitasJP, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($totalLiabilitasJPPembanding) : number_format($totalLiabilitasJPPembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>

            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Liabilitas Jangka Panjang</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: left; background-color: #f8f9fa;">Akun</th>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Berjalan</th>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Pembanding</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($liabilitasJPj as $item)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($item->saldo) : number_format($item->saldo, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($item->saldo_pembanding) : number_format($item->saldo_pembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Liabilitas JPj</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($totalLiabilitasJPj) : number_format($totalLiabilitasJPj, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($totalLiabilitasJPjPembanding) : number_format($totalLiabilitasJPjPembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>

            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Ekuitas</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: left; background-color: #f8f9fa;">Akun</th>
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Berjalan</th>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <th style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; background-color: #f8f9fa;">Pembanding</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($ekuitas as $item)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($item->saldo) : number_format($item->saldo, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($item->saldo_pembanding) : number_format($item->saldo_pembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                    @endforeach
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">Laba/Rugi Berjalan</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($labaRugi ?? 0) : number_format($labaRugi ?? 0, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($labaRugiPembanding ?? 0) : number_format($labaRugiPembanding ?? 0, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Ekuitas</td>
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ (request('export') == 'excel' ? ($totalEkuitas) : number_format($totalEkuitas, 0, ',', '.')) }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')
                        <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right; color: #555;">{{ (request('export') == 'excel' ? ($totalEkuitasPembanding) : number_format($totalEkuitasPembanding, 0, ',', '.')) }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>

            <table style="width: 100%; border-collapse: collapse; border-top: 2px solid #333; margin-top: 15px;">
                <tr>
                    <td style="font-weight: bold; font-size: 13px; padding-top: 5px;">TOTAL LIA + EKUITAS</td>
                    <td style="font-weight: bold; font-size: 13px; text-align: right; padding-top: 5px; {{ abs($totalAset - $totalLiabilitasEkuitas) < 0.01 ? 'color: #198754;' : 'color: #dc3545;' }}">
                        Rp {{ (request('export') == 'excel' ? ($totalLiabilitasEkuitas) : number_format($totalLiabilitasEkuitas, 0, ',', '.')) }}
                    </td>
                    @if(isset($penyajian) && $penyajian == 'komparatif')
                    <td style="font-weight: bold; font-size: 13px; text-align: right; padding-top: 5px; color: #555;">
                        Rp {{ (request('export') == 'excel' ? ($totalLiabilitasEkuitasPembanding) : number_format($totalLiabilitasEkuitasPembanding, 0, ',', '.')) }}
                    </td>
                    @endif
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
