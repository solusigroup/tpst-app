<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penjualan Offtaker Per Inv</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #999; padding: 4px 6px; }
        .table th { background-color: #f2f4f8; font-weight: bold; text-align: center; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .uppercase { text-transform: uppercase; }
        .header-title { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .row-offtaker { background-color: #e2e8f0; font-weight: bold; }
        .row-invoice { background-color: #f1f5f9; font-weight: bold; }
        .row-subtotal { background-color: #f8fafc; font-weight: bold; }
        .row-grandtotal { background-color: #1e293b; color: #ffffff; font-weight: bold; }
        .row-grandtotal td { border-color: #1e293b; }
        .text-muted { color: #64748b; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header-title">
        <h2 class="fw-bold uppercase mb-1" style="margin:0;">LAPORAN PENJUALAN HASIL PILAHAN PER OFFTAKER PER INVOICE</h2>
        <p style="margin: 4px 0 0 0; font-size: 8.5pt;">
            Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}
        </p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Offtaker / Invoice / Item Pilahan</th>
                <th style="width: 85px;">Tgl Transaksi</th>
                <th style="width: 85px;" class="text-end">Berat (kg)</th>
                <th style="width: 95px;" class="text-end">Harga Satuan</th>
                <th style="width: 105px;" class="text-end">Subtotal (Rp)</th>
                <th style="width: 100px;" class="text-end">Terbayar (Rp)</th>
                <th style="width: 80px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $globalNo = 1; @endphp
            @forelse($reports as $rep)
                <tr class="row-offtaker">
                    <td class="text-center">{{ $globalNo++ }}</td>
                    <td colspan="7">
                        OFFTAKER: {{ strtoupper($rep->klien->nama_klien) }}
                        @if($rep->klien->alamat) <span style="font-weight: normal; color: #475569;">({{ $rep->klien->alamat }})</span> @endif
                    </td>
                </tr>
                @foreach($rep->invoices as $inv)
                    <tr class="row-invoice">
                        <td></td>
                        <td colspan="4" style="padding-left: 15px;">
                            @if($inv->is_uninvoiced)
                                [Pending] Item Belum Di-invoice (Draft)
                            @else
                                [Invoice] {{ $inv->invoice->nomor_invoice }} (Tgl: {{ \Carbon\Carbon::parse($inv->invoice->tanggal_invoice)->format('d/m/Y') }}){{ $inv->total_uang_muka > 0 ? ' - DP: Rp ' . number_format($inv->total_uang_muka, 0, ',', '.') : '' }}
                            @endif
                        </td>
                        <td class="text-end fw-bold">
                            {{ request('export') == 'excel' ? $inv->total_nominal : number_format($inv->total_nominal, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold">
                            {{ request('export') == 'excel' ? $inv->total_terbayar : number_format($inv->total_terbayar, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            {{ $inv->is_uninvoiced ? 'Pending' : $inv->invoice->status }}
                        </td>
                    </tr>
                    @foreach($inv->items as $item)
                        <tr>
                            <td></td>
                            <td style="padding-left: 30px;">↳ {{ $item->jenis_produk }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="text-end">{{ request('export') == 'excel' ? $item->berat_kg : number_format($item->berat_kg, 2, ',', '.') }}</td>
                            <td class="text-end">{{ request('export') == 'excel' ? $item->harga_satuan : number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-end">{{ request('export') == 'excel' ? $item->total_harga : number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td class="text-end">{{ request('export') == 'excel' ? $item->jumlah_bayar : ($item->jumlah_bayar > 0 ? number_format($item->jumlah_bayar, 0, ',', '.') : '-') }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                @endforeach
                <tr class="row-subtotal">
                    <td colspan="3" class="text-end uppercase">Subtotal {{ $rep->klien->nama_klien }}:</td>
                    <td class="text-end">{{ request('export') == 'excel' ? $rep->total_berat : number_format($rep->total_berat, 2, ',', '.') }}</td>
                    <td></td>
                    <td class="text-end">{{ request('export') == 'excel' ? $rep->total_nominal : number_format($rep->total_nominal, 0, ',', '.') }}</td>
                    <td class="text-end">{{ request('export') == 'excel' ? $rep->total_terbayar : number_format($rep->total_terbayar, 0, ',', '.') }}</td>
                    <td class="text-center">Sisa: {{ request('export') == 'excel' ? $rep->total_sisa : number_format($rep->total_sisa, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data penjualan pada periode terpilih.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="row-grandtotal">
                <td colspan="3" class="text-end uppercase">GRAND TOTAL KESELURUHAN:</td>
                <td class="text-end">{{ request('export') == 'excel' ? $summary->total_berat_kg : number_format($summary->total_berat_kg, 2, ',', '.') }}</td>
                <td></td>
                <td class="text-end">{{ request('export') == 'excel' ? $summary->total_omzet : number_format($summary->total_omzet, 0, ',', '.') }}</td>
                <td class="text-end">{{ request('export') == 'excel' ? $summary->total_terbayar : number_format($summary->total_terbayar, 0, ',', '.') }}</td>
                <td class="text-center">Sisa: {{ request('export') == 'excel' ? $summary->total_sisa : number_format($summary->total_sisa, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <table style="width: 100%; margin-top: 30px; border: none;">
        <tr style="border: none;">
            <td style="width: 65%; border: none;"></td>
            <td style="width: 35%; text-align: center; border: none;">
                <p style="margin-bottom: 50px;">Lamongan, {{ now()->translatedFormat('d F Y') }}<br>Dicetak Oleh,</p>
                <p style="margin-bottom: 0; font-weight: bold;"><u>{{ auth()->user()->name ?? 'Administrator' }}</u></p>
                <p style="margin: 0; font-size: 8pt; color: #64748b;">Bagian Operasional / Kasir Penjualan</p>
            </td>
        </tr>
    </table>
</body>
</html>
