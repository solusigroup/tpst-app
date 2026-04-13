<?php $__env->startSection('title', 'Jurnal'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Jurnal</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Jurnal</li></ol></nav>
    </div>
    <a href="<?php echo e(route('admin.jurnal.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Jurnal</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari referensi/deskripsi..." value="<?php echo e(request('search')); ?>"></div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="posted" <?php echo e(request('status') == 'posted' ? 'selected' : ''); ?>>Posted</option>
                    <option value="unposted" <?php echo e(request('status') == 'unposted' ? 'selected' : ''); ?>>Unposted</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            <?php if(request()->hasAny(['search','status'])): ?><div class="col-auto"><a href="<?php echo e(route('admin.jurnal.index')); ?>" class="btn btn-outline-secondary">Reset</a></div><?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>No. Referensi</th><th>Deskripsi</th><th>Status</th><th>Bukti</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $jurnals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')); ?></td>
                        <td><strong><?php echo e($item->nomor_referensi ?? '-'); ?></strong></td>
                        <td><?php echo e(\Illuminate\Support\Str::limit($item->deskripsi, 50)); ?></td>
                        <td><span class="badge bg-<?php echo e($item->status === 'posted' ? 'success' : 'warning'); ?>"><?php echo e(ucfirst($item->status)); ?></span></td>
                        <td>
                            <?php if($item->bukti_transaksi): ?>
                                <img src="<?php echo e(asset('storage/' . $item->bukti_transaksi)); ?>" class="rounded" style="width:32px;height:32px;object-fit:cover;" alt="bukti">
                            <?php else: ?> -
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <?php if($item->status !== 'posted'): ?>
                                    <form method="POST" action="<?php echo e(route('admin.jurnal.post', $item)); ?>" class="d-inline" ><?php echo csrf_field(); ?><button class="btn btn-outline-success" title="Post"><i class="cil-check-circle"></i></button></form>
                                <?php else: ?>
                                    <form method="POST" action="<?php echo e(route('admin.jurnal.unpost', $item)); ?>" class="d-inline" ><?php echo csrf_field(); ?><button class="btn btn-outline-warning" title="Unpost"><i class="cil-x-circle"></i></button></form>
                                <?php endif; ?>
                                <a href="<?php echo e(route('admin.jurnal.show', $item)); ?>" class="btn btn-outline-info" title="Lihat"><i class="cil-search"></i></a>
                                <a href="<?php echo e(route('admin.jurnal.edit', $item)); ?>" class="btn btn-outline-primary" title="Edit"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.jurnal.destroy', $item)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
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
    <?php if($jurnals->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($jurnals->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/jurnal/index.blade.php ENDPATH**/ ?>