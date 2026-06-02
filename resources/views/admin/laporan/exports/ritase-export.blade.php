@extends('admin.laporan.exports.layout', ['title' => 'Laporan Ritase'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN RITASE</h2>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
</div>

@if(isset($rekapJenis) && count($rekapJenis) > 0)
<table class="table" style="width: 50%; margin-bottom: 20px;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th>Jenis Armada</th>
            <th class="text-center">Ritase</th>
            <th class="text-end">Tonase (kg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapJenis as $rj)
        <tr>
            <td>{{ $rj->jenis_armada ?? 'N/A' }}</td>
            <td class="text-center">{{ (request('export') == 'excel' ? ($rj->total_ritase) : number_format($rj->total_ritase, 0, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($rj->total_netto) : number_format($rj->total_netto, 2, ',', '.')) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td>TOTAL REKAP</td>
            <td class="text-center">{{ (request('export') == 'excel' ? $rekapJenis->sum('total_ritase') : number_format($rekapJenis->sum('total_ritase'), 0, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? $rekapJenis->sum('total_netto') : number_format($rekapJenis->sum('total_netto'), 2, ',', '.')) }}</td>
        </tr>
    </tfoot>
</table>
@endif

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>No Tiket</th>
            <th>Armada</th>
            <th>Jenis Armada</th>
            <th>Klien</th>
            <th class="text-end">Bruto (kg)</th>
            <th class="text-end">Tarra (kg)</th>
            <th class="text-end">Berat Netto (kg)</th>
            <th class="text-end">Biaya Tipping</th>
            <th>Status</th>
            <th>Approve</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $r)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($r->waktu_masuk)->format('d/m/Y') }}</td>
            <td>{{ $r->nomor_tiket }}</td>
            <td>{{ $r->armada->plat_nomor ?? '-' }}</td>
            <td>{{ $r->armada->jenis_armada ?? '-' }}</td>
            <td>{{ $r->klien->nama_klien ?? '-' }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($r->berat_bruto) : number_format($r->berat_bruto, 2, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($r->berat_tarra) : number_format($r->berat_tarra, 2, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($r->berat_netto) : number_format($r->berat_netto, 2, ',', '.')) }}</td>
            <td class="text-end">{{ $r->biaya_tipping }}</td>
            <td>{{ ucfirst($r->status) }}</td>
            <td>{{ $r->is_approved ? 'Yes' : 'No' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td colspan="6" class="text-end">TOTAL KESELURUHAN</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($totals->total_bruto ?? 0) : number_format($totals->total_bruto ?? 0, 2, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($totals->total_tarra ?? 0) : number_format($totals->total_tarra ?? 0, 2, ',', '.')) }}</td>
            <td class="text-end">{{ (request('export') == 'excel' ? ($totals->total_netto ?? 0) : number_format($totals->total_netto ?? 0, 2, ',', '.')) }}</td>
            <td class="text-end">{{ $totals->total_tipping ?? 0 }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@endsection
