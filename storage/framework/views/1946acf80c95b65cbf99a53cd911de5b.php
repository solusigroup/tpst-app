<?php $__env->startSection('title', 'Penjualan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Penjualan</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Penjualan</li></ol></nav>
    </div>
    <a href="<?php echo e(route('admin.penjualan.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Penjualan</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari jenis produk..." value="<?php echo e(request('search')); ?>"></div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            <?php if(request('search')): ?><div class="col-auto"><a href="<?php echo e(route('admin.penjualan.index')); ?>" class="btn btn-outline-secondary">Reset</a></div><?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Pembeli</th><th>Tanggal</th><th>Jenis Produk</th><th>Berat (kg)</th><th>Harga Satuan</th><th>Total Harga</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $penjualans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($item->klien->nama_klien ?? '-'); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')); ?></td>
                        <td><?php echo e($item->jenis_produk); ?></td>
                        <td><?php echo e(number_format($item->berat_kg, 2, ',', '.')); ?></td>
                        <td>Rp <?php echo e(number_format($item->harga_satuan, 0, ',', '.')); ?></td>
                        <td><strong>Rp <?php echo e(number_format($item->total_harga, 0, ',', '.')); ?></strong></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.penjualan.show', $item)); ?>" class="btn btn-outline-info" title="Lihat"><i class="cil-search"></i></a>
                                <a href="<?php echo e(route('admin.penjualan.edit', $item)); ?>" class="btn btn-outline-primary" title="Edit"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.penjualan.destroy', $item)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger" title="Hapus"><i class="cil-trash"></i></button></form>
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
    <?php if($penjualans->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($penjualans->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/penjualan/index.blade.php ENDPATH**/ ?>