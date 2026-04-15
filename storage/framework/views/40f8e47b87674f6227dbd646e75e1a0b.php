<?php $__env->startSection('title', 'Laporan Kehadiran Karyawan'); ?>

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
    <div><h1>Laporan Kehadiran Karyawan</h1></div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button>
        <div class="btn-group">
            <a href="<?php echo e(route('admin.laporan-operasional.kehadiran', array_merge(request()->all(), ['export' => 'pdf']))); ?>" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="<?php echo e(route('admin.laporan-operasional.kehadiran', array_merge(request()->all(), ['export' => 'excel']))); ?>" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari</label>
                <input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Karyawan</label>
                <select name="user_id" class="form-select">
                    <option value="">-- Semua Karyawan --</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php echo e($userId == $u->id ? 'selected' : ''); ?>>
                            <?php echo e($u->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">
                    <i class="cil-filter me-1"></i> Filter
                </button>
                <a href="<?php echo e(route('admin.laporan-operasional.kehadiran')); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($r->attendance_date)->format('d M Y')); ?></td>
                        <td><strong><?php echo e($r->user->name ?? '-'); ?></strong></td>
                        <td><?php echo e($r->check_in ? \Carbon\Carbon::parse($r->check_in)->format('H:i') : '-'); ?></td>
                        <td><?php echo e($r->check_out ? \Carbon\Carbon::parse($r->check_out)->format('H:i') : '-'); ?></td>
                        <td>
                            <?php 
                                $statusColors = [
                                    'present' => 'success',
                                    'absent' => 'danger',
                                    'sick' => 'warning',
                                    'leave' => 'info'
                                ]; 
                                $statusLabels = [
                                    'present' => 'Hadir',
                                    'absent' => 'Alpa',
                                    'sick' => 'Sakit',
                                    'leave' => 'Izin'
                                ];
                            ?>
                            <span class="badge bg-<?php echo e($statusColors[$r->status] ?? 'secondary'); ?>">
                                <?php echo e($statusLabels[$r->status] ?? $r->status); ?>

                            </span>
                        </td>
                        <td><?php echo e($r->notes ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-body-secondary">
                            Belum ada data kehadiran pada periode ini.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">RINGKASAN (<?php echo e(number_format($totals->total_rows, 0, ',', '.')); ?> Record)</td>
                        <td colspan="2">
                            <div class="d-flex gap-3">
                                <span class="text-success">Hadir: <?php echo e($totals->present); ?></span>
                                <span class="text-danger">Alpa: <?php echo e($totals->absent); ?></span>
                                <span class="text-warning">Sakit: <?php echo e($totals->sick); ?></span>
                                <span class="text-info">Izin: <?php echo e($totals->leave); ?></span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php if($rows->hasPages()): ?> 
        <div class="card-footer bg-white d-print-none">
            <?php echo e($rows->links()); ?>

        </div> 
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/attendance.blade.php ENDPATH**/ ?>