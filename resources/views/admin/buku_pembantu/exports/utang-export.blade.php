@extends('admin.laporan.exports.layout', ['title' => 'Buku Pembantu Utang'])

@section('content')
@php
    $isExcel = (isset($isExport) && $isExport) || request('export') === 'excel';
@endphp

<div class="text-center mb-4">
    <h2 style="margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase;">Buku Pembantu Utang / Liabilitas</h2>
    @if(!empty($dari) || !empty($sampai))
        <p style="margin: 5px 0 0 0; color: #555; font-size: 11px;">
            Periode: {{ !empty($dari) ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Awal' }} s/d {{ !empty($sampai) ? \Carbon\Carbon::parse($sampai)->format('d/m/Y') : 'Sekarang' }}
        </p>
    @else
        <p style="margin: 5px 0 0 0; color: #555; font-size: 11px;">Semua Periode</p>
    @endif
    <p style="margin: 4px 0 0 0; color: #666; font-size: 10px;">
        @if(!empty($selectedCoa))
            Akun: <strong>{{ $selectedCoa->kode_akun }} - {{ $selectedCoa->nama_akun }}</strong> |
        @else
            Akun: <strong>Semua Akun Liabilitas</strong> |
        @endif
        Status: <strong>{{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}</strong>
        @if(request('search'))
            | Pencarian: "<strong>{{ request('search') }}</strong>"
        @endif
    </p>
</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
    <tr>
        <td style="border: 1px solid #dee2e6; padding: 6px 10px; width: 33.33%; background-color: #f8f9fa;">
            <div style="font-size: 9px; color: #555; text-transform: uppercase; font-weight: bold;">Total Utang Awal</div>
            <div style="font-size: 13px; font-weight: bold; color: #0d6efd; margin-top: 2px;">
                {{ $isExcel ? $totalHutangAwal : 'Rp ' . number_format($totalHutangAwal ?? 0, 0, ',', '.') }}
            </div>
        </td>
        <td style="border: 1px solid #dee2e6; padding: 6px 10px; width: 33.33%; background-color: #f8f9fa;">
            <div style="font-size: 9px; color: #555; text-transform: uppercase; font-weight: bold;">Total Sudah Dibayar</div>
            <div style="font-size: 13px; font-weight: bold; color: #198754; margin-top: 2px;">
                {{ $isExcel ? $totalTerbayar : 'Rp ' . number_format($totalTerbayar ?? 0, 0, ',', '.') }}
            </div>
        </td>
        <td style="border: 1px solid #dee2e6; padding: 6px 10px; width: 33.33%; background-color: #f8f9fa;">
            <div style="font-size: 9px; color: #555; text-transform: uppercase; font-weight: bold;">Sisa Utang (Outstanding)</div>
            <div style="font-size: 13px; font-weight: bold; color: #dc3545; margin-top: 2px;">
                {{ $isExcel ? $totalJumlah : 'Rp ' . number_format($totalJumlah ?? 0, 0, ',', '.') }}
            </div>
        </td>
    </tr>
</table>

<table class="table" style="width: 100%; border-collapse: collapse; font-size: {{ $isExcel ? '11px' : '9px' }};">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center; width: 30px;">No</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">Tgl Terbentuk</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px;">Pemilik Hutang</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px;">Akun COA Liabilitas</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px;">Keterangan</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: right;">Jumlah Utang</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: right;">Jmlh Dibayar</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">Tgl Dibayar</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: right;">Sisa (Outstanding)</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">Jatuh Tempo</th>
            <th style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($entries as $index => $item)
            @php
                $sisa = $item->status == 'lunas' ? 0 : max(0, $item->jumlah - $item->terbayar);
                $pemilik = $item->contactable->nama_vendor ?? $item->contactable->nama_klien ?? $item->contactable->name ?? 'N/A';
                $akunCoa = $item->coa ? ($item->coa->kode_akun . ' - ' . $item->coa->nama_akun) : '-';
                
                $tglDibayar = '-';
                if ($item->settledByJurnalHeader && $item->settledByJurnalHeader->tanggal) {
                    $tglDibayar = \Carbon\Carbon::parse($item->settledByJurnalHeader->tanggal)->format('d/m/Y');
                } elseif ($item->status == 'lunas' && $item->updated_at) {
                    $tglDibayar = $item->updated_at->format('d/m/Y');
                }
            @endphp
            <tr>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px;">{{ $pemilik }}</td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px;">{{ $akunCoa }}</td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px;">{{ $item->keterangan ?? '-' }}</td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: right;">
                    {{ $isExcel ? $item->jumlah : number_format($item->jumlah, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: right; color: #198754;">
                    {{ $isExcel ? $item->terbayar : number_format($item->terbayar, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">{{ $tglDibayar }}</td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: right; font-weight: bold; {{ $item->status == 'lunas' ? 'color: #198754;' : 'color: #dc3545;' }}">
                    {{ $isExcel ? $sisa : number_format($sisa, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">
                    {{ $item->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 4px 5px; text-align: center;">
                    {{ ucfirst($item->status) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" style="border: 1px solid #dee2e6; padding: 10px; text-align: center; color: #777;">
                    Tidak ada data buku pembantu utang / liabilitas.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot style="background-color: #f8f9fa; font-weight: bold;">
        <tr>
            <td colspan="5" style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">TOTAL</td>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right;">
                {{ $isExcel ? $totalHutangAwal : number_format($totalHutangAwal ?? 0, 0, ',', '.') }}
            </td>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right; color: #198754;">
                {{ $isExcel ? $totalTerbayar : number_format($totalTerbayar ?? 0, 0, ',', '.') }}
            </td>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">-</td>
            <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right; color: #dc3545;">
                {{ $isExcel ? $totalJumlah : number_format($totalJumlah ?? 0, 0, ',', '.') }}
            </td>
            <td colspan="2" style="border: 1px solid #dee2e6; padding: 5px; text-align: center;">-</td>
        </tr>
    </tfoot>
</table>

@if(!$isExcel)
<div style="margin-top: 25px; page-break-inside: avoid;">
    <table class="table-borderless" style="width: 100%; border: none;">
        <tr>
            <td style="border: none; width: 70%;"></td>
            <td style="border: none; text-align: center; width: 30%;">
                <p style="margin: 0;">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
                <p style="margin: 0; font-size: 10px; color: #555;">Oleh: {{ auth()->user()->name ?? 'Administrator' }}</p>
                <div style="margin-top: 50px;">
                    <p style="margin: 0; font-weight: bold;">( ____________________ )</p>
                </div>
            </td>
        </tr>
    </table>
</div>
@endif
@endsection
