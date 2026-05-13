<?php $__env->startSection('title', 'Laporan Rekap Ritase II'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Laporan Rekap Ritase II</h1>
    </div>
    <div>
        <a href="<?php echo e(route('admin.laporan-operasional.rekap-ritase-2', array_merge(request()->all(), ['export' => 'pdf']))); ?>" class="btn btn-danger">
            <i class="cil-file text-white"></i> Export PDF
        </a>
        <a href="<?php echo e(route('admin.laporan-operasional.rekap-ritase-2', array_merge(request()->all(), ['export' => 'excel']))); ?>" class="btn btn-success">
            <i class="cil-spreadsheet text-white"></i> Export Excel
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('admin.laporan-operasional.rekap-ritase-2')); ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="klien_id" class="form-label">Klien</label>
                <select name="klien_id" id="klien_id" class="form-select ts-select">
                    <option value="">Semua Klien</option>
                    <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id); ?>" <?php echo e($klienId == $k->id ? 'selected' : ''); ?>>
                            <?php echo e($k->nama_klien); ?> (<?php echo e($k->jenis); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select name="bulan" id="bulan" class="form-select">
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?php echo e(sprintf('%02d', $i)); ?>" <?php echo e($bulan == sprintf('%02d', $i) ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::create()->month($i)->translatedFormat('F')); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="tahun" class="form-label">Tahun</label>
                <select name="tahun" id="tahun" class="form-select">
                    <?php for($i=date('Y'); $i>=2020; $i--): ?>
                        <option value="<?php echo e($i); ?>" <?php echo e($tahun == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="is_approved" class="form-label">Approval</label>
                <select name="is_approved" id="is_approved" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" <?php echo e($isApproved === '1' ? 'selected' : ''); ?>>Approved</option>
                    <option value="0" <?php echo e($isApproved === '0' ? 'selected' : ''); ?>>Not Approved</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="cil-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Preview Data Rekap</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="font-weight: bold;">KLIEN</th>
                        <th colspan="2"><?php echo e($klien ? $klien->nama_klien : 'Semua Klien'); ?></th>
                    </tr>
                    <tr>
                        <th style="font-weight: bold;">JENIS KLIEN</th>
                        <th colspan="2"><?php echo e($klien ? $klien->jenis : '-'); ?></th>
                    </tr>
                    <tr>
                        <th style="font-weight: bold;">BULAN</th>
                        <th><?php echo e(\Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F')); ?></th>
                        <th style="font-weight: bold;">Tahun: <?php echo e($tahun); ?></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="border-0"></th>
                    </tr>
                    <tr>
                        <th>Row Labels</th>
                        <th>Count of Berat Netto (kg)</th>
                        <th>Sum of Berat Netto (kg)2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rekapHarian; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($row->tanggal)->format('d/m/Y')); ?></td>
                        <td><?php echo e($row->total_ritase); ?></td>
                        <td><?php echo e(number_format($row->total_netto, 0, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data untuk periode ini</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <?php if($rekapHarian->count() > 0): ?>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td>Grand Total</td>
                        <td><?php echo e($grandTotalRitase); ?></td>
                        <td><?php echo e(number_format($grandTotalNetto, 0, ',', '.')); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('.ts-select', {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/rekap-ritase-2.blade.php ENDPATH**/ ?>