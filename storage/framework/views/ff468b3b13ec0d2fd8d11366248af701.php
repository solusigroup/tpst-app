<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">LAPORAN PERUBAHAN EKUITAS</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d M Y')); ?></p>
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Akun Ekuitas</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Saldo Awal</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Penambahan</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Pengurangan</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px;"><?php echo e($r['kode_akun']); ?> - <?php echo e($r['nama_akun']); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;"><?php echo e(number_format($r['saldoAwal'], 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #198754;"><?php echo e(number_format($r['penambahan'], 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; color: #dc3545;"><?php echo e(number_format($r['pengurangan'], 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; font-weight: bold;"><?php echo e(number_format($r['saldoAkhir'], 0, ',', '.')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr style="background-color: #f8f9fa;">
            <td style="border: 1px solid #ddd; padding: 5px;">Laba / (Rugi) Bersih Periode Berjalan</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">-</td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; <?php echo e($labaRugi >= 0 ? 'color: #198754;' : ''); ?>"><?php echo e($labaRugi >= 0 ? number_format($labaRugi, 0, ',', '.') : '-'); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; <?php echo e($labaRugi < 0 ? 'color: #dc3545;' : ''); ?>"><?php echo e($labaRugi < 0 ? number_format(abs($labaRugi), 0, ',', '.') : '-'); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right; font-weight: bold; <?php echo e($labaRugi >= 0 ? 'color: #198754;' : 'color: #dc3545;'); ?>"><?php echo e(number_format($labaRugi, 0, ',', '.')); ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold;">TOTAL EKUITAS</td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">Rp <?php echo e(number_format($totalSaldoAwal, 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right; color: #198754;">Rp <?php echo e(number_format($totalPenambahan + ($labaRugi >= 0 ? $labaRugi : 0), 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right; color: #dc3545;">Rp <?php echo e(number_format($totalPengurangan + ($labaRugi < 0 ? abs($labaRugi) : 0), 0, ',', '.')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; font-weight: bold; text-align: right;">Rp <?php echo e(number_format($totalSaldoAkhir, 0, ',', '.')); ?></td>
        </tr>
    </tfoot>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Perubahan Ekuitas'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\exports\perubahan-ekuitas-export.blade.php ENDPATH**/ ?>