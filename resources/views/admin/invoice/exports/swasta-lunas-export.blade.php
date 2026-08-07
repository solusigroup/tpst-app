<!DOCTYPE html>
<html>
<head>
    <title>Klien Swasta Lunas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .header { text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAP KLIEN SWASTA LUNAS - {{ $tab === 'invoices' ? 'DAFTAR INVOICE LUNAS' : 'DAFTAR KLIEN' }}</h2>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($tab === 'invoices')
    <table>
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>No. Invoice</th>
                <th>Nama Klien</th>
                <th class="text-center">Periode</th>
                <th class="text-end">Total Tagihan</th>
                <th class="text-end">Uang Muka</th>
                <th class="text-end">Sisa Tagihan</th>
                <th class="text-center">Tanggal Invoice</th>
                <th class="text-center">Tanggal Pelunasan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoiceList as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nomor_invoice ?? '-' }}</td>
                <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                <td class="text-center">{{ $item->periode_bulan }}/{{ $item->periode_tahun }}</td>
                <td class="text-end">{{ request('export') == 'excel' ? $item->total_tagihan : 'Rp ' . number_format($item->total_tagihan, 0, ',', '.') }}</td>
                <td class="text-end">{{ request('export') == 'excel' ? $item->uang_muka : 'Rp ' . number_format($item->uang_muka, 0, ',', '.') }}</td>
                <td class="text-end">{{ request('export') == 'excel' ? ($item->total_tagihan - $item->uang_muka) : 'Rp ' . number_format($item->total_tagihan - $item->uang_muka, 0, ',', '.') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_invoice)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $item->tanggal_pelunasan ? \Carbon\Carbon::parse($item->tanggal_pelunasan)->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <table>
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>Nama Klien</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th class="text-center">Invoice Lunas</th>
                <th class="text-end">Total Pembayaran Lunas</th>
                <th class="text-end">Sisa Piutang Aktif</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientList as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_klien }}</td>
                <td>{{ $item->kontak ?? '-' }}</td>
                <td>{{ $item->alamat ?? '-' }}</td>
                <td class="text-center">{{ $item->invoices_count }}</td>
                <td class="text-end">{{ request('export') == 'excel' ? $item->invoices_sum_total_tagihan : 'Rp ' . number_format($item->invoices_sum_total_tagihan, 0, ',', '.') }}</td>
                <td class="text-end">{{ request('export') == 'excel' ? $item->outstanding_piutang : 'Rp ' . number_format($item->outstanding_piutang, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
