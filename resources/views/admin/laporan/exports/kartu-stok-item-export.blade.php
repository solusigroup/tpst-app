@extends('admin.laporan.exports.layout', ['title' => 'Kartu Stok Item - ' . ($jenisItem ?? '')])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">KARTU STOK BARANG (HISTORI ITEM)</h2>
    <p style="margin:5px 0">Jenis Barang: <strong>{{ $jenisItem }}</strong></p>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th width="30" class="text-center">No</th>
            <th>Tanggal</th>
            <th>Jenis Mutasi</th>
            <th>Keterangan</th>
            <th class="text-end">Masuk (Kg)</th>
            <th class="text-end">Keluar (Kg)</th>
            <th class="text-end">Saldo (Kg)</th>
        </tr>
    </thead>
    <tbody>
        <tr style="background-color: #e9ecef;">
            <td colspan="4" class="text-end fw-bold">SALDO AWAL SEBELUM {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }}</td>
            <td></td>
            <td></td>
            <td class="text-end fw-bold">{{ request('export') == 'excel' ? $saldoAwal : number_format($saldoAwal, 2, ',', '.') }}</td>
        </tr>

        @php 
            $runningBalance = $saldoAwal; 
            $totalMasuk = 0;
            $totalKeluar = 0;
        @endphp

        @foreach($mutasi as $index => $row)
            @php 
                $masuk = $row->jumlah_masuk;
                $keluar = $row->jumlah_keluar;
                $runningBalance = $runningBalance + $masuk - $keluar;
                
                $totalMasuk += $masuk;
                $totalKeluar += $keluar;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $row->tipe }}</td>
                <td>{{ $row->keterangan }}</td>
                <td class="text-end">{{ $masuk > 0 ? (request('export') == 'excel' ? $masuk : number_format($masuk, 2, ',', '.')) : '-' }}</td>
                <td class="text-end">{{ $keluar > 0 ? (request('export') == 'excel' ? $keluar : number_format($keluar, 2, ',', '.')) : '-' }}</td>
                <td class="text-end fw-bold">{{ request('export') == 'excel' ? $runningBalance : number_format($runningBalance, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold" style="background-color: #f8f9fa;">
        <tr>
            <td colspan="4" class="text-end">TOTAL MUTASI PERIODE INI</td>
            <td class="text-end text-success">{{ request('export') == 'excel' ? $totalMasuk : number_format($totalMasuk, 2, ',', '.') }}</td>
            <td class="text-end text-danger">{{ request('export') == 'excel' ? $totalKeluar : number_format($totalKeluar, 2, ',', '.') }}</td>
            <td class="text-end">{{ request('export') == 'excel' ? $runningBalance : number_format($runningBalance, 2, ',', '.') }}</td>
        </tr>
        <tr style="background-color: #cfe2ff;">
            <td colspan="4" class="text-end">SALDO AKHIR (SISA STOK)</td>
            <td colspan="2"></td>
            <td class="text-end" style="font-size: 13px;">{{ request('export') == 'excel' ? $runningBalance : number_format($runningBalance, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<div style="margin-top: 30px;">
    <table class="table-borderless" style="width: 100%;">
        <tr>
            <td width="70%"></td>
            <td class="text-center">
                <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
                <div style="margin-top: 60px;">
                    <p><b>( ____________________ )</b></p>
                    <p>&nbsp;</p>
                </div>
            </td>
        </tr>
    </table>
</div>
@endsection
