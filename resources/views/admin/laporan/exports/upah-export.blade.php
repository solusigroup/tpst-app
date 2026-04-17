@extends('admin.laporan.exports.layout', ['title' => 'Laporan Upah Karyawan'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN PERHITUNGAN UPAH KARYAWAN</h2>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
</div>

<div style="margin-bottom: 20px;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Parameter Laporan:</strong><br>
                Skema Upah: {{ $skemaUpah ?: 'Semua' }}<br>
                Total Record: {{ $totals->total_rows }}
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <strong>Ringkasan Keuangan:</strong><br>
                Total Upah: Rp {{ number_format($totals->total_wage, 0, ',', '.') }}<br>
                Sudah Dibayar: <span style="color: green;">Rp {{ number_format($totals->total_paid, 0, ',', '.') }}</span><br>
                Belum Dibayar: <span style="color: red;">Rp {{ number_format($totals->total_unpaid, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>
</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Periode</th>
            <th>Nama Karyawan</th>
            <th class="text-center">H</th>
            <th class="text-center">S/I</th>
            <th class="text-center">A</th>
            <th>Skema</th>
            <th class="text-right">Total Upah</th>
            <th class="text-right">Sdh Dibayar</th>
            <th class="text-right">Blm Dibayar</th>
            <th class="text-center">Status</th>
            <th>Tgl Bayar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $r)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td style="font-size: 10px;">{{ \Carbon\Carbon::parse($r->week_start)->format('d/m/y') }}-{{ \Carbon\Carbon::parse($r->week_end)->format('d/m/y') }}</td>
            <td>{{ $r->user->name ?? '-' }}</td>
            <td class="text-center">{{ $r->stats->hadir ?? 0 }}</td>
            <td class="text-center">{{ ($r->stats->sakit ?? 0) + ($r->stats->izin ?? 0) }}</td>
            <td class="text-center">{{ $r->stats->mangkir ?? 0 }}</td>
            <td style="text-transform: capitalize;">{{ $r->user->salary_type ?? '-' }}</td>
            <td class="text-right">Rp {{ number_format($r->total_wage, 0, ',', '.') }}</td>
            <td class="text-right">
                {{ $r->status === 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-' }}
            </td>
            <td class="text-right">
                {{ $r->status !== 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-' }}
            </td>
            <td class="text-center">{{ ucfirst($r->status) }}</td>
            <td>{{ $r->paid_date ? \Carbon\Carbon::parse($r->paid_date)->format('d/m/Y') : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <td colspan="7" class="text-right">TOTAL</td>
            <td class="text-right">Rp {{ number_format($totals->total_wage, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($totals->total_paid, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($totals->total_unpaid, 0, ',', '.') }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@endsection
