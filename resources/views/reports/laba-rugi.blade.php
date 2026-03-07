<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - {{ $period }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            .no-print {
                display: none;
            }
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact;
            }
            .report-bridge {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .report-bridge {
            background-color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 3rem;
            max-width: 21cm;
            margin: 3rem auto;
            min-height: 29.7cm;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="fixed top-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md shadow-sm no-print z-50 flex justify-between items-center px-10">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Preview Laporan Laba Rugi</h1>
            <p class="text-sm text-gray-600 font-medium">Periode: <span class="text-indigo-600">{{ $period }}</span></p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-bold shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                PDF / CETAK
            </button>
            <button onclick="window.close()" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-2 rounded-lg font-bold transition-all">
                TUTUP
            </button>
        </div>
    </div>

    <div class="report-bridge mt-20">
        <!-- Header -->
        <x-kop-surat :tenant="$tenant" />
        <div class="text-center mb-10">
            <h3 class="text-2xl font-bold text-gray-800">LAPORAN LABA RUGI</h3>
            <p class="text-gray-500 font-medium uppercase tracking-widest mt-1">UNTUK PERIODE YANG BERAKHIR PADA {{ $period }}</p>
        </div>

        <!-- content -->
        <div class="space-y-12">
            <!-- Pendapatan -->
            <section>
                <div class="flex justify-between items-center border-b-2 border-gray-900 pb-2 mb-4">
                    <h4 class="text-lg font-extrabold text-gray-900 uppercase">I. PENDAPATAN</h4>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase tracking-tighter border-b border-gray-200">
                            <th class="pb-2 font-semibold">Akun</th>
                            <th class="pb-2 text-right font-semibold" colspan="2">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($revenue as $item)
                        <tr>
                            <td class="py-3 w-2/3 text-gray-800 font-medium">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                            <td class="py-3 text-right text-gray-600">Rp</td>
                            <td class="py-3 text-right text-gray-900 font-bold w-32">{{ number_format($item->total, 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-gray-400 italic bg-gray-50 rounded-lg">Tidak ada data pendapatan dalam periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-extrabold text-gray-900 bg-gray-50">
                            <td class="py-4 px-2 uppercase tracking-tight">TOTAL PENDAPATAN</td>
                            <td class="py-4 text-right">Rp</td>
                            <td class="py-4 text-right border-t-2 border-gray-900">{{ number_format($totalRevenue, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </section>

            <!-- Beban -->
            <section>
                <div class="flex justify-between items-center border-b-2 border-gray-900 pb-2 mb-4">
                    <h4 class="text-lg font-extrabold text-gray-900 uppercase">II. BEBAN OPERASIONAL</h4>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase tracking-tighter border-b border-gray-200">
                            <th class="pb-2 font-semibold">Akun</th>
                            <th class="pb-2 text-right font-semibold" colspan="2">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($expenses as $item)
                        <tr>
                            <td class="py-3 w-2/3 text-gray-800 font-medium">{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                            <td class="py-3 text-right text-gray-600">Rp</td>
                            <td class="py-3 text-right text-gray-900 font-bold w-32">({{ number_format($item->total, 2, ',', '.') }})</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-gray-400 italic bg-gray-50 rounded-lg">Tidak ada data beban operasional dalam periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-extrabold text-gray-900 bg-gray-50">
                            <td class="py-4 px-2 uppercase tracking-tight">TOTAL BEBAN OPERASIONAL</td>
                            <td class="py-4 text-right">Rp</td>
                            <td class="py-4 text-right border-t-2 border-gray-900">({{ number_format($totalExpenses, 2, ',', '.') }})</td>
                        </tr>
                    </tfoot>
                </table>
            </section>

            <!-- Laba Bersih -->
            <section class="border-t-4 border-double border-gray-900 pt-6">
                <div class="bg-gray-900 text-white p-6 rounded-lg flex justify-between items-center">
                    <h4 class="text-xl font-black uppercase tracking-widest">LABA (RUGI) BERSIH</h4>
                    <div class="text-right">
                        <span class="text-sm font-bold opacity-80 mr-2">Rp</span>
                        <span class="text-3xl font-black {{ $netProfit >= 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ number_format($netProfit, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </section>
        </div>

        <!-- Signatures -->
        <x-report-signatures />

        <div class="mt-16 text-[10px] text-gray-400 border-t border-gray-100 pt-4 flex justify-between italic">
            <span>Generated by TPST Management System</span>
            <span>Waktu Cetak: {{ now()->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>
</body>
</html>
