<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan Per Klien</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .table th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .mb-4 { margin-bottom: 20px; }
        .uppercase { text-transform: uppercase; }
        .border-bottom { border-bottom: 2px solid #000; }
        .d-flex { display: block; width: 100%; }
        .float-right { float: right; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <h2 class="fw-bold uppercase mb-1">LAPORAN PENJUALAN PER KLIEN</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
    </div>

    @foreach($reports as $report)
    <div style="page-break-inside: avoid; margin-bottom: 30px;">
        <div class="border-bottom clearfix" style="padding-bottom: 5px; margin-bottom: 10px;">
            <div style="float: left;">
                <h3 style="margin: 0;">{{ $report->klien->nama_klien }}</h3>
                <p style="margin: 0; font-size: 8pt; color: #666;">{{ $report->klien->alamat }}</p>
            </div>
            <div style="float: right; text-align: right;">
                <p style="margin: 0;"><strong>Frekuensi: {{ $report->frequency }}x</strong></p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Tanggal</th>
                    <th>Jenis Produk</th>
                    <th class="text-end">Berat (kg)</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $item->jenis_produk }}</td>
                    <td class="text-end">{{ (request('export') == 'excel' ? ($item->berat_kg) : number_format($item->berat_kg, 2, ',', '.')) }}</td>
                    <td class="text-end">{{ (request('export') == 'excel' ? ($item->harga_satuan) : number_format($item->harga_satuan, 0, ',', '.')) }}</td>
                    <td class="text-end fw-bold">{{ (request('export') == 'excel' ? ($item->total_harga) : number_format($item->total_harga, 0, ',', '.')) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="3" class="text-end uppercase" style="font-size: 8pt;">Total Per Klien</td>
                    <td class="text-end">{{ (request('export') == 'excel' ? ($report->total_berat) : number_format($report->total_berat, 2, ',', '.')) }} kg</td>
                    <td></td>
                    <td class="text-end">Rp {{ (request('export') == 'excel' ? ($report->total_nominal) : number_format($report->total_nominal, 0, ',', '.')) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach

    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        <br><br><br>
        <p class="fw-bold">( ____________________ )</p>
        <p style="font-size: 8pt; color: #666;">Admin Penjualan</p>
    </div>
</body>
</html>
