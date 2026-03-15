@extends('admin.laporan.exports.layout', ['title' => 'Laporan Arus Kas'])

@section('content')
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">LAPORAN ARUS KAS</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Metode Langsung</p>
    <p style="margin: 5px 0 0 0; color: #555;">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <tbody>
        <tr>
            <td colspan="2" style="border: 1px solid #ddd; padding: 5px; font-weight: bold;">Arus Kas dari Aktivitas Operasi</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px; padding-left: 20px;">Penerimaan Kas</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #198754;">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px; padding-left: 20px;">Pengeluaran Kas</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #dc3545;">(Rp {{ number_format($totalKasKeluar, 0, ',', '.') }})</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold;">Kas Bersih dari Aktivitas Operasi</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; font-weight: bold;">Rp {{ number_format($totalKasBersih, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<table style="width: 100%; border-collapse: collapse; border-top: 2px solid #333;">
    <tr>
        <td style="padding: 10px; font-weight: bold;">Saldo Kas Awal Periode</td>
        <td style="padding: 10px; text-align: right;">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td style="padding: 10px; font-weight: bold;">Kenaikan/(Penurunan) Kas Bersih</td>
        <td style="padding: 10px; text-align: right; {{ $totalKasBersih >= 0 ? 'color: #198754;' : 'color: #dc3545;' }}">Rp {{ number_format($totalKasBersih, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td style="padding: 10px; font-weight: bold; font-size: 14px;">SALDO KAS AKHIR PERIODE</td>
        <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 14px;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
    </tr>
</table>
@endsection
