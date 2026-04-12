<?php $__env->startSection('title', 'Laporan Pengangkutan Residu'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Laporan Pengangkutan Residu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Laporan Operasional</a></li>
                <li class="breadcrumb-item active">Pengangkutan Residu</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="cil-filter me-1"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo e(route('admin.laporan-operasional.residu')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>No. Tiket</th>
                        <th>Tanggal</th>
                        <th>Armada</th>
                        <th class="text-end">Bruto (Kg)</th>
                        <th class="text-end">Tarra (Kg)</th>
                        <th class="text-end">Netto (Kg)</th>
                        <th class="text-end">Retribusi</th>
                        <th>Tujuan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center"><?php echo e($rows->firstItem() + $index); ?></td>
                        <td><?php echo e($row->nomor_tiket); ?></td>
                        <td><?php echo e($row->tanggal->format('d/m/Y')); ?></td>
                        <td><?php echo e($row->armada->nomor_plat); ?></td>
                        <td class="text-end"><?php echo e(number_format($row->berat_bruto, 0, ',', '.')); ?></td>
                        <td class="text-end"><?php echo e(number_format($row->berat_tarra, 0, ',', '.')); ?></td>
                        <td class="text-end fw-bold"><?php echo e(number_format($row->berat_netto, 0, ',', '.')); ?></td>
                        <td class="text-end">Rp <?php echo e(number_format($row->biaya_retribusi, 0, ',', '.')); ?></td>
                        <td><?php echo e($row->tujuan); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Tidak ada data untuk periode ini.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <?php if($rows->count() > 0): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="text-end">TOTAL</td>
                        <td class="text-end text-primary"><?php echo e(number_format($totals->total_netto, 0, ',', '.')); ?></td>
                        <td class="text-end text-danger">Rp <?php echo e(number_format($totals->total_biaya, 0, ',', '.')); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
    <?php if($rows->hasPages()): ?>
    <div class="card-footer bg-white">
        <?php echo e($rows->links()); ?>

    </div>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="small opacity-75">Total Netto Residu</div>
                <div class="fs-4 fw-bold"><?php echo e(number_format($totals->total_netto, 0, ',', '.')); ?> Kg</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="small opacity-75">Total Biaya Tipping Fee</div>
                <div class="fs-4 fw-bold">Rp <?php echo e(number_format($totals->total_biaya, 0, ',', '.')); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="small opacity-75">Total Ritase</div>
                <div class="fs-4 fw-bold"><?php echo e($totals->total_rows); ?> Trip</div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/residu.blade.php ENDPATH**/ ?>