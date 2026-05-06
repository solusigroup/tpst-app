<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="margin:0">LAPORAN KEHADIRAN KARYAWAN</h2>
    <p style="margin:5px 0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
</div>

<div style="margin-bottom: 20px;">
    <strong>Ringkasan Status:</strong><br>
    Hadir: <?php echo e($totals->present); ?> | Alpa: <?php echo e($totals->absent); ?> | Sakit: <?php echo e($totals->sick); ?> | Izin: <?php echo e($totals->leave); ?> | Total: <?php echo e($totals->total_rows); ?>

</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>Nama Karyawan</th>
            <th>Status</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td><?php echo e(\Carbon\Carbon::parse($r->attendance_date)->format('d/m/Y')); ?></td>
            <td><?php echo e($r->user->name ?? '-'); ?></td>
            <td><?php echo e(ucfirst($r->status)); ?></td>
            <td><?php echo e($r->clock_in ? \Carbon\Carbon::parse($r->clock_in)->format('H:i') : '-'); ?></td>
            <td><?php echo e($r->clock_out ? \Carbon\Carbon::parse($r->clock_out)->format('H:i') : '-'); ?></td>
            <td><?php echo e($r->notes ?? '-'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Laporan Kehadiran Karyawan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\exports\attendance-export.blade.php ENDPATH**/ ?>