<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN HASIL PILAHAN</h2>
    <p style="margin:5px 0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
</div>

<h3 style="margin-bottom: 10px;">Ringkasan Stok Pilahan</h3>
<table class="table" style="margin-bottom: 30px;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th>Kategori</th>
            <th>Jenis</th>
            <th class="text-end">Total Pilahan (kg)</th>
            <th class="text-end">Terjual (kg)</th>
            <th class="text-end">Sisa Stok (kg)</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $stokSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($stok->kategori); ?></td>
            <td><?php echo e($stok->jenis); ?></td>
            <td class="text-end"><?php echo e(number_format($stok->total_pilahan, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($stok->total_terjual, 2, ',', '.')); ?></td>
            <td class="text-end fw-bold"><?php echo e(number_format($stok->sisa_stok, 2, ',', '.')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td colspan="2" class="text-end">TOTAL KESELURUHAN</td>
            <td class="text-end"><?php echo e(number_format($summaryTotals->total_pilahan, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($summaryTotals->total_terjual, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($summaryTotals->sisa_stok, 2, ',', '.')); ?></td>
        </tr>
    </tfoot>
</table>

<h3 style="margin-bottom: 10px;">Riwayat Log Harian</h3>
<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Jenis</th>
            <th>Petugas</th>
            <th class="text-end">Tonase (kg)</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td><?php echo e(\Carbon\Carbon::parse($r->tanggal)->format('d/m/Y')); ?></td>
            <td><?php echo e($r->kategori); ?></td>
            <td><?php echo e($r->jenis); ?></td>
            <td><?php echo e($r->officer); ?></td>
            <td class="text-end"><?php echo e(number_format($r->tonase, 2, ',', '.')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td colspan="5" class="text-end">TOTAL LOG</td>
            <td class="text-end"><?php echo e(number_format($totals->total_tonase ?? 0, 2, ',', '.')); ?></td>
        </tr>
    </tfoot>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Laporan Hasil Pilahan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\exports\hasil-pilahan-export.blade.php ENDPATH**/ ?>