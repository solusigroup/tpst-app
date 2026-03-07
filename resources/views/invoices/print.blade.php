<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->nomor_invoice }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact; }
            .print-container { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: none !important; }
        }
        @page {
            size: A4;
            margin: 1.5cm;
        }
    </style>
</head>
<body class="bg-slate-100 font-serif text-slate-900 antialiased p-8">

    <!-- Action Buttons -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-end gap-3 no-print">
        <button onclick="window.print()" class="px-6 py-2 bg-slate-900 text-white rounded-lg shadow hover:bg-slate-800 transition flex items-center gap-2 font-sans font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Print Invoice
        </button>
        <button onclick="window.close()" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition font-sans">
            Tutup
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 shadow-xl print-container">
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-8 mb-10">
            <div>
                <h1 class="text-2xl font-black uppercase text-slate-900">{{ $invoice->tenant->name }}</h1>
                <p class="text-sm text-slate-600 mt-2 max-w-xs leading-relaxed">
                    {!! nl2br(e($invoice->tenant->address)) !!}<br>
                    @if($invoice->tenant->email) Email: {{ $invoice->tenant->email }} @endif
                </p>
            </div>
            <div class="text-right">
                <h2 class="text-5xl font-black text-slate-200 tracking-tighter uppercase mb-4">INVOICE</h2>
                <div class="space-y-1 text-sm font-sans uppercase tracking-wider font-bold">
                    <p><span class="text-slate-400">Nomor:</span> {{ $invoice->nomor_invoice }}</p>
                    <p><span class="text-slate-400">Tanggal:</span> {{ $invoice->tanggal_invoice->format('d/m/Y') }}</p>
                    <p><span class="text-slate-400">Jatuh Tempo:</span> {{ $invoice->tanggal_jatuh_tempo->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Billing Info -->
        <div class="mb-12">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Ditujukan Kepada:</h3>
            <p class="text-xl font-bold text-slate-900">{{ $invoice->klien->nama_klien }}</p>
            <p class="text-slate-600 mt-1 max-w-xs">
                {!! nl2br(e($invoice->klien->alamat)) !!}
            </p>
            @if($invoice->klien->nama_klien === 'DLH' || str_contains($invoice->klien->nama_klien, 'Dinas Lingkungan Hidup'))
            <p class="mt-4 font-bold text-slate-800">u.p. Kepala Dinas Lingkungan Hidup</p>
            @endif
        </div>

        <!-- Table -->
        <table class="w-full mb-12">
            <thead>
                <tr class="bg-slate-50 border-y border-slate-200 text-left text-xs uppercase tracking-widest font-bold">
                    <th class="px-4 py-4">Deskripsi Layanan</th>
                    <th class="px-4 py-4 text-center">Volume (Ton)</th>
                    <th class="px-4 py-4 text-right">Harga Satuan</th>
                    <th class="px-4 py-4 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                <tr>
                    <td class="px-4 py-6">
                        <p class="font-bold text-base">Jasa Pengelolaan Sampah (Tipping Fee)</p>
                        <p class="text-slate-500 mt-1">Periode Layanan: {{ $periodeLabel }}</p>
                    </td>
                    <td class="px-4 py-6 text-center font-sans">
                        {{ number_format($totalTonnage / 1000, 2, ',', '.') }}
                    </td>
                    <td class="px-4 py-6 text-right font-sans">
                        Rp {{ number_format($invoice->total_tagihan / max(1, $totalTonnage / 1000), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-6 text-right font-bold font-sans">
                        Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" rowspan="3" class="px-4 py-8 align-top italic text-slate-400 text-xs">
                        * Pembayaran dapat dilakukan via transfer ke Rekening {{ $invoice->tenant->bank_name ?: 'Bank Mandiri' }} <br>
                        a/n {{ $invoice->tenant->bank_account_name ?: $invoice->tenant->name }} No. Rek: {{ $invoice->tenant->bank_account_number ?: '123-456-7890' }}
                    </td>
                    <td class="px-4 py-4 text-right font-bold uppercase text-xs tracking-wider text-slate-400">Subtotal</td>
                    <td class="px-4 py-4 text-right font-sans">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="px-4 py-4 text-right font-bold uppercase text-xs tracking-wider text-slate-400">Pajak (0%)</td>
                    <td class="px-4 py-4 text-right font-sans">Rp 0</td>
                </tr>
                <tr class="bg-slate-900 text-white">
                    <td class="px-4 py-4 text-right font-black uppercase text-sm tracking-widest">Grand Total</td>
                    <td class="px-4 py-4 text-right font-black text-xl font-sans tracking-tight">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                        <p class="text-xs uppercase tracking-widest font-bold text-slate-400 mb-1">Terbilang:</p>
                        <p class="text-sm font-bold italic text-slate-700 uppercase">
                            # {{ \App\Helpers\Terbilang::make($invoice->total_tagihan) }} RUPIAH #
                        </p>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Note -->
        @if($invoice->keterangan)
        <div class="mb-12 p-4 bg-slate-50 rounded italic text-slate-600 text-sm border-l-4 border-slate-200">
            <strong>Catatan:</strong> {{ $invoice->keterangan }}
        </div>
        @endif

        <!-- Signatures -->
        <div class="grid grid-cols-3 gap-8 mt-24 text-center text-sm font-sans uppercase tracking-widest">
            <div>
                <p class="mb-20">Penerima / Disetujui Oleh,</p>
                <div class="border-b border-slate-900 w-3/4 mx-auto pb-1"></div>
                <p class="text-[10px] mt-1 text-slate-400">(Cap & Tanda Tangan Klien)</p>
            </div>
            <div>
                <p class="mb-20">Diperiksa Oleh,</p>
                <div class="border-b border-slate-900 w-3/4 mx-auto pb-1 font-bold">{{ $invoice->tenant->finance_name ?: 'Bagian Keuangan' }}</div>
            </div>
            <div>
                <p class="mb-20">Hormat Kami,</p>
                <div class="border-b border-slate-900 w-3/4 mx-auto pb-1 font-bold">{{ $invoice->tenant->director_name ?: 'Direktur' }}</div>
                <p class="text-[10px] mt-1 text-slate-400">{{ $invoice->tenant->name }}</p>
            </div>
        </div>
    </div>

</body>
</html>
