<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN PERHITUNGAN UPAH KARYAWAN</h2>
    <p style="margin:5px 0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
</div>

<div style="margin-bottom: 20px;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Parameter Laporan:</strong><br>
                Skema Upah: <?php echo e($skemaUpah ?: 'Semua'); ?><br>
                Total Record: <?php echo e($totals->total_rows); ?>

            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <strong>Ringkasan Keuangan:</strong><br>
                Total Upah: Rp <?php echo e(number_format($totals->total_wage, 0, ',', '.')); ?><br>
                Sudah Dibayar: <span style="color: green;">Rp <?php echo e(number_format($totals->total_paid, 0, ',', '.')); ?></span><br>
                Belum Dibayar: <span style="color: red;">Rp <?php echo e(number_format($totals->total_unpaid, 0, ',', '.')); ?></span>
            </td>
        </tr>
    </table>
</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Periode</th>
            <th>Nama Karyawan</th>
            <th class="text-center">H</th>
            <th class="text-center">S/I</th>
            <th class="text-center">A</th>
            <th>Skema</th>
            <th class="text-right">Total Upah</th>
            <th class="text-right">Sdh Dibayar</th>
            <th class="text-right">Blm Dibayar</th>
            <th class="text-center">Status</th>
            <th>Tgl Bayar</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td style="font-size: 10px;"><?php echo e(\Carbon\Carbon::parse($r->week_start)->format('d/m/y')); ?>-<?php echo e(\Carbon\Carbon::parse($r->week_end)->format('d/m/y')); ?></td>
            <td><?php echo e($r->user->name ?? '-'); ?></td>
            <td class="text-center"><?php echo e($r->stats->hadir ?? 0); ?></td>
            <td class="text-center"><?php echo e(($r->stats->sakit ?? 0) + ($r->stats->izin ?? 0)); ?></td>
            <td class="text-center"><?php echo e($r->stats->mangkir ?? 0); ?></td>
            <td style="text-transform: capitalize;"><?php echo e($r->user->salary_type ?? '-'); ?></td>
            <td class="text-right">Rp <?php echo e(number_format($r->total_wage, 0, ',', '.')); ?></td>
            <td class="text-right">
                <?php echo e($r->status === 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-'); ?>

            </td>
            <td class="text-right">
                <?php echo e($r->status !== 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-'); ?>

            </td>
            <td class="text-center"><?php echo e(ucfirst($r->status)); ?></td>
            <td><?php echo e($r->paid_date ? \Carbon\Carbon::parse($r->paid_date)->format('d/m/Y') : '-'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <td colspan="7" class="text-right">TOTAL</td>
            <td class="text-right">Rp <?php echo e(number_format($totals->total_wage, 0, ',', '.')); ?></td>
            <td class="text-right">Rp <?php echo e(number_format($totals->total_paid, 0, ',', '.')); ?></td>
            <td class="text-right">Rp <?php echo e(number_format($totals->total_unpaid, 0, ',', '.')); ?></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Laporan Upah Karyawan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/exports/upah-export.blade.php ENDPATH**/ ?>