<?php
    $isExport = $isExport ?? false;
?>

<?php if(!$isExport): ?>
    <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
<?php else: ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
            .container { padding: 30px; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .mb-2 { margin-bottom: 8px; }
            .mb-4 { margin-bottom: 16px; }
            .mb-8 { margin-bottom: 32px; }
            .border-b { border-bottom: 1px solid #eee; }
            .border-b-2 { border-bottom: 2px solid #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #f9fafb; text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: bold; text-transform: uppercase; font-size: 10px; color: #6b7280; }
            td { padding: 10px; border-bottom: 1px solid #f3f4f6; }
            .flex { display: flex; }
            .justify-between { justify-content: space-between; }
            .mt-12 { margin-top: 48px; }
            .w-full { width: 100%; }
            .signature-table { margin-top: 60px; border: none; }
            .signature-table td { border: none; text-align: center; padding: 0; vertical-align: top; width: 33.33%; }
            .signature-box { height: 80px; }
            .signature-line { border-top: 1px solid #333; width: 80%; margin: 0 auto; margin-top: 5px; }
            @media print {
                .no-print { display: none; }
            }
        </style>
    </head>
    <body class="container">
<?php endif; ?>

    <div class="report-content">
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
                <p style="font-size: 12px; color: #666; margin: 5px 0 0 0;">
                    Jl. Tambakboyo No. 123, Kelurahan Tambakboyo, Kecamatan Tambakboyo, Kabupaten Lamongan 12345<br>
                    Telp: (021) 123-4567 | Email: info@tpst-app.com | Web: www.tpst-app.com
                </p>
            </div>
        <?php endif; ?>

        <div class="text-center mb-8">
            <h2 style="font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0;">INVOICE RITASE</h2>
            <p style="font-size: 14px; color: #666; margin-top: 5px;">NO: <?php echo e($ritase->nomor_tiket); ?></p>
        </div>

        <div style="margin-bottom: 30px;">
            <table style="border: none; margin-top: 0;">
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0; width: 120px; font-weight: bold;">KLIEN</td>
                    <td style="border: none; padding: 2px 0; width: 20px;">:</td>
                    <td style="border: none; padding: 2px 0;"><?php echo e($ritase->klien->nama_klien); ?></td>
                    
                    <td style="border: none; padding: 2px 0; width: 120px; font-weight: bold;">WAKTU MASUK</td>
                    <td style="border: none; padding: 2px 0; width: 20px;">:</td>
                    <td style="border: none; padding: 2px 0;"><?php echo e($ritase->waktu_masuk?->format('d/m/Y H:i')); ?></td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0; font-weight: bold;">ARMADA</td>
                    <td style="border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0;"><?php echo e($ritase->armada->plat_nomor); ?> (<?php echo e($ritase->armada->jenis_kendaraan ?? '-'); ?>)</td>
                    
                    <td style="border: none; padding: 2px 0; font-weight: bold;">WAKTU KELUAR</td>
                    <td style="border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0;"><?php echo e($ritase->waktu_keluar?->format('d/m/Y H:i') ?? '-'); ?></td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0; font-weight: bold;">ASAL SAMPAH</td>
                    <td style="border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0;"><?php echo e($ritase->jenis_sampah ?? '-'); ?></td>
                    
                    <td style="border: none; padding: 2px 0; font-weight: bold;">STATUS</td>
                    <td style="border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0; text-transform: uppercase;"><?php echo e($ritase->status); ?></td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th>DESKRIPSI PENGUKURAN</th>
                    <th class="text-right">BERAT (KG)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>BERAT BRUTO (KOTOR)</td>
                    <td class="text-right"><?php echo e(number_format($ritase->berat_bruto, 0, ',', '.')); ?></td>
                </tr>
                <tr>
                    <td>BERAT TARRA (KENDARAAN)</td>
                    <td class="text-right"><?php echo e(number_format($ritase->berat_tarra, 0, ',', '.')); ?></td>
                </tr>
                <tr style="background-color: #f9fafb; font-weight: bold;">
                    <td>BERAT NETTO (BERSIH)</td>
                    <td class="text-right"><?php echo e(number_format($ritase->berat_netto, 0, ',', '.')); ?></td>
                </tr>
                <tr style="border-top: 2px solid #eee;">
                    <td style="font-weight: bold; font-size: 14px;">BIAYA TIPPING (TOTAL)</td>
                    <td class="text-right" style="font-weight: bold; font-size: 14px;">Rp <?php echo e(number_format($ritase->biaya_tipping, 0, ',', '.')); ?></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 40px;">
            <p style="font-weight: bold; margin-bottom: 5px;">Terbilang:</p>
            <p style="font-style: italic; background-color: #f3f4f6; padding: 10px; border-radius: 4px;">
                
                <?php echo e(ucwords(\App\Helpers\Terbilang::make($ritase->biaya_tipping))); ?> Rupiah
            </p>
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    <p>Petugas Timbang,</p>
                    <div class="signature-box"></div>
                    <div class="signature-line"></div>
                    <p>( ................................ )</p>
                </td>
                <td>
                    <p>Sopir Armada,</p>
                    <div class="signature-box"></div>
                    <div class="signature-line"></div>
                    <p>( <?php echo e($ritase->armada->nama_supir ?? '................................'); ?> )</p>
                </td>
                <td>
                    <p>Admin / Kasir,</p>
                    <div class="signature-box"></div>
                    <div class="signature-line"></div>
                    <p>( <?php echo e(auth()->user()->name ?? '................................'); ?> )</p>
                </td>
            </tr>
        </table>

        <?php if(!$isExport): ?>
            <div class="mt-8 pt-4 border-t border-gray-100 flex justify-end gap-3 no-print">
                <button onclick="window.print()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path></svg>
                    Print
                </button>
            </div>
        <?php endif; ?>
    </div>

<?php if(!$isExport): ?>
    </div>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\ritase\invoice.blade.php ENDPATH**/ ?>