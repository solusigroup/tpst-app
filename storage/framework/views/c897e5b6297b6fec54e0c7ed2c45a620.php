<?php $__env->startSection('title', 'Perubahan Ekuitas'); ?>

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
    <div><h1 class="mb-0">Laporan Perubahan Ekuitas</h1></div>
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
    <div class="text-center mb-4"><h5 class="fw-bold mb-1">LAPORAN PERUBAHAN EKUITAS</h5><p class="text-body-secondary mb-0">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d M Y')); ?></p></div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Akun Ekuitas</th><th class="text-end">Saldo Awal</th><th class="text-end">Penambahan</th><th class="text-end">Pengurangan</th><th class="text-end">Saldo Akhir</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($r['kode_akun']); ?> - <?php echo e($r['nama_akun']); ?></td>
                    <td class="text-end"><?php echo e(number_format($r['saldoAwal'], 0, ',', '.')); ?></td>
                    <td class="text-end text-success"><?php echo e(number_format($r['penambahan'], 0, ',', '.')); ?></td>
                    <td class="text-end text-danger"><?php echo e(number_format($r['pengurangan'], 0, ',', '.')); ?></td>
                    <td class="text-end fw-bold"><?php echo e(number_format($r['saldoAkhir'], 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="bg-light">
                    <td>Laba / (Rugi) Bersih Periode Berjalan</td>
                    <td class="text-end">-</td>
                    <td class="text-end <?php echo e($labaRugi >= 0 ? 'text-success' : ''); ?>"><?php echo e($labaRugi >= 0 ? number_format($labaRugi, 0, ',', '.') : '-'); ?></td>
                    <td class="text-end <?php echo e($labaRugi < 0 ? 'text-danger' : ''); ?>"><?php echo e($labaRugi < 0 ? number_format(abs($labaRugi), 0, ',', '.') : '-'); ?></td>
                    <td class="text-end fw-bold <?php echo e($labaRugi >= 0 ? 'text-success' : 'text-danger'); ?>"><?php echo e(number_format($labaRugi, 0, ',', '.')); ?></td>
                </tr>
            </tbody>
            <tfoot class="border-top border-2 fw-bold fs-6">
                <tr><td>TOTAL EKUITAS</td><td class="text-end">Rp <?php echo e(number_format($totalSaldoAwal, 0, ',', '.')); ?></td><td class="text-end text-success">Rp <?php echo e(number_format($totalPenambahan + ($labaRugi >= 0 ? $labaRugi : 0), 0, ',', '.')); ?></td><td class="text-end text-danger">Rp <?php echo e(number_format($totalPengurangan + ($labaRugi < 0 ? abs($labaRugi) : 0), 0, ',', '.')); ?></td><td class="text-end">Rp <?php echo e(number_format($totalSaldoAkhir, 0, ',', '.')); ?></td></tr>
            </tfoot>
        </table>
    </div>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\perubahan-ekuitas.blade.php ENDPATH**/ ?>