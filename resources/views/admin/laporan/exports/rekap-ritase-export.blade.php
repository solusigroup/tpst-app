@extends('admin.laporan.exports.layout', ['title' => 'Rekap Ritase per Tanggal & Jenis Klien'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">REKAP RITASE PER TANGGAL & JENIS KLIEN</h2>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
    @if($jenisKlien)
        <p style="margin:2px 0; font-size:10px;">Filter Jenis Klien: {{ $jenisKlien }}</p>
    @endif
    @if($isApproved !== null && $isApproved !== '')
        <p style="margin:2px 0; font-size:10px;">Status Approval: {{ $isApproved == 1 ? 'Approved' : 'Not Approved' }}</p>
    @endif
</div>

{{-- Ringkasan per Jenis Klien --}}
<table class="table" style="width: 50%; margin-bottom: 20px;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th>Jenis Klien</th>
            <th class="text-center">Total Ritase</th>
            <th class="text-end">Netto (kg)</th>
            <th class="text-end">Biaya Tipping</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapPerJenis as $rj)
        <tr>
            <td>{{ $rj->jenis }}</td>
            <td class="text-center">{{ (request('export') == 'excel' ? ($rj->total_ritase) : number_format($rj->total_ritase, 0, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($rj->total_netto) : number_format($rj->total_netto, 2, ',', '.')) }}</td>
            <td class="text-end">{{ $rj->total_tipping }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold">
        <tr style="background-color: #f0f0f0;">
            <td>TOTAL</td>
            <td class="text-center">{{ (request('export') == 'excel' ? ($grandTotals->total_ritase ?? 0) : number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($grandTotals->total_netto ?? 0) : number_format($grandTotals->total_netto ?? 0, 2, ',', '.')) }}</td>
            <td class="text-end">{{ $grandTotals->total_tipping ?? 0 }}</td>
        </tr>
    </tfoot>
</table>

{{-- Pivot Harian --}}
<p class="fw-bold mb-1" style="font-size: 12px;">Rekap Harian per Jenis Klien</p>
<table class="table" style="font-size: 9px;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th rowspan="2" class="align-middle text-center">Tanggal</th>
            @foreach($jenisTypes as $jt)
            <th colspan="3" class="text-center" style="border-left: 2px solid #999;">{{ $jt }}</th>
            @endforeach
            <th colspan="3" class="text-center" style="border-left: 2px solid #999;">Total</th>
        </tr>
        <tr style="background-color: #f8f8f8;">
            @foreach($jenisTypes as $jt)
            <th class="text-center" style="border-left: 1px solid #ccc;">Rit</th>
            <th class="text-end">Netto</th>
            <th class="text-end">Tipping</th>
            @endforeach
            <th class="text-center" style="border-left: 2px solid #999;">Rit</th>
            <th class="text-end">Netto</th>
            <th class="text-end">Tipping</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pivotData as $row)
        <tr>
            <td class="text-center">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
            @foreach($jenisTypes as $jt)
            @php $cell = $row['jenis'][$jt] ?? null; @endphp
            <td class="text-center" style="border-left: 1px solid #ccc;">{{ $cell ? (request('export') == 'excel' ? ($cell['total_ritase']) : number_format($cell['total_ritase'], 0, ',', '.')) : '-' }}</td>
            <td class="text-end">{{ $cell ? (request('export') == 'excel' ? ($cell['total_netto']) : number_format($cell['total_netto'], 2, ',', '.')) : '-' }}</td>
            <td class="text-end">{{ $cell ? $cell['total_tipping'] : '-' }}</td>
            @endforeach
            <td class="text-center fw-bold" style="border-left: 2px solid #999;">{{ (request('export') == 'excel' ? ($row['total_ritase']) : number_format($row['total_ritase'], 0, ',', '.')) }}</td>
            <td class="text-end fw-bold">{{ (request('export') == 'excel' ? ($row['total_netto']) : number_format($row['total_netto'], 2, ',', '.')) }}</td>
            <td class="text-end fw-bold">{{ $row['total_tipping'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold">
        <tr style="background-color: #f0f0f0;">
            <td class="text-center">TOTAL</td>
            @foreach($jenisTypes as $jt)
            @php $jtRekap = $rekapPerJenis->firstWhere('jenis', $jt); @endphp
            <td class="text-center" style="border-left: 1px solid #ccc;">{{ $jtRekap ? (request('export') == 'excel' ? ($jtRekap->total_ritase) : number_format($jtRekap->total_ritase, 0, ',', '.')) : '-' }}</td>
            <td class="text-end">{{ $jtRekap ? (request('export') == 'excel' ? ($jtRekap->total_netto) : number_format($jtRekap->total_netto, 2, ',', '.')) : '-' }}</td>
            <td class="text-end">{{ $jtRekap ? $jtRekap->total_tipping : '-' }}</td>
            @endforeach
            <td class="text-center" style="border-left: 2px solid #999;">{{ (request('export') == 'excel' ? ($grandTotals->total_ritase ?? 0) : number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($grandTotals->total_netto ?? 0) : number_format($grandTotals->total_netto ?? 0, 2, ',', '.')) }}</td>
            <td class="text-end">{{ $grandTotals->total_tipping ?? 0 }}</td>
        </tr>
    </tfoot>
</table>

{{-- Detail per Klien --}}
<p class="fw-bold mb-1 mt-3" style="font-size: 12px;">Detail per Klien</p>
<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30" class="text-center">No</th>
            <th>Nama Klien</th>
            <th>Jenis</th>
            <th class="text-center">Ritase</th>
            <th class="text-end">Netto (kg)</th>
            <th class="text-end">Biaya Tipping</th>
            <th class="text-end">Avg Netto/Rit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapPerKlien as $index => $rk)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $rk->nama_klien }}</td>
            <td>{{ $rk->jenis }}</td>
            <td class="text-center">{{ (request('export') == 'excel' ? ($rk->total_ritase) : number_format($rk->total_ritase, 0, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($rk->total_netto) : number_format($rk->total_netto, 2, ',', '.')) }}</td>
            <td class="text-end">{{ $rk->total_tipping }}</td>
            <td class="text-end">{{ $rk->total_ritase > 0 ? (request('export') == 'excel' ? ($rk->total_netto / $rk->total_ritase) : number_format($rk->total_netto / $rk->total_ritase, 2, ',', '.')) : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold">
        <tr style="background-color: #f0f0f0;">
            <td colspan="3" class="text-end">TOTAL</td>
            <td class="text-center">{{ (request('export') == 'excel' ? ($grandTotals->total_ritase ?? 0) : number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($grandTotals->total_netto ?? 0) : number_format($grandTotals->total_netto ?? 0, 2, ',', '.')) }}</td>
            <td class="text-end">{{ $grandTotals->total_tipping ?? 0 }}</td>
            <td class="text-end">{{ ($grandTotals->total_ritase ?? 0) > 0 ? (request('export') == 'excel' ? (($grandTotals->total_netto ?? 0) : number_format(($grandTotals->total_netto ?? 0, 0, ',', '.')) / $grandTotals->total_ritase, 2, ',', '.') : '-' }}</td>
        </tr>
    </tfoot>
</table>
@endsection
