<?php $__env->startSection('title', 'Laporan Hasil Pilahan'); ?>

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

<div class="page-header d-print-none"><div><h1>Laporan Hasil Pilahan Sampah</h1></div><button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button></div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>"></div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">-- Semua --</option>
                <?php $__currentLoopData = ['Organik','Anorganik','B3','Residu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c); ?>" <?php echo e($kategori == $c ? 'selected' : ''); ?>><?php echo e($c); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>


<div class="card mb-4" id="printable-summary">
    <div class="card-header bg-light fw-bold">
        <i class="cil-bar-chart me-1"></i> Ringkasan Stok Pilahan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th class="text-end">Total Pilahan</th>
                        <th class="text-end">Terjual</th>
                        <th class="text-end">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $stokSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <?php $catColors = ['Organik'=>'success','Anorganik'=>'info','B3'=>'danger','Residu'=>'warning']; ?>
                            <span class="badge bg-<?php echo e($catColors[$stok->kategori] ?? 'secondary'); ?>"><?php echo e($stok->kategori); ?></span>
                        </td>
                        <td class="fw-medium"><?php echo e($stok->jenis); ?></td>
                        <td class="text-end text-primary"><?php echo e(number_format($stok->total_pilahan, 2, ',', '.')); ?> kg</td>
                        <td class="text-end text-danger"><?php echo e(number_format($stok->total_terjual, 2, ',', '.')); ?> kg</td>
                        <td class="text-end fw-bold <?php echo e($stok->sisa_stok > 0 ? 'text-success' : 'text-body-secondary'); ?>"><?php echo e(number_format($stok->sisa_stok, 2, ',', '.')); ?> kg</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Data ringkasan stok belum tersedia untuk filter ini.</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($stokSummary) > 0): ?>
                <tfoot class="border-top border-2 fw-bold bg-light">
                    <tr>
                        <td colspan="2" class="text-end">TOTAL KESELURUHAN</td>
                        <td class="text-end text-primary"><?php echo e(number_format($summaryTotals->total_pilahan, 2, ',', '.')); ?> kg</td>
                        <td class="text-end text-danger"><?php echo e(number_format($summaryTotals->total_terjual, 2, ',', '.')); ?> kg</td>
                        <td class="text-end text-success"><?php echo e(number_format($summaryTotals->sisa_stok, 2, ',', '.')); ?> kg</td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<h5 class="mb-3">Riwayat Log Harian</h5>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Tanggal</th><th>Kategori</th><th>Jenis</th><th>Petugas</th><th class="text-end">Tonase</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($r->tanggal)->format('d M Y')); ?></td>
                        <td>
                            <?php $catColors = ['Organik'=>'success','Anorganik'=>'info','B3'=>'danger','Residu'=>'warning']; ?>
                            <span class="badge bg-<?php echo e($catColors[$r->kategori] ?? 'secondary'); ?>"><?php echo e($r->kategori); ?></span>
                        </td>
                        <td><?php echo e($r->jenis); ?></td>
                        <td><?php echo e($r->officer); ?></td>
                        <td class="text-end"><?php echo e(number_format($r->tonase, 2, ',', '.')); ?> kg</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data hasil pilahan.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr><td colspan="4" class="text-end">TOTAL (<?php echo e(number_format($totals->total_rows ?? 0, 0, ',', '.')); ?> Catatan)</td><td class="text-end"><?php echo e(number_format($totals->total_tonase ?? 0, 2, ',', '.')); ?> kg</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php if($rows->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($rows->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/hasil-pilahan.blade.php ENDPATH**/ ?>