<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo e($invoice->nomor_invoice); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                -webkit-print-color-adjust: exact;
            }

            .print-container {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }
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
        <button onclick="window.print()"
            class="px-6 py-2 bg-slate-900 text-white rounded-lg shadow hover:bg-slate-800 transition flex items-center gap-2 font-sans font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Invoice
        </button>
        <button onclick="window.close()"
            class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition font-sans">
            Tutup
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 shadow-xl print-container">
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-8 mb-10">
            <div>
                <h1 class="text-2xl font-black uppercase text-slate-900"><?php echo e($invoice->tenant->name); ?></h1>
                <p class="text-sm text-slate-600 mt-2 max-w-xs leading-relaxed">
                    <?php echo nl2br(e($invoice->tenant->address)); ?><br>
                    <?php if($invoice->tenant->email): ?> Email: <?php echo e($invoice->tenant->email); ?> <?php endif; ?>
                </p>
            </div>
            <div class="text-right">
                <h2 class="text-5xl font-black text-slate-200 tracking-tighter uppercase mb-4">INVOICE</h2>
                <div class="space-y-1 text-sm font-sans uppercase tracking-wider font-bold">
                    <p><span class="text-slate-400">Nomor:</span> <?php echo e($invoice->nomor_invoice); ?></p>
                    <p><span class="text-slate-400">Tanggal:</span> <?php echo e($invoice->tanggal_invoice->format('d/m/Y')); ?></p>
                    <p><span class="text-slate-400">Jatuh Tempo:</span>
                        <?php echo e($invoice->tanggal_jatuh_tempo->format('d/m/Y')); ?></p>
                </div>
            </div>
        </div>

        <!-- Billing Info -->
        <div class="mb-12">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Ditujukan Kepada:</h3>
            <p class="text-xl font-bold text-slate-900"><?php echo e($invoice->klien->nama_klien); ?></p>
            <p class="text-slate-600 mt-1 max-w-xs">
                <?php echo nl2br(e($invoice->klien->alamat)); ?>

            </p>
            <?php if($invoice->klien->nama_klien === 'DLH' || str_contains($invoice->klien->nama_klien, 'Dinas Lingkungan Hidup')): ?>
                <p class="mt-4 font-bold text-slate-800">u.p. Kepala Dinas Lingkungan Hidup</p>
            <?php endif; ?>
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
                <?php $hasItems = false; ?>

                <!-- Ritase Section -->
                <?php if($invoice->ritase->count() > 0): ?>
                    <?php $hasItems = true; ?>
                    <tr>
                        <td class="px-4 py-6">
                            <p class="font-bold text-base text-slate-900 uppercase">Jasa Pengelolaan Sampah (Tipping Fee)
                            </p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider font-bold">Rincian Tiket:</p>
                            <div class="mt-2 grid grid-cols-2 gap-x-8 gap-y-1">
                                <?php $__currentLoopData = $invoice->ritase; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div
                                        class="text-[10px] text-slate-500 font-sans flex justify-between border-b border-slate-50 pb-1">
                                        <span><?php echo e($r->nomor_tiket); ?> (<?php echo e($r->waktu_masuk->format('d/m/Y')); ?>)</span>
                                        <span class="font-bold text-slate-400">
                                            <?php echo e(number_format($r->berat_netto / 1000, 2, ',', '.')); ?> Ton
                                            <span class="ml-2 text-slate-500 inline-block min-w-[80px] text-right">Rp
                                                <?php echo e(number_format($r->biaya_tipping, 0, ',', '.')); ?></span>
                                        </span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="text-slate-400 mt-4 text-[10px] font-bold italic uppercase">Periode Layanan:
                                <?php echo e($periodeLabel); ?> (<?php echo e($invoice->ritase->count()); ?> Ritase)</p>
                        </td>
                        <td class="px-4 py-6 text-center font-sans align-top text-slate-600">
                            <?php echo e(number_format($totalTonnageRitase / 1000, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-6 text-right font-sans align-top text-slate-600">
                            Rp
                            <?php echo e(number_format($invoice->ritase->sum('biaya_tipping') / max(1, $totalTonnageRitase / 1000), 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-6 text-right font-bold font-sans align-top text-slate-900">
                            Rp <?php echo e(number_format($invoice->ritase->sum('biaya_tipping'), 0, ',', '.')); ?>

                        </td>
                    </tr>
                <?php endif; ?>

                <!-- Penjualan Section -->
                <?php if($invoice->penjualan->count() > 0): ?>
                    <?php $hasItems = true; ?>
                    <tr>
                        <td class="px-4 py-6">
                            <p class="font-bold text-base text-slate-900 uppercase">Penjualan Hasil Pilahan</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider font-bold">Rincian Produk:
                            </p>
                            <div class="mt-2 space-y-1 max-w-md">
                                <?php $__currentLoopData = $invoice->penjualan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div
                                        class="text-[10px] text-slate-500 font-sans flex justify-between border-b border-slate-50 pb-1">
                                        <span><?php echo e($p->jenis_produk); ?> (<?php echo e($p->tanggal->format('d/m/Y')); ?>)</span>
                                        <span class="font-bold text-slate-400">
                                            <?php echo e(number_format($p->berat_kg, 1, ',', '.')); ?> kg
                                            <span class="ml-2 text-slate-500 inline-block min-w-[80px] text-right">Rp
                                                <?php echo e(number_format($p->total_harga, 0, ',', '.')); ?></span>
                                        </span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="text-slate-400 mt-4 text-[10px] font-bold italic uppercase">Periode:
                                <?php echo e($periodeLabel); ?></p>
                        </td>
                        <td class="px-4 py-6 text-center font-sans align-top text-slate-600">
                            <?php echo e(number_format($totalTonnagePenjualan / 1000, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-6 text-right font-sans align-top text-slate-600">
                            Rp
                            <?php echo e(number_format($invoice->penjualan->sum('total_harga') / max(1, $totalTonnagePenjualan / 1000), 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-6 text-right font-bold font-sans align-top text-slate-900">
                            Rp <?php echo e(number_format($invoice->penjualan->sum('total_harga'), 0, ',', '.')); ?>

                        </td>
                    </tr>
                <?php endif; ?>

                <!-- Custom Description Section -->
                <?php if($invoice->deskripsi_layanan): ?>
                    <tr class="bg-slate-50/30">
                        <td class="px-4 py-8" colspan="<?php echo e($hasItems ? 4 : 3); ?>">
                            <p class="text-[10px] uppercase tracking-widest font-black text-slate-400 mb-3">Informasi
                                Tambahan / Deskripsi Layanan:</p>
                            <div class="font-bold text-lg text-slate-800 leading-relaxed max-w-2xl">
                                <?php echo nl2br(e($invoice->deskripsi_layanan)); ?>

                            </div>
                            <?php if(!$hasItems): ?>
                                <p class="text-slate-500 mt-2 text-xs font-bold italic">PERIODE LAYANAN: <?php echo e($periodeLabel); ?></p>
                            <?php endif; ?>
                        </td>
                        <?php if(!$hasItems): ?>
                            <td class="px-4 py-8 text-right font-black font-sans align-top text-slate-900 text-base">
                                Rp <?php echo e(number_format($invoice->total_tagihan, 0, ',', '.')); ?>

                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" rowspan="3" class="px-4 py-8 align-top">
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                            <p class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-2">Instruksi Pembayaran:</p>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Silakan melakukan pembayaran melalui transfer ke rekening berikut:
                            </p>
                            <div class="mt-2 text-sm text-slate-800">
                                <p>Bank: <strong class="uppercase"><?php echo e($invoice->tenant->bank_name ?: '-'); ?></strong></p>
                                <p>No. Rekening: <strong><?php echo e($invoice->tenant->bank_account_number ?: '-'); ?></strong></p>
                                <p>Atas Nama: <strong><?php echo e($invoice->tenant->bank_account_name ?: $invoice->tenant->name); ?></strong></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-right font-bold uppercase text-xs tracking-wider text-slate-400">Subtotal
                    </td>
                    <td class="px-4 py-4 text-right font-sans">Rp
                        <?php echo e(number_format($invoice->total_tagihan, 0, ',', '.')); ?></td>
                </tr>
                <tr>
                    <td class="px-4 py-4 text-right font-bold uppercase text-xs tracking-wider text-slate-400">Total
                    </td>
                    <td class="px-4 py-4 text-right font-sans">Rp
                        <?php echo e(number_format($invoice->total_tagihan, 0, ',', '.')); ?></td>
                </tr>
                <tr>
                    <td class="px-4 py-4 text-right font-bold uppercase text-xs tracking-wider text-slate-400">Uang Muka
                        / DP</td>
                    <td class="px-4 py-4 text-right font-sans text-red-600">- Rp
                        <?php echo e(number_format($invoice->uang_muka, 0, ',', '.')); ?></td>
                </tr>
                <tr class="bg-slate-900 text-white font-sans">
                    <td class="px-4 py-4 text-right font-black uppercase text-sm tracking-widest">Sisa Tagihan
                        (Pelunasan)</td>
                    <td class="px-4 py-4 text-right font-black text-xl tracking-tight">Rp
                        <?php echo e(number_format($invoice->total_tagihan - $invoice->uang_muka, 0, ',', '.')); ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                        <p class="text-xs uppercase tracking-widest font-bold text-slate-400 mb-1">Terbilang (Sisa
                            Pelunasan):</p>
                        <p class="text-sm font-bold italic text-slate-700 uppercase">
                            # <?php echo e(\App\Helpers\Terbilang::make($invoice->total_tagihan - $invoice->uang_muka)); ?> RUPIAH #
                        </p>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Note -->
        <?php if($invoice->keterangan): ?>
            <div class="mb-12 p-4 bg-slate-50 rounded italic text-slate-600 text-sm border-l-4 border-slate-200">
                <strong>Catatan:</strong> <?php echo e($invoice->keterangan); ?>

            </div>
        <?php endif; ?>

        <!-- Signatures -->
        <div class="grid grid-cols-3 gap-8 mt-24 text-center text-sm font-sans uppercase tracking-widest">
            <div>
                <p class="mb-20">Penerima / Disetujui Oleh,</p>
                <div class="border-b border-slate-900 w-3/4 mx-auto pb-1"></div>
                <p class="text-[10px] mt-1 text-slate-400">(Cap & Tanda Tangan Klien)</p>
            </div>
            <div>
                <p class="mb-20">Diperiksa Oleh,</p>
                <div class="border-b border-slate-900 w-3/4 mx-auto pb-1 font-bold">
                    <?php echo e($invoice->tenant->finance_name ?: 'Bagian Keuangan'); ?></div>
            </div>
            <div>
                <p class="mb-20">Hormat Kami,</p>
                <div class="border-b border-slate-900 w-3/4 mx-auto pb-1 font-bold">
                    <?php echo e($invoice->tenant->director_name ?: 'Direktur'); ?></div>
                <p class="text-[10px] mt-1 text-slate-400"><?php echo e($invoice->tenant->name); ?></p>
            </div>
        </div>
    </div>

</body>

</html><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/invoices/print.blade.php ENDPATH**/ ?>