<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Ritase II</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        @if(isset($isExport) && $isExport)
        /* Styling khusus untuk Excel export agar border muncul sesuai di gambar */
        th, td { padding: 4px; border: 1px solid #000000; }
        .no-border { border: none !important; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        @else
        /* Styling khusus untuk PDF export */
        th, td { border: 1px solid #ddd; padding: 6px; }
        .font-bold { font-weight: bold; }
        @endif
    </style>
</head>
<body>

<table>
    <tr>
        <td class="font-bold">KLIEN</td>
        <td colspan="2">{{ $klien ? $klien->nama_klien : 'Semua Klien' }}</td>
    </tr>
    <tr>
        <td class="font-bold">JENIS KLIEN</td>
        <td colspan="2">{{ $klien ? $klien->jenis : ($jenisKlien ?: 'Semua Jenis') }}</td>
    </tr>
    <tr>
        <td class="font-bold">BULAN</td>
        <td>{{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }}</td>
        <td class="font-bold">Tahun: {{ $tahun }}</td>
    </tr>
    @if($isApproved !== null && $isApproved !== '')
    <tr>
        <td class="font-bold">STATUS APPROVAL</td>
        <td colspan="2">{{ $isApproved == 1 ? 'Approved' : 'Not Approved' }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="3" style="border: none !important;"></td>
    </tr>
    <tr>
        <td class="font-bold">Tanggal</td>
        <td class="font-bold">Jml Bongkar/Ritase</td>
        <td class="font-bold">Jml Tonase</td>
    </tr>
    @foreach($rekapHarian as $row)
    <tr>
        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
        <td>{{ $row->total_ritase }}</td>
        <td>{{ $row->total_netto }}</td>
    </tr>
    @endforeach
    <tr>
        <td class="font-bold">Grand Total</td>
        <td class="font-bold">{{ $grandTotalRitase }}</td>
        <td class="font-bold">{{ $grandTotalNetto }}</td>
    </tr>
    @if($rekapHarian->count() > 0)
    <tr>
        <td class="font-bold">Rata-rata Tonase</td>
        <td class="font-bold">-</td>
        <td class="font-bold">{{ (isset($isExport) && $isExport) ? ($grandTotalNetto / $rekapHarian->count()) : number_format($grandTotalNetto / $rekapHarian->count(), 2, ',', '.') }}</td>
    </tr>
    @endif
</table>

</body>
</html>
