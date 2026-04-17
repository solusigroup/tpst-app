@extends('admin.laporan.exports.layout', ['title' => 'Database Karyawan'])

@section('content')
<style>
    @page { size: landscape; }
    .table th, .table td { font-size: 9px; padding: 4px; }
    .text-center { text-align: center; }
</style>

<div class="text-center mb-4">
    <h2 style="margin:0">DATABASE KARYAWAN LENGKAP</h2>
    <p style="margin:5px 0">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="20">No</th>
            <th>Nama Karyawan</th>
            <th>Jabatan</th>
            <th>No. KTP</th>
            <th>Gender</th>
            <th>Tipe Gaji</th>
            <th>Gaji/Upah</th>
            <th>Frekuensi</th>
            <th>Tgl Masuk</th>
            <th>Tgl Keluar</th>
            <th>BPJS</th>
            <th>No. BPJS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $index => $emp)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td><strong>{{ $emp->name }}</strong></td>
            <td>{{ $emp->position ?? '-' }}</td>
            <td>{{ $emp->ktp_number ?? '-' }}</td>
            <td>{{ $emp->gender ?? '-' }}</td>
            <td style="text-transform: capitalize;">{{ $emp->salary_type ?? '-' }}</td>
            <td>
                @if($emp->salary_type === 'bulanan')
                    Rp {{ number_format($emp->monthly_salary, 0, ',', '.') }}
                @elseif($emp->salary_type === 'harian')
                    Rp {{ number_format($emp->daily_wage, 0, ',', '.') }}/hari
                @else
                    Borongan
                @endif
            </td>
            <td>{{ $emp->payment_frequency ?? '-' }}</td>
            <td>{{ $emp->joined_at ? \Carbon\Carbon::parse($emp->joined_at)->format('d/m/Y') : '-' }}</td>
            <td>{{ $emp->ended_at ? \Carbon\Carbon::parse($emp->ended_at)->format('d/m/Y') : '-' }}</td>
            <td class="text-center">{{ $emp->bpjs_status ?? 'Tidak Aktif' }}</td>
            <td>{{ $emp->bpjs_number ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
