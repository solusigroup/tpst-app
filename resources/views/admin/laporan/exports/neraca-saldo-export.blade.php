@extends('admin.laporan.exports.layout', ['title' => 'Neraca Saldo'])

@section('content')
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">NERACA SALDO</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px;">Kode Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px;">Nama Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Total Debit</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Total Kredit</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ $row->kode_akun }}</td>
            <td style="border: 1px solid #ddd; padding: 5px;">{{ $row->nama_akun }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">{{ number_format($row->total_debit, 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">{{ number_format($row->total_kredit, 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; font-weight: bold;">
                {{ number_format($row->saldo, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">TOTAL</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">{{ number_format($totalDebit, 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">{{ number_format($totalKredit, 0, ',', '.') }}</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">
                {{ number_format($totalDebit - $totalKredit, 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>
@endsection
