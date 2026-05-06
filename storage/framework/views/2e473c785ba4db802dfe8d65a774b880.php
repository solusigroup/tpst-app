<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">NERACA SALDO</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d M Y')); ?></p>
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px;">Kode Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px;">Nama Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Total Debit</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Total Kredit</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px;"><?php echo e($row->kode_akun); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px;"><?php echo e($row->nama_akun); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;"><?php echo e(number_format($row->total_debit, 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;"><?php echo e(number_format($row->total_kredit, 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; font-weight: bold;">
                <?php echo e(number_format($row->saldo, 0, ',', '.')); ?>

            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">TOTAL</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;"><?php echo e(number_format($totalDebit, 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;"><?php echo e(number_format($totalKredit, 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">
                <?php echo e(number_format($totalDebit - $totalKredit, 0, ',', '.')); ?>

            </td>
        </tr>
    </tfoot>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Neraca Saldo'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\exports\neraca-saldo-export.blade.php ENDPATH**/ ?>