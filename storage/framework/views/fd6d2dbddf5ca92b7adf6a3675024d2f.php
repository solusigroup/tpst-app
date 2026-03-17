<!DOCTYPE html>
<html>
<head>
    <title>Rekap Perhitungan Upah</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; padding: 0; }
    </style>
</head>
<body>

<div class="header">
    <h2>Rekapitulasi Perhitungan Upah Karyawan</h2>
    <p>Tanggal Cetak: <?php echo e(\Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d/m/Y H:i')); ?> WIB</p>
</div>

<table>
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th>Karyawan</th>
            <th>Periode Mingguan</th>
            <th class="text-end">Total Output</th>
            <th class="text-end">Total Upah</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $wages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td><?php echo e($item->user->name ?? 'Unknown'); ?></td>
            <td><?php echo e(\Carbon\Carbon::parse($item->week_start)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($item->week_end)->format('d/m/Y')); ?></td>
            <td class="text-end"><?php echo e(number_format($item->total_output, 2, ',', '.')); ?> kg</td>
            <td class="text-end">Rp <?php echo e(number_format($item->total_wage, 2, ',', '.')); ?></td>
            <td>
                <?php if($item->status == 'pending'): ?> Pending
                <?php elseif($item->status == 'approved'): ?> Disetujui
                <?php elseif($item->status == 'paid'): ?> Dibayar
                <?php else: ?> <?php echo e(ucfirst($item->status)); ?>

                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="6" class="text-center">Belum ada data rekapitulasi pada filter ini.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/hrd/wage-calculation/pdf-rekap.blade.php ENDPATH**/ ?>