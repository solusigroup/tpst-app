@extends('admin.laporan.exports.layout', ['title' => 'Rekap Kehadiran Karyawan'])

@section('content')
<div class="text-center mb-4">
    <h2 style="margin:0">REKAP KEHADIRAN KARYAWAN</h2>
    <p style="margin:5px 0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
</div>

<div style="margin-bottom: 20px;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Parameter Laporan:</strong><br>
                Tipe Gaji: {{ $salaryType ? ucfirst($salaryType) : 'Semua' }}<br>
                Total Karyawan: {{ $rekapData->count() }}
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <strong>Dicetak pada:</strong><br>
                {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>
</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Nama Karyawan</th>
            <th>Tipe Gaji</th>
            <th class="text-center">Hadir (H)</th>
            <th class="text-center">Sakit (S)</th>
            <th class="text-center">Izin (I)</th>
            <th class="text-center">Alpa (A)</th>
            <th class="text-center">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapData as $index => $emp)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td><strong>{{ $emp->name }}</strong><br><small>{{ $emp->position ?? '-' }}</small></td>
            <td style="text-transform: capitalize;">{{ $emp->salary_type ?? '-' }}</td>
            <td class="text-center">{{ $emp->present_count }}</td>
            <td class="text-center">{{ $emp->sick_count }}</td>
            <td class="text-center">{{ $emp->leave_count }}</td>
            <td class="text-center">{{ $emp->absent_count }}</td>
            <td class="text-center" style="background-color: #f9f9f9; font-weight: bold;">{{ $emp->total_days }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top: 50px;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 70%;"></td>
            <td style="width: 30%; text-align: center;">
                <p>Hormat Kami,</p>
                <div style="height: 80px;"></div>
                <p><strong>( ____________________ )</strong></p>
                <p>Admin HRD</p>
            </td>
        </tr>
    </table>
</div>
@endsection
