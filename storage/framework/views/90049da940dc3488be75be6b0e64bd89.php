<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN PENGANGKUTAN RESIDU</h2>
    <p style="margin:5px 0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
</div>

<table class="table">
    <thead>
        <tr>
            <th width="30">No</th>
            <th>No. Tiket</th>
            <th>Tanggal</th>
            <th>Armada</th>
            <th class="text-end">Bruto (Kg)</th>
            <th class="text-end">Tarra (Kg)</th>
            <th class="text-end">Netto (Kg)</th>
            <th class="text-end">Retribusi</th>
            <th>Tujuan</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td><?php echo e($row->nomor_tiket); ?></td>
            <td><?php echo e($row->tanggal->format('d/m/Y')); ?></td>
            <td><?php echo e($row->armada->plat_nomor); ?></td>
            <td class="text-end"><?php echo e(number_format($row->berat_bruto, 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($row->berat_tarra, 0, ',', '.')); ?></td>
            <td class="text-end fw-bold"><?php echo e(number_format($row->berat_netto, 0, ',', '.')); ?></td>
            <td class="text-end">Rp <?php echo e(number_format($row->biaya_retribusi, 0, ',', '.')); ?></td>
            <td><?php echo e($row->tujuan); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold" style="background-color: #f8f9fa;">
        <tr>
            <td colspan="6" class="text-end">TOTAL</td>
            <td class="text-end"><?php echo e(number_format($totals->total_netto, 0, ',', '.')); ?></td>
            <td class="text-end">Rp <?php echo e(number_format($totals->total_biaya, 0, ',', '.')); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top: 30px;">
    <table class="table-borderless" style="width: 100%;">
        <tr>
            <td width="70%"></td>
            <td class="text-center">
                <p>Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
                <div style="margin-top: 60px;">
                    <p><b>( ____________________ )</b></p>
                    <p>&nbsp;</p>
                </div>
            </td>
        </tr>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Laporan Pengangkutan Residu'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\exports\residu-export.blade.php ENDPATH**/ ?>