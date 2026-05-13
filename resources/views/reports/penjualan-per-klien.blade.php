<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Per Klien - {{ $period }}</title>
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
            <h1 class="text-xl font-bold text-gray-900">Preview Laporan Penjualan Per Klien</h1>
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
            <h3 class="text-2xl font-bold text-gray-800 uppercase">LAPORAN PENJUALAN PER KLIEN</h3>
            <p class="text-gray-500 font-medium uppercase tracking-widest mt-1">PERIODE: {{ $period }}</p>
        </div>

        <!-- content -->
        <div class="space-y-12">
            @forelse($reports as $report)
            <section class="break-inside-avoid">
                <div class="flex justify-between items-end border-b-2 border-gray-900 pb-2 mb-4">
                    <div>
                        <h4 class="text-lg font-extrabold text-gray-900 uppercase">{{ $report->klien->nama_klien }}</h4>
                        <p class="text-xs text-gray-500 font-medium italic">{{ $report->klien->alamat }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Frekuensi:</span>
                        <span class="text-lg font-black text-indigo-600 ml-1">{{ $report->frequency }}x</span>
                    </div>
                </div>

                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase tracking-tighter border-b border-gray-200 bg-gray-50">
                            <th class="py-2 px-2 font-semibold">Tanggal</th>
                            <th class="py-2 px-2 font-semibold">Produk / Kategori</th>
                            <th class="py-2 px-2 text-right font-semibold">Berat (kg)</th>
                            <th class="py-2 px-2 text-right font-semibold">Harga Satuan</th>
                            <th class="py-2 px-2 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($report->items as $item)
                        <tr>
                            <td class="py-2 px-2 text-gray-600">{{ $item->tanggal->format('d/m/Y') }}</td>
                            <td class="py-2 px-2 text-gray-800 font-medium">{{ $item->jenis_produk }}</td>
                            <td class="py-2 px-2 text-right text-gray-700">{{ number_format($item->berat_kg, 2, ',', '.') }}</td>
                            <td class="py-2 px-2 text-right text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-2 px-2 text-right text-gray-900 font-bold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-extrabold text-gray-900 bg-gray-50">
                            <td colspan="4" class="py-3 px-2 text-right uppercase tracking-tight">Total Penjualan - {{ $report->klien->nama_klien }}</td>
                            <td class="py-3 px-2 text-right border-t-2 border-gray-900">Rp {{ number_format($report->total_nominal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            @empty
            <div class="py-20 text-center text-gray-400 italic bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Tidak ada data penjualan dalam periode ini
            </div>
            @endforelse
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
