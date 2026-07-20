@extends('admin.laporan.exports.layout', ['title' => 'Laporan Ritase Rerata Harian Per Bulan'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN RITASE RERATA HARIAN PER BULAN</h2>
    <p style="margin:5px 0">Tahun: {{ $tahun }}</p>
    @if(isset($isExport) && !$isExport)
    <p style="margin:2px 0; font-size:10px; color:#666;">
        @if($klien) Filter Klien: {{ $klien->nama_klien }} | @endif Filter Jenis: {{ $jenisKlien ?: 'Semua' }}
    </p>
    @endif
</div>

<table class="table">
    <thead style="background-color: #f8f9fa;">
        <tr>
            <th rowspan="2" style="vertical-align: middle; text-align: center;">Bulan</th>
            <th colspan="3" style="text-align: center;">Ritase</th>
            <th colspan="3" style="text-align: center;">Tonase / Netto (kg)</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center;">Hari Kalender</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center;">Hari Aktif</th>
        </tr>
        <tr>
            <th style="text-align: center;">Total</th>
            <th style="text-align: center;">Rerata/Hari (Kal)</th>
            <th style="text-align: center;">Rerata/Hari (Akt)</th>
            <th style="text-align: center;">Total</th>
            <th style="text-align: center;">Rerata/Hari (Kal)</th>
            <th style="text-align: center;">Rerata/Hari (Akt)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData as $row)
        <tr>
            <td style="font-weight: bold;">{{ $row->nama_bulan }}</td>
            <td class="text-center">{{ (isset($isExport) && $isExport) ? $row->total_ritase : number_format($row->total_ritase, 0, ',', '.') }}</td>
            <td class="text-center fw-bold">{{ (isset($isExport) && $isExport) ? $row->rerata_ritase_kalender : number_format($row->rerata_ritase_kalender, 2, ',', '.') }}</td>
            <td class="text-center">{{ (isset($isExport) && $isExport) ? $row->rerata_ritase_aktif : number_format($row->rerata_ritase_aktif, 2, ',', '.') }}</td>
            <td class="text-end">{{ (isset($isExport) && $isExport) ? $row->total_netto : number_format($row->total_netto, 2, ',', '.') }}</td>
            <td class="text-end fw-bold">{{ (isset($isExport) && $isExport) ? $row->rerata_netto_kalender : number_format($row->rerata_netto_kalender, 2, ',', '.') }}</td>
            <td class="text-end">{{ (isset($isExport) && $isExport) ? $row->rerata_netto_aktif : number_format($row->rerata_netto_aktif, 2, ',', '.') }}</td>
            <td class="text-center">{{ $row->calendar_days }}</td>
            <td class="text-center">{{ $row->active_days }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="fw-bold" style="background-color: #f8f9fa;">
        <tr>
            <td style="text-align: center;">TOTAL / RERATA</td>
            <td class="text-center">{{ (isset($isExport) && $isExport) ? $grandTotalRitase : number_format($grandTotalRitase, 0, ',', '.') }}</td>
            <td class="text-center">{{ (isset($isExport) && $isExport) ? $overallRerataRitaseKalender : number_format($overallRerataRitaseKalender, 2, ',', '.') }}</td>
            <td class="text-center">{{ (isset($isExport) && $isExport) ? $overallRerataRitaseAktif : number_format($overallRerataRitaseAktif, 2, ',', '.') }}</td>
            <td class="text-end">{{ (isset($isExport) && $isExport) ? $grandTotalNetto : number_format($grandTotalNetto, 2, ',', '.') }}</td>
            <td class="text-end">{{ (isset($isExport) && $isExport) ? $overallRerataNettoKalender : number_format($overallRerataNettoKalender, 2, ',', '.') }}</td>
            <td class="text-end">{{ (isset($isExport) && $isExport) ? $overallRerataNettoAktif : number_format($overallRerataNettoAktif, 2, ',', '.') }}</td>
            <td class="text-center">{{ $totalCalendarDays }}</td>
            <td class="text-center">{{ $totalActiveDays }}</td>
        </tr>
    </tfoot>
</table>

@if(!isset($isExport) || !$isExport)
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
@endif
@endsection
