<?php $__env->startSection('title', 'Neraca Saldo'); ?>

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
    <div><h1 class="mb-0">Neraca Saldo</h1></div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button>
        <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-inline">
            <input type="hidden" name="dari" value="<?php echo e(request('dari', $dari)); ?>">
            <input type="hidden" name="sampai" value="<?php echo e(request('sampai', $sampai)); ?>">
            <input type="hidden" name="export" value="pdf">
            <button type="submit" class="btn btn-outline-danger"><i class="cil-file me-1"></i> PDF</button>
        </form>
        <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-inline">
            <input type="hidden" name="dari" value="<?php echo e(request('dari', $dari)); ?>">
            <input type="hidden" name="sampai" value="<?php echo e(request('sampai', $sampai)); ?>">
            <input type="hidden" name="export" value="excel">
            <button type="submit" class="btn btn-outline-success"><i class="cil-spreadsheet me-1"></i> Excel</button>
        </form>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>"></div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="card" id="printable"><div class="card-body">
    <div class="text-center mb-4"><h5 class="fw-bold mb-1">NERACA SALDO</h5><p class="text-body-secondary mb-0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d M Y')); ?></p></div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Kode Akun</th><th>Nama Akun</th><th>Tipe</th><th class="text-end">Debit</th><th class="text-end">Kredit</th><th class="text-end">Saldo</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong><?php echo e($r->kode_akun); ?></strong></td>
                    <td><?php echo e($r->nama_akun); ?></td>
                    <td><span class="badge bg-secondary"><?php echo e($r->tipe); ?></span></td>
                    <td class="text-end"><?php echo e(number_format($r->total_debit, 0, ',', '.')); ?></td>
                    <td class="text-end"><?php echo e(number_format($r->total_kredit, 0, ',', '.')); ?></td>
                    <td class="text-end fw-bold <?php echo e($r->saldo >= 0 ? '' : 'text-danger'); ?>"><?php echo e(number_format($r->saldo, 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot class="border-top border-2 fw-bold">
                <tr><td colspan="3">TOTAL</td><td class="text-end">Rp <?php echo e(number_format($totalDebit, 0, ',', '.')); ?></td><td class="text-end">Rp <?php echo e(number_format($totalKredit, 0, ',', '.')); ?></td><td class="text-end <?php echo e($totalDebit == $totalKredit ? 'text-success' : 'text-danger'); ?>"><?php echo e($totalDebit == $totalKredit ? 'BALANCE' : 'IMBALANCE'); ?></td></tr>
            </tfoot>
        </table>
    </div>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/neraca-saldo.blade.php ENDPATH**/ ?>