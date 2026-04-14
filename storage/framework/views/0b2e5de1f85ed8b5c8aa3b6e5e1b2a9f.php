<?php $__env->startSection('title', 'Chart of Account'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Chart of Account</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">COA</li></ol></nav>
    </div>
    <a href="<?php echo e(route('admin.coa.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Akun</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari kode/nama akun..." value="<?php echo e(request('search')); ?>"></div>
            <div class="col-auto">
                <select name="tipe" class="form-select">
                    <option value="">Semua Tipe</option>
                    <?php $__currentLoopData = ['Asset','Liability','Equity','Revenue','Expense']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t); ?>" <?php echo e(request('tipe') == $t ? 'selected' : ''); ?>><?php echo e($t); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            <?php if(request()->hasAny(['search','tipe'])): ?><div class="col-auto"><a href="<?php echo e(route('admin.coa.index')); ?>" class="btn btn-outline-secondary">Reset</a></div><?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Kode Akun</th><th>Nama Akun</th><th>Tipe</th><th>Klasifikasi</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($item->kode_akun); ?></strong></td>
                        <td><?php echo e($item->nama_akun); ?></td>
                        <td><span class="badge bg-primary"><?php echo e($item->tipe); ?></span></td>
                        <td><span class="badge bg-secondary"><?php echo e($item->klasifikasi); ?></span></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.coa.edit', $item)); ?>" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.coa.destroy', $item)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($coas->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($coas->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/coa/index.blade.php ENDPATH**/ ?>