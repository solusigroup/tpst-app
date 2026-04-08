<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Ritase</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }

        .header p {
            margin: 0;
            color: #666;
        }

        .info-section {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            text-transform: uppercase;
        }

        .badge-masuk {
            background-color: #17a2b8;
            color: white;
        }

        .badge-timbang {
            background-color: #ffc107;
            color: #000;
        }

        .badge-keluar {
            background-color: #0d6efd;
            color: white;
        }

        .badge-selesai {
            background-color: #198754;
            color: white;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            width: 300px;
            float: right;
        }

        .summary-box table {
            margin-bottom: 0;
        }

        .summary-box th,
        .summary-box td {
            border: none;
            padding: 4px;
        }

        .footer {
            margin-top: 50px;
            clear: both;
        }

        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #333;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Rekapitulasi Ritase</h2>
        <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
        @if(request('start_date') || request('end_date'))
            <p>Periode:
                {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') : 'Awal' }}
                s/d
                {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y') : 'Sekarang' }}
            </p>
        @else
            <p>Periode: Semua Waktu</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">No. Tiket</th>
                <th width="10%">Tiket</th>
                <th width="12%">Waktu Masuk</th>
                <th width="12%">Asal Sampah</th>
                <th width="13%">Armada</th>
                <th width="15%">Klien</th>
                <th width="10%">Status</th>
                <th width="13%" class="text-right">Berat Netto (kg)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ritase as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->nomor_tiket ?? '-' }}</strong></td>
                    <td>{{ $item->tiket ?? '-' }}</td>
                    <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $item->jenis_sampah ?? '-' }}</td>
                    <td>{{ $item->armada->plat_nomor ?? '-' }}</td>
                    <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $item->status ?? 'default' }}">{{ ucfirst($item->status) }}</span>
                    </td>
                    <td class="text-right">{{ number_format($item->berat_netto, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data ritase pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <th>Total Ritase</th>
                <td class="text-right">: {{ $ritase->count() }} unit</td>
            </tr>
            <tr>
                <th>Total Berat Netto</th>
                <td class="text-right">: <strong>{{ number_format($totalBeratNetto, 2, ',', '.') }} kg</strong></td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <div class="signature-line"></div>
            <p><strong>{{ auth()->user()->name }}</strong><br>Petugas</p>
        </div>
    </div>
</body>

</html>