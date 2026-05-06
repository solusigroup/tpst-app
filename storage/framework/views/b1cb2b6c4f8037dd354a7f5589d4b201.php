<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas - <?php echo e($tenant->name ?? 'TPST'); ?></title>
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
        .report-table th { background-color: #f8fafc; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.1em; color: #64748b; padding: 12px 8px; border-bottom: 2px solid #e2e8f0; }
        .report-table td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; }
        .total-row { background-color: #f8fafc; font-weight: 800; border-top: 2px solid #0f172a; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-slate-900 antialiased p-8">

    <div class="max-w-4xl mx-auto mb-8 flex justify-end gap-3 no-print">
        <button onclick="window.print()" class="px-5 py-2 bg-slate-900 hover:bg-black text-white rounded-lg shadow-sm transition flex items-center gap-2 text-sm font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Cetak Laporan
        </button>
        <button onclick="window.close()" class="px-5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg shadow-sm transition text-sm">
            Tutup
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 shadow-2xl print-container mt-10">
        <!-- Header -->
        <?php if (isset($component)) { $__componentOriginalb7b80f38d0023f8f730a94fb78f032db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7b80f38d0023f8f730a94fb78f032db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kop-surat','data' => ['tenant' => $tenant]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kop-surat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tenant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tenant)]); ?>
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
        <div class="text-center mb-10">
            <h2 class="text-2xl font-black italic tracking-tighter uppercase">Laporan Perubahan Ekuitas</h2>
            <p class="text-sm text-slate-500 font-bold mt-4 uppercase tracking-[0.2em]"><?php echo e($periodLabel); ?></p>
        </div>

        <table class="w-full report-table text-sm">
            <thead>
                <tr>
                    <th class="text-left w-24">Kode</th>
                    <th class="text-left">Keterangan</th>
                    <th class="text-right">Saldo Awal</th>
                    <th class="text-right">Penambahan</th>
                    <th class="text-right">Pengurangan</th>
                    <th class="text-right">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="font-mono text-slate-400 text-xs"><?php echo e($row['kode_akun']); ?></td>
                    <td class="font-bold text-slate-700"><?php echo e($row['nama_akun']); ?></td>
                    <td class="text-right tabular-nums text-slate-600">Rp <?php echo e(number_format($row['saldo_awal'], 0, ',', '.')); ?></td>
                    <td class="text-right tabular-nums text-emerald-600">Rp <?php echo e(number_format($row['penambahan'], 0, ',', '.')); ?></td>
                    <td class="text-right tabular-nums text-rose-600">Rp <?php echo e(number_format($row['pengurangan'], 0, ',', '.')); ?></td>
                    <td class="text-right tabular-nums font-black text-slate-900">Rp <?php echo e(number_format($row['saldo_akhir'], 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-slate-300 italic text-xs">-</td>
                    <td class="font-bold text-slate-700">Laba (Rugi) Bersih Periode Berjalan</td>
                    <td class="text-right text-slate-300">-</td>
                    <td class="text-right tabular-nums <?php echo e($labaRugi >= 0 ? 'text-emerald-600' : 'text-rose-600'); ?>">
                        <?php if($labaRugi >= 0): ?>
                            Rp <?php echo e(number_format($labaRugi, 0, ',', '.')); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="text-right tabular-nums <?php echo e($labaRugi < 0 ? 'text-rose-600' : 'text-slate-300'); ?>">
                        <?php if($labaRugi < 0): ?>
                            Rp <?php echo e(number_format(abs($labaRugi), 0, ',', '.')); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="text-right tabular-nums font-black text-slate-900">Rp <?php echo e(number_format($labaRugi, 0, ',', '.')); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-right py-4 text-xs font-black uppercase tracking-widest text-slate-500">Total Ekuitas Akhir</td>
                    <td class="text-right text-slate-400">Rp <?php echo e(number_format($totalSaldoAwal, 0, ',', '.')); ?></td>
                    <td class="text-right text-emerald-700">Rp <?php echo e(number_format($totalPenambahan + ($labaRugi > 0 ? $labaRugi : 0), 0, ',', '.')); ?></td>
                    <td class="text-right text-rose-700">Rp <?php echo e(number_format($totalPengurangan + ($labaRugi < 0 ? abs($labaRugi) : 0), 0, ',', '.')); ?></td>
                    <td class="text-right text-lg font-black text-slate-900">Rp <?php echo e(number_format($totalSaldoAkhir, 0, ',', '.')); ?></td>
                </tr>
            </tfoot>
        </table>

        <?php if (isset($component)) { $__componentOriginal0777f5ce8f7ff24622b44ea63471a3d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0777f5ce8f7ff24622b44ea63471a3d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.report-signatures','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('report-signatures'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0777f5ce8f7ff24622b44ea63471a3d8)): ?>
<?php $attributes = $__attributesOriginal0777f5ce8f7ff24622b44ea63471a3d8; ?>
<?php unset($__attributesOriginal0777f5ce8f7ff24622b44ea63471a3d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0777f5ce8f7ff24622b44ea63471a3d8)): ?>
<?php $component = $__componentOriginal0777f5ce8f7ff24622b44ea63471a3d8; ?>
<?php unset($__componentOriginal0777f5ce8f7ff24622b44ea63471a3d8); ?>
<?php endif; ?>
    </div>

</body>
</html>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\reports\perubahan-ekuitas.blade.php ENDPATH**/ ?>