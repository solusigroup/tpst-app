@extends('admin.laporan.exports.layout', ['title' => 'Laporan Pengangkutan Residu'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN PENGANGKUTAN RESIDU</h2>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th width="30">No</th>
            <th>No. Tiket</th>
            <th>Tanggal</th>
            <th>Armada</th>
            <th class="text-end">Bruto (Kg)</th>
            <th class="text-end">Tarra (Kg)</th>
            <th class="text-end">Netto (Kg)</th>
            <th class="text-end">Retribusi</th>
            <th>Tujuan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $row)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $row->nomor_tiket }}</td>
            <td>{{ $row->tanggal->format('d/m/Y') }}</td>
            <td>{{ $row->armada->plat_nomor }}</td>
            <td class="text-end">{{ number_format($row->berat_bruto, 0, ',', '.') }}</td>
            <td class="text-end">{{ number_format($row->berat_tarra, 0, ',', '.') }}</td>
            <td class="text-end fw-bold">{{ number_format($row->berat_netto, 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($row->biaya_retribusi, 0, ',', '.') }}</td>
            <td>{{ $row->tujuan }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold" style="background-color: #f8f9fa;">
        <tr>
            <td colspan="6" class="text-end">TOTAL</td>
            <td class="text-end">{{ number_format($totals->total_netto, 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($totals->total_biaya, 0, ',', '.') }}</td>
            <td></td>
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
