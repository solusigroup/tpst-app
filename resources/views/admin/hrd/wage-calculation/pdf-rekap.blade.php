<!DOCTYPE html>
<html>
<head>
    <title>Rekap Perhitungan Upah</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; padding: 0; }
    </style>
</head>
<body>

<div class="header">
    <h2>Rekapitulasi Perhitungan Upah Karyawan</h2>
    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
</div>

<table>
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th>Karyawan</th>
            <th>Periode Mingguan</th>
            <th class="text-end">Total Output</th>
            <th class="text-end">Total Upah</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($wages as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $item->user->name ?? 'Unknown' }}</td>
            <td>
                @if($item->user->salary_type === 'bulanan')
                    Bulan {{ \Carbon\Carbon::parse($item->week_start)->translatedFormat('F Y') }}
                @else
                    {{ \Carbon\Carbon::parse($item->week_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->week_end)->format('d/m/Y') }}
                @endif
            </td>
            <td class="text-end">{{ number_format($item->total_output, 2, ',', '.') }} kg</td>
            <td class="text-end">Rp {{ number_format($item->total_wage, 2, ',', '.') }}</td>
            <td>
                @if($item->status == 'pending') Pending
                @elseif($item->status == 'approved') Disetujui
                @elseif($item->status == 'paid') Dibayar
                @else {{ ucfirst($item->status) }}
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">Belum ada data rekapitulasi pada filter ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
