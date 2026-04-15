@extends('admin.laporan.exports.layout', ['title' => 'Laporan Kehadiran Karyawan'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN KEHADIRAN KARYAWAN</h2>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
</div>

<div style="margin-bottom: 20px;">
    <strong>Ringkasan Status:</strong><br>
    Hadir: {{ $totals->present }} | Alpa: {{ $totals->absent }} | Sakit: {{ $totals->sick }} | Izin: {{ $totals->leave }} | Total: {{ $totals->total_rows }}
</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>Nama Karyawan</th>
            <th>Status</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $r)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($r->attendance_date)->format('d/m/Y') }}</td>
            <td>{{ $r->user->name ?? '-' }}</td>
            <td>{{ ucfirst($r->status) }}</td>
            <td>{{ $r->clock_in ? \Carbon\Carbon::parse($r->clock_in)->format('H:i') : '-' }}</td>
            <td>{{ $r->clock_out ? \Carbon\Carbon::parse($r->clock_out)->format('H:i') : '-' }}</td>
            <td>{{ $r->notes ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
