<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <h2 style="margin:0">REKAP RITASE PER TANGGAL & JENIS KLIEN</h2>
    <p style="margin:5px 0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
    <?php if($jenisKlien): ?>
        <p style="margin:2px 0; font-size:10px;">Filter Jenis Klien: <?php echo e($jenisKlien); ?></p>
    <?php endif; ?>
</div>


<table class="table" style="width: 50%; margin-bottom: 20px;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th>Jenis Klien</th>
            <th class="text-center">Total Ritase</th>
            <th class="text-end">Netto (kg)</th>
            <th class="text-end">Biaya Tipping</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rekapPerJenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($rj->jenis); ?></td>
            <td class="text-center"><?php echo e(number_format($rj->total_ritase, 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($rj->total_netto, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e($rj->total_tipping); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold">
        <tr style="background-color: #f0f0f0;">
            <td>TOTAL</td>
            <td class="text-center"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e($grandTotals->total_tipping ?? 0); ?></td>
        </tr>
    </tfoot>
</table>


<p class="fw-bold mb-1" style="font-size: 12px;">Rekap Harian per Jenis Klien</p>
<table class="table" style="font-size: 9px;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th rowspan="2" class="align-middle text-center">Tanggal</th>
            <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th colspan="3" class="text-center" style="border-left: 2px solid #999;"><?php echo e($jt); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th colspan="3" class="text-center" style="border-left: 2px solid #999;">Total</th>
        </tr>
        <tr style="background-color: #f8f8f8;">
            <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th class="text-center" style="border-left: 1px solid #ccc;">Rit</th>
            <th class="text-end">Netto</th>
            <th class="text-end">Tipping</th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th class="text-center" style="border-left: 2px solid #999;">Rit</th>
            <th class="text-end">Netto</th>
            <th class="text-end">Tipping</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $pivotData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e(\Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y')); ?></td>
            <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $cell = $row['jenis'][$jt] ?? null; ?>
            <td class="text-center" style="border-left: 1px solid #ccc;"><?php echo e($cell ? number_format($cell['total_ritase'], 0, ',', '.') : '-'); ?></td>
            <td class="text-end"><?php echo e($cell ? number_format($cell['total_netto'], 2, ',', '.') : '-'); ?></td>
            <td class="text-end"><?php echo e($cell ? $cell['total_tipping'] : '-'); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td class="text-center fw-bold" style="border-left: 2px solid #999;"><?php echo e(number_format($row['total_ritase'], 0, ',', '.')); ?></td>
            <td class="text-end fw-bold"><?php echo e(number_format($row['total_netto'], 2, ',', '.')); ?></td>
            <td class="text-end fw-bold"><?php echo e($row['total_tipping']); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold">
        <tr style="background-color: #f0f0f0;">
            <td class="text-center">TOTAL</td>
            <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $jtRekap = $rekapPerJenis->firstWhere('jenis', $jt); ?>
            <td class="text-center" style="border-left: 1px solid #ccc;"><?php echo e($jtRekap ? number_format($jtRekap->total_ritase, 0, ',', '.') : '-'); ?></td>
            <td class="text-end"><?php echo e($jtRekap ? number_format($jtRekap->total_netto, 2, ',', '.') : '-'); ?></td>
            <td class="text-end"><?php echo e($jtRekap ? $jtRekap->total_tipping : '-'); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td class="text-center" style="border-left: 2px solid #999;"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e($grandTotals->total_tipping ?? 0); ?></td>
        </tr>
    </tfoot>
</table>


<p class="fw-bold mb-1 mt-3" style="font-size: 12px;">Detail per Klien</p>
<table class="table">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th width="30" class="text-center">No</th>
            <th>Nama Klien</th>
            <th>Jenis</th>
            <th class="text-center">Ritase</th>
            <th class="text-end">Netto (kg)</th>
            <th class="text-end">Biaya Tipping</th>
            <th class="text-end">Avg Netto/Rit</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $rekapPerKlien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($index + 1); ?></td>
            <td><?php echo e($rk->nama_klien); ?></td>
            <td><?php echo e($rk->jenis); ?></td>
            <td class="text-center"><?php echo e(number_format($rk->total_ritase, 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($rk->total_netto, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e($rk->total_tipping); ?></td>
            <td class="text-end"><?php echo e($rk->total_ritase > 0 ? number_format($rk->total_netto / $rk->total_ritase, 2, ',', '.') : '-'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot class="fw-bold">
        <tr style="background-color: #f0f0f0;">
            <td colspan="3" class="text-end">TOTAL</td>
            <td class="text-center"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?></td>
            <td class="text-end"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></td>
            <td class="text-end"><?php echo e($grandTotals->total_tipping ?? 0); ?></td>
            <td class="text-end"><?php echo e(($grandTotals->total_ritase ?? 0) > 0 ? number_format(($grandTotals->total_netto ?? 0) / $grandTotals->total_ritase, 2, ',', '.') : '-'); ?></td>
        </tr>
    </tfoot>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.laporan.exports.layout', ['title' => 'Rekap Ritase per Tanggal & Jenis Klien'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\exports\rekap-ritase-export.blade.php ENDPATH**/ ?>