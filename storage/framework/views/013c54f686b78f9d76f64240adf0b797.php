<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN RITASE</h2>
    <p style="margin:5px 0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
</div>

<?php if(isset($rekapJenis) && count($rekapJenis) > 0): ?>
<table class="table" style="width: 50%; margin-bottom: 20px;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th>Jenis Armada</th>
            <th class="text-center">Ritase</th>
            <th class="text-end">Tonase (kg)</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rekapJenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($rj->jenis_armada ?? 'N/A'); ?></td>
            <td class="text-center"><?php echo e(number_format($rj->total_ritase, 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($rj->total_netto, 2, ',', '.')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td>TOTAL REKAP</td>
            <td class="text-center"><?php echo e(number_format($rekapJenis->sum('total_ritase'), 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($rekapJenis->sum('total_netto'), 2, ',', '.')); ?></td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>No Tiket</th>
            <th>Armada</th>
            <th>Jenis Armada</th>
            <th>Klien</th>
            <th class="text-end">Berat Netto (kg)</th>
            <th class="text-end">Biaya Tipping</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td><?php echo e(\Carbon\Carbon::parse($r->waktu_masuk)->format('d/m/Y')); ?></td>
            <td><?php echo e($r->nomor_tiket); ?></td>
            <td><?php echo e($r->armada->plat_nomor ?? '-'); ?></td>
            <td><?php echo e($r->armada->jenis_armada ?? '-'); ?></td>
            <td><?php echo e($r->klien->nama_klien ?? '-'); ?></td>
            <td class="text-end"><?php echo e(number_format($r->berat_netto, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($r->biaya_tipping, 0, ',', '.')); ?></td>
            <td><?php echo e(ucfirst($r->status)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td colspan="6" class="text-end">TOTAL KESELURUHAN</td>
            <td class="text-end"><?php echo e(number_format($totals->total_netto ?? 0, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($totals->total_tipping ?? 0, 0, ',', '.')); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Laporan Ritase'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/exports/ritase-export.blade.php ENDPATH**/ ?>