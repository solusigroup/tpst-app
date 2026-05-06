<?php
    $isExport = $isExport ?? false;
    $groupedRecords = $records->groupBy('klien_id');
?>

<?php if(!$isExport): ?>
    <div class="space-y-12">
<?php else: ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
            .container { padding: 30px; }
            .invoice-page { page-break-after: always; }
            .invoice-page:last-child { page-break-after: auto; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .mb-2 { margin-bottom: 8px; }
            .mb-4 { margin-bottom: 16px; }
            .mb-8 { margin-bottom: 32px; }
            .border-b { border-bottom: 1px solid #eee; }
            .border-b-2 { border-bottom: 2px solid #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #f9fafb; text-align: left; padding: 8px; border: 1px solid #e5e7eb; font-weight: bold; text-transform: uppercase; font-size: 9px; color: #6b7280; }
            td { padding: 8px; border: 1px solid #e5e7eb; }
            .flex { display: flex; }
            .justify-between { justify-content: space-between; }
            .mt-12 { margin-top: 48px; }
            .w-full { width: 100%; }
            .signature-table { margin-top: 60px; border: none; width: 100%; }
            .signature-table td { border: none; text-align: center; padding: 0; vertical-align: top; width: 33.33%; }
            .signature-box { height: 60px; }
            .signature-line { border-top: 1px solid #333; width: 80%; margin: 0 auto; margin-top: 5px; }
            @media print {
                .no-print { display: none; }
                .invoice-page { page-break-after: always; }
            }
        </style>
    </head>
    <body class="container">
<?php endif; ?>

    <?php $__empty_1 = true; $__currentLoopData = $groupedRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $klienId => $clientRecords): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php 
            $currentKlien = $clientRecords->first()->klien;
            $totalNetto = $clientRecords->sum('berat_netto');
            $totalBiaya = $clientRecords->sum('biaya_tipping');
        ?>
        
        <div class="invoice-page <?php if(!$isExport): ?> p-8 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8 <?php endif; ?>">
            
            <?php if(!$isExport): ?>
                <?php if (isset($component)) { $__componentOriginalb7b80f38d0023f8f730a94fb78f032db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7b80f38d0023f8f730a94fb78f032db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kop-surat','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kop-surat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7b80f38d0023f8f730a94fb78f032db)): ?>
<?php $attributes = $__attributesOriginalb7b80f38d0023f8f730a94fb78f032db; ?>
<?php unset($__attributesOriginalb7b80f38d0023f8f730a94fb78f032db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7b80f38d0023f8f730a94fb78f032db)): ?>
<?php $component = $__componentOriginalb7b80f38d0023f8f730a94fb78f032db; ?>
<?php unset($__componentOriginalb7b80f38d0023f8f730a94fb78f032db); ?>
<?php endif; ?>
            <?php else: ?>
                <div style="margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; text-align: center;">
                    <h1 style="font-size: 20px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 2px;">TPST TATABUMI</h1>
                    <p style="font-size: 11px; color: #666; margin: 5px 0 0 0;">
                        Jl. Tambakboyo No. 123, Kelurahan Tambakboyo, Kecamatan Tambakboyo, Kabupaten Lamongan 12345<br>
                        Telp: (021) 123-4567 | Email: info@tpst-app.com | Web: www.tpst-app.com
                    </p>
                </div>
            <?php endif; ?>

            <div class="text-center mb-8">
                <h2 style="font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0;">INVOICE TAGIHAN RITASE</h2>
                <p style="font-size: 12px; color: #666; margin-top: 2px;">KLIEN: <?php echo e($currentKlien->nama_klien); ?></p>
            </div>

            <div style="margin-bottom: 20px;">
                <table style="border: none; margin-top: 0; width: 100%;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0; width: 100px; font-weight: bold;">ALAMAT</td>
                        <td style="border: none; padding: 2px 0; width: 20px;">:</td>
                        <td style="border: none; padding: 2px 0;"><?php echo e($currentKlien->alamat ?? '-'); ?></td>
                        
                        <td style="border: none; padding: 2px 0; width: 100px; font-weight: bold;">PERIODE</td>
                        <td style="border: none; padding: 2px 0; width: 20px;">:</td>
                        <td style="border: none; padding: 2px 0;">
                            <?php echo e(\Carbon\Carbon::parse($dari)->translatedFormat('d F Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->translatedFormat('d F Y')); ?>

                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0; font-weight: bold;">TELEPON</td>
                        <td style="border: none; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0;"><?php echo e($currentKlien->telepon ?? '-'); ?></td>
                        
                        <td style="border: none; padding: 2px 0; font-weight: bold;">TGL CETAK</td>
                        <td style="border: none; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0;"><?php echo e(now()->translatedFormat('d F Y')); ?></td>
                    </tr>
                </table>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="text-center" style="width: 30px;">NO</th>
                        <th class="text-center">TANGGAL</th>
                        <th class="text-center">NO. TIKET</th>
                        <th class="text-center">ARMADA</th>
                        <th class="text-center">ASAL SAMPAH</th>
                        <th class="text-right">NETTO (KG)</th>
                        <th class="text-right">BIAYA (RP)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $clientRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-center"><?php echo e($index + 1); ?></td>
                        <td class="text-center"><?php echo e($record->waktu_masuk?->format('d/m/Y')); ?></td>
                        <td class="text-center"><?php echo e($record->nomor_tiket); ?></td>
                        <td class="text-center"><?php echo e($record->armada->plat_nomor); ?></td>
                        <td class="text-center"><?php echo e($record->jenis_sampah ?? '-'); ?></td>
                        <td class="text-right"><?php echo e(number_format($record->berat_netto, 0, ',', '.')); ?></td>
                        <td class="text-right"><?php echo e(number_format($record->biaya_tipping, 0, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #f9fafb; font-weight: bold;">
                        <td colspan="5" class="text-right py-2 px-4 uppercase">TOTAL TAGIHAN</td>
                        <td class="text-right py-2 px-4"><?php echo e(number_format($totalNetto, 0, ',', '.')); ?> KG</td>
                        <td class="text-right py-2 px-4">Rp <?php echo e(number_format($totalBiaya, 0, ',', '.')); ?></td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 30px;">
                <p style="font-weight: bold; margin-bottom: 5px; font-size: 10px;">Terbilang:</p>
                <P style="font-style: italic; background-color: #f3f4f6; padding: 8px; border-radius: 4px; font-size: 11px;">
                    <?php echo e(ucwords(\App\Helpers\Terbilang::make($totalBiaya))); ?> Rupiah
                </p>
            </div>

            <table class="signature-table">
                <tr>
                    <td>
                        <p>Hormat Kami,</p>
                        <p style="margin-top: 5px; font-weight: bold;">TPST TATABUMI</p>
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <p>( Bagian Keuangan )</p>
                    </td>
                    <td></td>
                    <td>
                        <p>Penerima / Klien,</p>
                        <p style="margin-top: 5px; font-weight: bold;"><?php echo e($currentKlien->nama_klien); ?></p>
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <p>( ................................ )</p>
                    </td>
                </tr>
            </table>

            <?php if(!$isExport): ?>
                <div class="mt-8 pt-4 border-t border-gray-100 flex justify-end gap-3 no-print">
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path></svg>
                        Print Halaman Ini
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="p-8 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 text-center italic">
            Tidak ada data ritase dalam periode ini.
        </div>
    <?php endif; ?>

<?php if(!$isExport): ?>
    </div>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\ritase\invoice-global.blade.php ENDPATH**/ ?>