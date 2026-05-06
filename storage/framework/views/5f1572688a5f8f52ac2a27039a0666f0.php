<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN PENJUALAN</h2>
    <p style="margin:5px 0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>Klien</th>
            <th>Jenis Produk</th>
            <th class="text-end">Berat (kg)</th>
            <th class="text-end">Harga Satuan</th>
            <th class="text-end">Total Harga</th>
            <th>Status Invoice</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td><?php echo e(\Carbon\Carbon::parse($r->tanggal)->format('d/m/Y')); ?></td>
            <td><?php echo e($r->klien->nama_klien ?? '-'); ?></td>
            <td><?php echo e($r->jenis_produk); ?></td>
            <td class="text-end"><?php echo e(number_format($r->berat_kg, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($r->harga_satuan, 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($r->total_harga, 0, ',', '.')); ?></td>
            <td><?php echo e($r->status_invoice ?? 'Unbilled'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold">
        <tr>
            <td colspan="4" class="text-end">TOTAL</td>
            <td class="text-end"><?php echo e(number_format($totals->total_berat ?? 0, 2, ',', '.')); ?></td>
            <td></td>
            <td class="text-end">Rp <?php echo e(number_format($totals->total_harga ?? 0, 0, ',', '.')); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Laporan Penjualan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\exports\penjualan-export.blade.php ENDPATH**/ ?>