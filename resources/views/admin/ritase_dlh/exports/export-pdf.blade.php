<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Ritase DLH — {{ $label }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
        }

        .header h3 {
            margin: 0 0 4px 0;
            font-size: 13px;
            font-weight: normal;
        }

        .header p {
            margin: 0;
            color: #666;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 4px 6px;
            text-align: left;
        }

        th {
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 9px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            width: 350px;
            float: right;
            margin-bottom: 20px;
        }

        .summary-box table {
            margin-bottom: 0;
        }

        .summary-box th,
        .summary-box td {
            border: none;
            padding: 3px 6px;
        }

        .clear {
            clear: both;
        }

        /* ──── Tanda Tangan ──── */
        .signature-section {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border: none;
        }

        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 0 15px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #333;
            margin-left: 20px;
            margin-right: 20px;
        }

        .signature-name {
            font-size: 10px;
            margin-top: 4px;
        }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            text-transform: uppercase;
        }

        .badge-paid {
            background-color: #198754;
            color: white;
        }

        .badge-draft {
            background-color: #6c757d;
            color: white;
        }

        .badge-sent {
            background-color: #0dcaf0;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Ritase DLH — {{ $label }}</h2>
        <h3>Dinas Lingkungan Hidup — Tarif Rp 80.000 per Ton</h3>
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
        @if(request('search'))
            <p>Pencarian: "{{ request('search') }}"</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="10%">No. Tiket</th>
                <th width="9%">Tanggal</th>
                <th width="13%">Klien</th>
                <th width="8%">Armada</th>
                <th width="9%">Asal Sampah</th>
                <th width="8%" class="text-right">Bruto (kg)</th>
                <th width="8%" class="text-right">Tarra (kg)</th>
                <th width="8%" class="text-right">Netto (kg)</th>
                <th width="8%" class="text-right">Netto (ton)</th>
                <th width="10%" class="text-right">Biaya Tipping</th>
                <th width="6%" class="text-center">Invoice</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ritase as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->nomor_tiket ?? '-' }}</strong></td>
                    <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                    <td>{{ $item->armada->plat_nomor ?? '-' }}</td>
                    <td>{{ $item->jenis_sampah ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->berat_bruto, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->berat_tarra, 2, ',', '.') }}</td>
                    <td class="text-right fw-bold">{{ number_format($item->berat_netto, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->berat_netto / 1000, 4, ',', '.') }}</td>
                    <td class="text-right fw-bold">Rp {{ number_format($item->biaya_tipping, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($item->status_invoice === 'Paid')
                            <span class="badge badge-paid">Lunas</span>
                        @elseif($item->status_invoice === 'Sent')
                            <span class="badge badge-sent">Sent</span>
                        @else
                            <span class="badge badge-draft">{{ $item->status_invoice ?? '-' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data ritase DLH pada periode dan filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
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
            <tr>
                <th>Total Berat (Ton)</th>
                <td class="text-right">: <strong>{{ number_format($totalBeratNetto / 1000, 2, ',', '.') }} ton</strong></td>
            </tr>
            <tr>
                <th>Tarif per Ton</th>
                <td class="text-right">: Rp 80.000</td>
            </tr>
            <tr>
                <th>Total Biaya Tipping</th>
                <td class="text-right">: <strong>Rp {{ number_format($totalBiayaTipping, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    {{-- ══════ TANDA TANGAN (3 KOLOM) ══════ --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-label">Dinas Lingkungan Hidup</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">
                        (.................................)<br>
                        <small>Pejabat DLH</small>
                    </div>
                </td>
                <td>
                    <div class="signature-label">Manajer</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">
                        (.................................)<br>
                        <small>Manajer TPST</small>
                    </div>
                </td>
                <td>
                    <div class="signature-label">Admin</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">
                        (.................................)<br>
                        <small>Administrator</small>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
