<?php $__env->startSection('title', 'Laporan Ritase'); ?>

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

<div class="page-header d-print-none">
    <div><h1>Laporan Ritase</h1></div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button>
        <div class="btn-group">
            <a href="<?php echo e(route('admin.laporan-operasional.ritase', array_merge(request()->all(), ['export' => 'pdf']))); ?>" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="<?php echo e(route('admin.laporan-operasional.ritase', array_merge(request()->all(), ['export' => 'excel']))); ?>" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>"></div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Klien</label>
            <select name="klien_id" class="form-select">
                <option value="">-- Semua Klien --</option>
                <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k->id); ?>" <?php echo e($klienId == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_klien); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Status</label>
            <select name="status" class="form-select">
                <option value="">-- Semua --</option>
                <?php $__currentLoopData = ['masuk','timbang','keluar','selesai']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php echo e($status == $s ? 'selected' : ''); ?>><?php echo e(ucfirst($s)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-light"><strong>Rekap Jenis Armada</strong></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="small table-light">
                        <tr>
                            <th>Jenis Armada</th>
                            <th class="text-center">Ritase</th>
                            <th class="text-end">Tonase (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rekapJenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($rj->jenis_armada ?? 'N/A'); ?></td>
                            <td class="text-center"><?php echo e(number_format($rj->total_ritase, 0, ',', '.')); ?></td>
                            <td class="text-end"><?php echo e(number_format($rj->total_netto, 2, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="fw-bold table-light">
                        <tr>
                            <td>TOTAL</td>
                            <td class="text-center"><?php echo e(number_format($rekapJenis->sum('total_ritase'), 0, ',', '.')); ?></td>
                            <td class="text-end"><?php echo e(number_format($rekapJenis->sum('total_netto'), 2, ',', '.')); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>No Tiket</th><th>Tiket (M)</th><th>Armada</th><th>Jenis Armada</th><th>Klien</th><th class="text-end">Berat Netto</th><th class="text-end">Biaya Tipping</th><th>Status Tiket</th><th>Status Invoice</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($r->waktu_masuk)->format('d M Y')); ?></td>
                        <td><strong><?php echo e($r->nomor_tiket); ?></strong></td>
                        <td><?php echo e($r->tiket ?? '-'); ?></td>
                        <td><?php echo e($r->armada->plat_nomor ?? '-'); ?></td>
                        <td><?php echo e($r->armada->jenis_armada ?? '-'); ?></td>
                        <td><?php echo e($r->klien->nama_klien ?? '-'); ?></td>
                        <td class="text-end"><?php echo e(number_format($r->berat_netto, 2, ',', '.')); ?> kg</td>
                        <td class="text-end">Rp <?php echo e(number_format($r->biaya_tipping, 0, ',', '.')); ?></td>
                        <td>
                            <?php $statusColors = ['masuk'=>'warning','timbang'=>'info','keluar'=>'primary','selesai'=>'success']; ?>
                            <span class="badge bg-<?php echo e($statusColors[$r->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($r->status)); ?></span>
                        </td>
                        <td>
                            <?php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; ?>
                            <span class="badge bg-<?php echo e($invoiceColors[$r->status_invoice] ?? 'secondary'); ?>"><?php echo e($r->status_invoice ?? 'Unbilled'); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="10" class="text-center py-4 text-body-secondary">Belum ada data ritase.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr><td colspan="6" class="text-end">TOTAL (<?php echo e(number_format($totals->total_rows ?? 0, 0, ',', '.')); ?> Ritase)</td><td class="text-end"><?php echo e(number_format($totals->total_netto ?? 0, 2, ',', '.')); ?> kg</td><td class="text-end">Rp <?php echo e(number_format($totals->total_tipping ?? 0, 0, ',', '.')); ?></td><td colspan="2"></td></tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php if($rows->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($rows->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/ritase.blade.php ENDPATH**/ ?>