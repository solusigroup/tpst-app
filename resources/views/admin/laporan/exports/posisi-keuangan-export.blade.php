@extends('admin.laporan.exports.layout', ['title' => 'Posisi Keuangan'])

@section('content')
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">POSISI KEUANGAN</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Per Tanggal: {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
</div>

<table style="width: 100%; border-collapse: collapse; border: none;">
    <tr>
        <td style="width: 50%; vertical-align: top; padding-right: 10px; border: none;">
            <h3 style="font-size: 14px; margin-bottom: 5px; color: #fff; background-color: #0d6efd; padding: 5px;">ASET</h3>
            
            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Aset Lancar</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                @foreach($asetLancar as $item)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Aset Lancar</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ number_format($totalAsetLancar, 0, ',', '.') }}</td>
                </tr>
            </table>

            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Aset Tidak Lancar</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                @foreach($asetTidakLancar as $item)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Aset Tidak Lancar</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ number_format($totalAsetTidakLancar, 0, ',', '.') }}</td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; border-top: 2px solid #333; margin-top: 15px;">
                <tr>
                    <td style="font-weight: bold; font-size: 13px; padding-top: 5px;">TOTAL ASET</td>
                    <td style="font-weight: bold; font-size: 13px; text-align: right; padding-top: 5px;">Rp {{ number_format($totalAset, 0, ',', '.') }}</td>
                </tr>
            </table>
        </td>

        <td style="width: 50%; vertical-align: top; padding-left: 10px; border: none;">
            <h3 style="font-size: 14px; margin-bottom: 5px; color: #000; background-color: #ffc107; padding: 5px;">LIABILITAS &amp; EKUITAS</h3>
            
            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Liabilitas Jangka Pendek</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                @foreach($liabilitasJP as $item)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Liabilitas JP</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ number_format($totalLiabilitasJP, 0, ',', '.') }}</td>
                </tr>
            </table>

            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Liabilitas Jangka Panjang</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                @foreach($liabilitasJPj as $item)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Liabilitas JPj</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ number_format($totalLiabilitasJPj, 0, ',', '.') }}</td>
                </tr>
            </table>

            <h4 style="font-size: 12px; margin-bottom: 5px; color: #555;">Ekuitas</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                @foreach($ekuitas as $item)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px;">Laba/Rugi Berjalan</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-size: 11px; text-align: right;">{{ number_format($labaRugi ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px;">Total Ekuitas</td>
                    <td style="border: 1px solid #ddd; padding: 3px; font-weight: bold; font-size: 11px; text-align: right;">{{ number_format($totalEkuitas, 0, ',', '.') }}</td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; border-top: 2px solid #333; margin-top: 15px;">
                <tr>
                    <td style="font-weight: bold; font-size: 13px; padding-top: 5px;">TOTAL LIA + EKUITAS</td>
                    <td style="font-weight: bold; font-size: 13px; text-align: right; padding-top: 5px; {{ abs($totalAset - $totalLiabilitasEkuitas) < 0.01 ? 'color: #198754;' : 'color: #dc3545;' }}">
                        Rp {{ number_format($totalLiabilitasEkuitas, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
