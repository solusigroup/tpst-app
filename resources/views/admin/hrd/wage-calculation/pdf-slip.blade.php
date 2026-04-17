<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; padding: 0; }
        .info-table { width: 100%; margin-bottom: 20px; border: none; }
        .info-table td { padding: 4px 0; border: none; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .total-box { border: 1px solid #000; padding: 10px; text-align: right; font-size: 14px; font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 50px; width: 100%; clear: both; }
        .signature-box { float: right; width: 200px; text-align: center; }
        .signature-line { margin-top: 60px; border-bottom: 1px solid #000; width: 100%; margin-bottom: 5px; }
    </style>
</head>
<body>

<div class="header">
    <h2>SLIP GAJI KARYAWAN</h2>
</div>

<table class="info-table">
    <tr>
        <td width="150"><strong>Nama Karyawan</strong></td>
        <td>: {{ $wageCalculation->user->name ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Jabatan / Peran</strong></td>
        <td>: {{ $wageCalculation->user->position ?? 'Karyawan Pemilah' }}</td>
    </tr>
    <tr>
        <td><strong>Periode Gaji</strong></td>
        <td>: 
            @if($wageCalculation->user->salary_type === 'bulanan')
                Bulan {{ \Carbon\Carbon::parse($wageCalculation->week_start)->translatedFormat('F Y') }}
            @else
                {{ \Carbon\Carbon::parse($wageCalculation->week_start)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($wageCalculation->week_end)->format('d/m/Y') }}
            @endif
        </td>
    </tr>
    <tr>
        <td><strong>Status Pembayaran</strong></td>
        <td>: 
            @if($wageCalculation->status == 'pending') Belum Dibayar (Pending)
            @elseif($wageCalculation->status == 'approved') Belum Dibayar (Disetujui)
            @elseif($wageCalculation->status == 'paid') Lunas Dibayar (Tgl: {{ \Carbon\Carbon::parse($wageCalculation->paid_date)->format('d/m/Y') }})
            @endif
        </td>
    </tr>
</table>

<h4>Rincian Hasil Output:</h4>
<table class="data-table">
    <thead>
        <tr>
            <th width="50" class="text-center">No</th>
            <th>Tanggal Output</th>
            <th>Kategori Sampah</th>
            <th class="text-end">Kuantitas</th>
        </tr>
    </thead>
    <tbody>
        @forelse($outputs as $index => $out)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($out->output_date)->format('d/m/Y') }}</td>
            <td>{{ $out->wasteCategory->name ?? '-' }}</td>
            <td class="text-end">{{ number_format($out->quantity, 2, ',', '.') }} {{ $out->unit }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">Tidak ada catatan output pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="total-box">
    @if($wageCalculation->user->salary_type === 'borongan' || $wageCalculation->total_quantity > 0)
    Total Keseluruhan Output: {{ number_format($wageCalculation->total_quantity, 2, ',', '.') }} kg<br><br>
    @endif
    @if($wageCalculation->user->salary_type === 'bulanan')
    GAJI POKOK BULANAN: Rp {{ number_format($wageCalculation->user->monthly_salary ?? 0, 0, ',', '.') }}<br>
    @else
    UPAH DASAR: Rp {{ number_format($wageCalculation->total_wage, 0, ',', '.') }}<br>
    @endif
    
    @if($wageCalculation->overtime_pay > 0)
    UPAH LEMBUR: Rp {{ number_format($wageCalculation->overtime_pay, 0, ',', '.') }}<br>
    @endif
    
    <br>
    <strong>TOTAL UPAH DITERIMA: Rp {{ number_format($wageCalculation->total_wage + $wageCalculation->overtime_pay, 0, ',', '.') }}</strong>
</div>

<div class="footer">
    <div class="signature-box">
        Penerima / Karyawan<br>
        <div class="signature-line"></div>
        {{ $wageCalculation->user->name ?? '-' }}
    </div>
</div>

</body>
</html>
