<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Posisi Keuangan - {{ $tenant->name ?? 'TPST' }}</title>
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
        .account-table td { padding: 4px 8px; }
        .total-row { font-weight: bold; border-top: 1px solid #374151; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased p-8">

    <!-- Print/Action Buttons -->
    <div class="max-w-4xl mx-auto mb-8 flex justify-end gap-3 no-print">
        @if(!$isBalanced)
        <div class="mr-auto px-4 py-2 bg-rose-100 text-rose-700 rounded-lg text-sm font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            TIDAK SEIMBANG: Selisih Rp {{ number_format($selisih, 0, ',', '.') }}
        </div>
        @else
        <div class="mr-auto px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            STATUS: BALANCE (SEIMBANG)
        </div>
        @endif
        
        <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Cetak Laporan
        </button>
        <button onclick="window.close()" class="px-5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg shadow-sm transition">
            Tutup
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-10 shadow-xl print-container mt-10">
        <!-- Header -->
        <x-kop-surat :tenant="$tenant" />
        <div class="text-center mb-8">
            <h2 class="text-3xl font-black text-slate-900 uppercase">Laporan Posisi Keuangan</h2>
            <p class="text-lg text-slate-600 font-medium mt-1">{{ $periodLabel }}</p>
        </div>

        <div class="grid grid-cols-2 gap-8">
            <!-- Col 1: ASET -->
            <div>
                <h3 class="bg-slate-100 px-3 py-1 font-bold text-slate-900 border-l-4 border-indigo-600 mb-4 uppercase text-sm tracking-wide">ASET</h3>
                
                <div class="mb-6">
                    <table class="w-full account-table text-sm">
                        <tbody>
                            @foreach($asetItems->sortBy('kode_akun') as $item)
                            <tr>
                                <td class="text-slate-500 w-20">{{ $item->kode_akun }}</td>
                                <td class="text-slate-800">{{ $item->nama_akun }}</td>
                                <td class="text-right font-medium">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-slate-900 text-white p-3 flex justify-between items-center rounded-sm mt-auto">
                    <span class="font-bold uppercase tracking-wider text-xs">TOTAL ASET</span>
                    <span class="text-lg font-black font-mono">Rp {{ number_format($aset, 2, ',', '.') }}</span>
                </div>
            </div>

            <!-- Col 2: LIABILITAS & EKUITAS -->
            <div>
                <h3 class="bg-slate-100 px-3 py-1 font-bold text-slate-900 border-l-4 border-emerald-600 mb-4 uppercase text-sm tracking-wide">LIABILITAS & EKUITAS</h3>

                <!-- Liabilitas -->
                <div class="mb-6">
                    <p class="font-bold text-slate-700 mb-2 underline decoration-emerald-300 italic">Liabilitas</p>
                    <table class="w-full account-table text-sm">
                        <tbody>
                            @foreach($liabilitasItems->sortBy('kode_akun') as $item)
                            <tr>
                                <td class="text-slate-500 w-20">{{ $item->kode_akun }}</td>
                                <td class="text-slate-800">{{ $item->nama_akun }}</td>
                                <td class="text-right font-medium">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="2" class="text-right pr-4 uppercase text-[10px] tracking-wider text-slate-500 font-bold">Total Liabilitas</td>
                                <td class="text-right text-emerald-700 font-bold">Rp {{ number_format($liabilitas, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Ekuitas -->
                <div class="mb-6">
                    <p class="font-bold text-slate-700 mb-2 underline decoration-emerald-300 italic">Ekuitas</p>
                    <table class="w-full account-table text-sm">
                        <tbody>
                            @foreach($ekuitasItems->sortBy('kode_akun') as $item)
                            <tr>
                                <td class="text-slate-500 w-20">{{ $item->kode_akun }}</td>
                                <td class="text-slate-800">{{ $item->nama_akun }}</td>
                                <td class="text-right font-medium">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <!-- Important: Accounting Core - Net Income integration -->
                            <tr>
                                <td class="text-slate-400 w-20 italic">INC</td>
                                <td class="text-indigo-600 font-bold italic">Laba (Rugi) Berjalan</td>
                                <td class="text-right font-black text-indigo-700">Rp {{ number_format($labaRugiBerjalan, 2, ',', '.') }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2" class="text-right pr-4 uppercase text-[10px] tracking-wider text-slate-500 font-bold">Total Ekuitas</td>
                                <td class="text-right text-emerald-700 font-bold">Rp {{ number_format($totalEkuitas, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-slate-900 text-white p-3 flex justify-between items-center rounded-sm">
                    <span class="font-bold uppercase tracking-wider text-xs">TOTAL LIABILITAS & EKUITAS</span>
                    <span class="text-lg font-black font-mono">Rp {{ number_format($totalPasiva, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <x-report-signatures />

        <div class="mt-8 pt-8 border-t border-slate-100 flex justify-between text-slate-400 text-[10px] uppercase tracking-widest">
            <span>Dicetak pada: {{ date('d/m/Y H:i') }}</span>
            <span>Accounting Compliance: SAK-EP</span>
        </div>
    </div>
</body>
</html>
