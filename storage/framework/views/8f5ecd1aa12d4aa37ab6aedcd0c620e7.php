<!DOCTYPE html>
<html>
<head>
    <title>Data Klien</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Klien</h2>
        <p>Tanggal Cetak: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Klien</th>
                <th>Jenis</th>
                <th>Jenis Tarif</th>
                <th>Besaran Tarif</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th>Dibuat Pada</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($item->nama_klien); ?></td>
                <td><?php echo e($item->jenis); ?></td>
                <td><?php echo e($item->jenis_tarif ?? '-'); ?></td>
                <td><?php echo e($item->besaran_tarif ? 'Rp ' . number_format($item->besaran_tarif, 0, ',', '.') : '-'); ?></td>
                <td><?php echo e($item->kontak ?? '-'); ?></td>
                <td><?php echo e($item->alamat ?? '-'); ?></td>
                <td><?php echo e($item->created_at?->format('d/m/Y H:i')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\klien\export.blade.php ENDPATH**/ ?>