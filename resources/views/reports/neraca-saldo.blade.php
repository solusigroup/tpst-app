<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca Saldo - {{ $tenant->name ?? 'TPST' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 10pt; }
            .print-container { width: 100%; max-width: none; margin: 0; padding: 0; }
        }
        @page {
            size: A4;
            margin: 1cm;
        }
        .report-table th { background-color: #f1f5f9; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.1em; padding: 10px 8px; border: 1px solid #cbd5e1; }
        .report-table td { padding: 6px 8px; border: 1px solid #e2e8f0; }
        .total-row { background-color: #f8fafc; font-weight: 800; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased p-8">

    <div class="max-w-4xl mx-auto mb-8 flex justify-end gap-3 no-print">
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition flex items-center gap-2 text-sm font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Cetak Laporan
        </button>
        <button onclick="window.close()" class="px-5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg shadow-sm transition text-sm">
            Tutup
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 shadow-2xl print-container mt-10">
        <!-- Header -->
        <x-kop-surat :tenant="$tenant" />
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight uppercase">Laporan Neraca Saldo</h2>
            <p class="text-sm text-slate-500 font-bold mt-2 uppercase tracking-[0.2em] italic">{{ $periodLabel }}</p>
        </div>

        <table class="w-full report-table text-xs border-collapse">
            <thead>
                <tr>
                    <th class="text-left w-24">Kode Akun</th>
                    <th class="text-left">Nama Akun</th>
                    <th class="text-right w-32">Debit</th>
                    <th class="text-right w-32">Kredit</th>
                    <th class="text-right w-32">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                <tr>
                    <td class="font-mono text-slate-500">{{ $row->kode_akun }}</td>
                    <td class="font-semibold text-slate-700">{{ $row->nama_akun }}</td>
                    <td class="text-right tabular-nums">Rp {{ number_format($row->total_debit, 0, ',', '.') }}</td>
                    <td class="text-right tabular-nums">Rp {{ number_format($row->total_kredit, 0, ',', '.') }}</td>
                    <td class="text-right tabular-nums font-black {{ $row->saldo >= 0 ? 'text-blue-700' : 'text-rose-700' }}">
                        Rp {{ number_format($row->saldo, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row bg-slate-100">
                    <td colspan="2" class="text-right p-4 font-black uppercase tracking-widest text-slate-600">Total Akumulasi</td>
                    <td class="text-right p-4 font-black text-slate-900 bg-blue-50">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="text-right p-4 font-black text-slate-900 bg-rose-50">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                    <td class="text-right p-4 font-black text-slate-900 bg-slate-200">Rp {{ number_format($totalDebit - $totalKredit, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <x-report-signatures />
    </div>

</body>
</html>
