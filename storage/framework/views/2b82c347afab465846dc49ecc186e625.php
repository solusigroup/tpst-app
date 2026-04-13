<?php $__env->startSection('title', 'Posisi Keuangan'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-none d-print-block">
    <?php if (isset($component)) { $__componentOriginalb7b80f38d0023f8f730a94fb78f032db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7b80f38d0023f8f730a94fb78f032db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kop-surat','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kop-surat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7b80f38d0023f8f730a94fb78f032db)): ?>
<?php $attributes = $__attributesOriginalb7b80f38d0023f8f730a94fb78f032db; ?>
<?php unset($__attributesOriginalb7b80f38d0023f8f730a94fb78f032db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7b80f38d0023f8f730a94fb78f032db)): ?>
<?php $component = $__componentOriginalb7b80f38d0023f8f730a94fb78f032db; ?>
<?php unset($__componentOriginalb7b80f38d0023f8f730a94fb78f032db); ?>
<?php endif; ?>
</div>

<div class="page-header d-print-none flex-wrap d-flex justify-content-between align-items-center">
    <div><h1 class="mb-0">Laporan Posisi Keuangan</h1></div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button>
        <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-inline">
            <input type="hidden" name="sampai" value="<?php echo e(request('sampai', $sampai)); ?>">
            <input type="hidden" name="export" value="pdf">
            <button type="submit" class="btn btn-outline-danger"><i class="cil-file me-1"></i> PDF</button>
        </form>
        <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-inline">
            <input type="hidden" name="sampai" value="<?php echo e(request('sampai', $sampai)); ?>">
            <input type="hidden" name="export" value="excel">
            <button type="submit" class="btn btn-outline-success"><i class="cil-spreadsheet me-1"></i> Excel</button>
        </form>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Per Tanggal</label><input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>"></div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="row g-4" id="printable">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white"><h6 class="mb-0 fw-bold">ASET</h6></div>
            <div class="card-body">
                <h6 class="fw-semibold text-body-secondary">Aset Lancar</h6>
                <table class="table table-sm">
                    <?php $__currentLoopData = $asetLancar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($item->kode_akun); ?> - <?php echo e($item->nama_akun); ?></td><td class="text-end"><?php echo e(number_format($item->saldo, 0, ',', '.')); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="fw-bold border-top"><td>Total Aset Lancar</td><td class="text-end"><?php echo e(number_format($totalAsetLancar, 0, ',', '.')); ?></td></tr>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Aset Tidak Lancar</h6>
                <table class="table table-sm">
                    <?php $__currentLoopData = $asetTidakLancar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($item->kode_akun); ?> - <?php echo e($item->nama_akun); ?></td><td class="text-end"><?php echo e(number_format($item->saldo, 0, ',', '.')); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="fw-bold border-top"><td>Total Aset Tidak Lancar</td><td class="text-end"><?php echo e(number_format($totalAsetTidakLancar, 0, ',', '.')); ?></td></tr>
                </table>
                <div class="border-top border-2 pt-2 mt-3"><table class="table table-sm mb-0"><tr class="fw-bold fs-5"><td>TOTAL ASET</td><td class="text-end">Rp <?php echo e(number_format($totalAset, 0, ',', '.')); ?></td></tr></table></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark"><h6 class="mb-0 fw-bold">LIABILITAS & EKUITAS</h6></div>
            <div class="card-body">
                <h6 class="fw-semibold text-body-secondary">Liabilitas Jangka Pendek</h6>
                <table class="table table-sm">
                    <?php $__currentLoopData = $liabilitasJP; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($item->kode_akun); ?> - <?php echo e($item->nama_akun); ?></td><td class="text-end"><?php echo e(number_format($item->saldo, 0, ',', '.')); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="fw-bold border-top"><td>Total Liabilitas JP</td><td class="text-end"><?php echo e(number_format($totalLiabilitasJP, 0, ',', '.')); ?></td></tr>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Liabilitas Jangka Panjang</h6>
                <table class="table table-sm">
                    <?php $__currentLoopData = $liabilitasJPj; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($item->kode_akun); ?> - <?php echo e($item->nama_akun); ?></td><td class="text-end"><?php echo e(number_format($item->saldo, 0, ',', '.')); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="fw-bold border-top"><td>Total Liabilitas JPj</td><td class="text-end"><?php echo e(number_format($totalLiabilitasJPj, 0, ',', '.')); ?></td></tr>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Ekuitas</h6>
                <table class="table table-sm">
                    <?php $__currentLoopData = $ekuitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($item->kode_akun); ?> - <?php echo e($item->nama_akun); ?></td><td class="text-end"><?php echo e(number_format($item->saldo, 0, ',', '.')); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr><td>Laba/Rugi Berjalan</td><td class="text-end"><?php echo e(number_format($labaRugi ?? 0, 0, ',', '.')); ?></td></tr>
                    <tr class="fw-bold border-top"><td>Total Ekuitas</td><td class="text-end"><?php echo e(number_format($totalEkuitas, 0, ',', '.')); ?></td></tr>
                </table>
                <div class="border-top border-2 pt-2 mt-3"><table class="table table-sm mb-0"><tr class="fw-bold fs-5"><td>TOTAL LIABILITAS + EKUITAS</td><td class="text-end <?php echo e(abs($totalAset - $totalLiabilitasEkuitas) < 0.01 ? 'text-success' : 'text-danger'); ?>">Rp <?php echo e(number_format($totalLiabilitasEkuitas, 0, ',', '.')); ?></td></tr></table></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/posisi-keuangan.blade.php ENDPATH**/ ?>