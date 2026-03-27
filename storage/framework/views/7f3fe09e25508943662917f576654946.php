<?php $__env->startSection('title', 'Hasil Pilahan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Hasil Pilahan Sampah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Hasil Pilahan</li></ol></nav>
    </div>
    <a href="<?php echo e(route('admin.hasil-pilahan.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Data</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari jenis/petugas..." value="<?php echo e(request('search')); ?>"></div>
            <div class="col-auto">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = ['Organik','Anorganik','B3','Residu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php echo e(request('kategori') == $k ? 'selected' : ''); ?>><?php echo e($k); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            <?php if(request()->hasAny(['search','kategori'])): ?><div class="col-auto"><a href="<?php echo e(route('admin.hasil-pilahan.index')); ?>" class="btn btn-outline-secondary">Reset</a></div><?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Tanggal</th><th>Kategori</th><th>Jenis</th><th>Tonase</th><th>Petugas</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $hasilPilahans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($item->tanggal)->format('d M Y')); ?></td>
                        <td>
                            <?php $catColors = ['Organik'=>'success','Anorganik'=>'info','B3'=>'danger','Residu'=>'warning']; ?>
                            <span class="badge bg-<?php echo e($catColors[$item->kategori] ?? 'secondary'); ?>"><?php echo e($item->kategori); ?></span>
                        </td>
                        <td><?php echo e($item->jenis); ?></td>
                        <td><?php echo e(number_format($item->tonase, 2, ',', '.')); ?> kg</td>
                        <td><?php echo e($item->officer); ?></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.hasil-pilahan.edit', $item)); ?>" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.hasil-pilahan.destroy', $item)); ?>" class="d-inline" onsubmit="return confirm('Yakin hapus?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($hasilPilahans->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($hasilPilahans->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/hasil-pilahan/index.blade.php ENDPATH**/ ?>