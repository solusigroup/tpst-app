<?php $__env->startSection('title', 'Buku Besar'); ?>

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
    <div><h1 class="mb-0">Buku Besar</h1></div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button>
        <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-inline">
            <input type="hidden" name="dari" value="<?php echo e(request('dari', $dari)); ?>">
            <input type="hidden" name="sampai" value="<?php echo e(request('sampai', $sampai)); ?>">
            <?php if($coaId): ?><input type="hidden" name="coa_id" value="<?php echo e(request('coa_id', $coaId)); ?>"><?php endif; ?>
            <input type="hidden" name="export" value="pdf">
            <button type="submit" class="btn btn-outline-danger"><i class="cil-file me-1"></i> PDF</button>
        </form>
        <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-inline">
            <input type="hidden" name="dari" value="<?php echo e(request('dari', $dari)); ?>">
            <input type="hidden" name="sampai" value="<?php echo e(request('sampai', $sampai)); ?>">
            <?php if($coaId): ?><input type="hidden" name="coa_id" value="<?php echo e(request('coa_id', $coaId)); ?>"><?php endif; ?>
            <input type="hidden" name="export" value="excel">
            <button type="submit" class="btn btn-outline-success"><i class="cil-spreadsheet me-1"></i> Excel</button>
        </form>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>"></div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Akun COA</label>
            <select name="coa_id" class="form-select">
                <option value="">-- Semua Akun --</option>
                <?php $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php echo e($coaId == $c->id ? 'selected' : ''); ?>><?php echo e($c->kode_akun); ?> - <?php echo e($c->nama_akun); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Kode Akun</th><th>Nama Akun</th><th>Keterangan</th><th class="text-end">Debit</th><th class="text-end">Kredit</th><th class="text-end">Saldo</th></tr></thead>
                <tbody>
                    <?php 
                        $runningSaldo = $pageSaldoAwal ?? 0;
                        $isDebitNormal = $selectedCoa ? in_array($selectedCoa->tipe, ['Asset', 'Expense']) : true;
                    ?>

                    <?php if($coaId && $rows->currentPage() == 1): ?>
                    <tr class="table-light italic">
                        <td colspan="4"><strong>SALDO AWAL (Per <?php echo e(\Carbon\Carbon::parse($dari)->format('d M Y')); ?>)</strong></td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                        <td class="text-end fw-bold text-primary"><?php echo e(number_format($saldoAwal, 0, ',', '.')); ?></td>
                    </tr>
                    <?php endif; ?>

                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php 
                        if ($selectedCoa) {
                            if ($isDebitNormal) {
                                $runningSaldo += ($r->debit - $r->kredit);
                            } else {
                                $runningSaldo += ($r->kredit - $r->debit);
                            }
                        }
                    ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($r->tanggal)->format('d M Y')); ?></td>
                        <td><strong><?php echo e($r->kode_akun); ?></strong></td>
                        <td><?php echo e($r->nama_akun); ?></td>
                        <td style="font-size: 0.85rem; max-width: 300px; white-space: normal; word-wrap: break-word;"><?php echo e($r->deskripsi); ?></td>
                        <td class="text-end"><?php echo e($r->debit > 0 ? number_format($r->debit, 0, ',', '.') : '-'); ?></td>
                        <td class="text-end"><?php echo e($r->kredit > 0 ? number_format($r->kredit, 0, ',', '.') : '-'); ?></td>
                        <td class="text-end fw-bold <?php echo e($runningSaldo < 0 ? 'text-danger' : 'text-primary'); ?>">
                            <?php if($coaId): ?>
                                <?php echo e(number_format($runningSaldo, 0, ',', '.')); ?>

                            <?php else: ?>
                                <span class="text-muted small">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data jurnal untuk periode ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($rows->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($rows->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\buku-besar.blade.php ENDPATH**/ ?>