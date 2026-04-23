<?php $__env->startSection('content'); ?>
<style>
    @page { size: landscape; }
    .table th, .table td { font-size: 9px; padding: 4px; }
    .text-center { text-align: center; }
</style>

<div class="text-center mb-4">
    <h2 style="margin:0">DATABASE KARYAWAN LENGKAP</h2>
    <p style="margin:5px 0">Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
</div>

<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="20">No</th>
            <th>Nama Karyawan</th>
            <th>Jabatan</th>
            <th>No. KTP</th>
            <th>Gender</th>
            <th>Tipe Gaji</th>
            <th>Gaji/Upah</th>
            <th>Frekuensi</th>
            <th>Tgl Masuk</th>
            <th>Tgl Keluar</th>
            <th>BPJS</th>
            <th>No. BPJS</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td><strong><?php echo e($emp->name); ?></strong></td>
            <td><?php echo e($emp->position ?? '-'); ?></td>
            <td><?php echo e($emp->ktp_number ?? '-'); ?></td>
            <td><?php echo e($emp->gender ?? '-'); ?></td>
            <td style="text-transform: capitalize;"><?php echo e($emp->salary_type ?? '-'); ?></td>
            <td>
                <?php if($emp->salary_type === 'bulanan'): ?>
                    Rp <?php echo e(number_format($emp->monthly_salary, 0, ',', '.')); ?>

                <?php elseif($emp->salary_type === 'harian'): ?>
                    Rp <?php echo e(number_format($emp->daily_wage, 0, ',', '.')); ?>/hari
                <?php else: ?>
                    Borongan
                <?php endif; ?>
            </td>
            <td><?php echo e($emp->payment_frequency ?? '-'); ?></td>
            <td><?php echo e($emp->joined_at ? \Carbon\Carbon::parse($emp->joined_at)->format('d/m/Y') : '-'); ?></td>
            <td><?php echo e($emp->ended_at ? \Carbon\Carbon::parse($emp->ended_at)->format('d/m/Y') : '-'); ?></td>
            <td class="text-center"><?php echo e($emp->bpjs_status ?? 'Tidak Aktif'); ?></td>
            <td><?php echo e($emp->bpjs_number ?? '-'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Database Karyawan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/hrd/employee/export.blade.php ENDPATH**/ ?>