<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="font-size: 16px; margin: 0; font-weight: bold;">BUKU BESAR</h2>
    <p style="margin: 5px 0 0 0; color: #555;">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d M Y')); ?></p>
    <?php if($coaId): ?>
        <p style="margin: 5px 0 0 0; color: #555;">Akun: <?php echo e($coas->where('id', $coaId)->first()->kode_akun ?? ''); ?> - <?php echo e($coas->where('id', $coaId)->first()->nama_akun ?? ''); ?></p>
    <?php else: ?>
        <p style="margin: 5px 0 0 0; color: #555;">Akun: Semua Akun</p>
    <?php endif; ?>
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px;">Tanggal</th>
            <th style="border: 1px solid #ddd; padding: 5px;">Kode Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px;">Nama Akun</th>
            <th style="border: 1px solid #ddd; padding: 5px;">Keterangan</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Debit</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">Kredit</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td style="border: 1px solid #ddd; padding: 5px;"><?php echo e(\Carbon\Carbon::parse($r->tanggal)->format('d M Y')); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px;"><?php echo e($r->kode_akun); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px;"><?php echo e($r->nama_akun); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px;"><?php echo e($r->deskripsi); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;"><?php echo e($r->debit > 0 ? number_format($r->debit, 0, ',', '.') : '-'); ?></td>
            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;"><?php echo e($r->kredit > 0 ? number_format($r->kredit, 0, ',', '.') : '-'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Buku Besar'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\exports\buku-besar-export.blade.php ENDPATH**/ ?>