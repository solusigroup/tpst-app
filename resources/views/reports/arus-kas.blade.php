<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas - {{ $tenant->name ?? 'TPST' }}</title>
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
        .account-table td { padding: 6px 8px; }
        .total-row { font-weight: bold; border-top: 1px solid #374151; border-bottom: 2px double #374151; }
    </style>
</head>
<body class="bg-zinc-50 font-sans text-zinc-800 antialiased p-8">

    <div class="max-w-3xl mx-auto mb-8 flex justify-end gap-3 no-print">
        <button onclick="window.print()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Cetak Laporan
        </button>
        <button onclick="window.close()" class="px-5 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-600 rounded-lg shadow-sm transition">
            Tutup
        </button>
    </div>

    <div class="max-w-3xl mx-auto bg-white p-12 shadow-xl print-container mt-10">
        <!-- Header -->
        <x-kop-surat :tenant="$tenant" />
        <div class="text-center mb-10">
            <h2 class="text-2xl font-black text-zinc-900 tracking-tight uppercase">Laporan Arus Kas</h2>
            <p class="text-sm text-zinc-500 font-medium mt-1 uppercase tracking-widest">{{ $periodLabel }}</p>
        </div>

        <div class="space-y-10">
            <!-- Arus Kas Operasi -->
            <section>
                <h3 class="font-black text-zinc-900 border-b border-zinc-200 pb-2 mb-4 uppercase text-xs tracking-[0.2em]">Arus Kas dari Aktivitas Operasi</h3>
                <table class="w-full account-table text-sm">
                    <tbody>
                        @foreach($operasi as $item)
                        <tr>
                            <td class="text-zinc-400 w-24 tabular-nums">{{ $item->kode_akun }}</td>
                            <td class="text-zinc-800">{{ $item->nama_akun }}</td>
                            <td class="text-right font-semibold tabular-nums">
                                @if($item->kas_bersih > 0)
                                    Rp {{ number_format($item->kas_bersih, 0, ',', '.') }}
                                @else
                                    (Rp {{ number_format(abs($item->kas_bersih), 0, ',', '.') }})
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-zinc-900">
                            <td colspan="2" class="py-4 font-bold text-zinc-900 uppercase text-xs">Arus Kas Bersih dari Aktivitas Operasi</td>
                            <td class="text-right py-4 font-black text-zinc-900">Rp {{ number_format($totalKasBersih, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </section>

            <!-- Ringkasan Kas -->
            <section class="bg-zinc-50 p-6 rounded-sm border border-zinc-100">
                <table class="w-full text-sm">
                    <tr class="h-10">
                        <td class="text-zinc-600 uppercase text-xs tracking-wider font-bold">Kenaikan (Penurunan) Kas Bersih</td>
                        <td class="text-right font-black text-zinc-900">Rp {{ number_format($totalKasBersih, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="h-10">
                        <td class="text-zinc-600 uppercase text-xs tracking-wider font-bold">Saldo Kas & Bank Awal Periode</td>
                        <td class="text-right font-black text-zinc-900">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="h-12 border-t border-zinc-300">
                        <td class="text-zinc-900 uppercase text-xs tracking-widest font-black">Saldo Kas & Bank Akhir Periode</td>
                        <td class="text-right font-black text-xl text-emerald-700">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </section>
        </div>

        <x-report-signatures />
    </div>

</body>
</html>
