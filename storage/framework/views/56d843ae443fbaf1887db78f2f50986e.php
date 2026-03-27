<?php $__env->startSection('title', 'Jurnal Kas'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Jurnal Kas</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Jurnal Kas</li></ol></nav>
    </div>
    <a href="<?php echo e(route('admin.jurnal-kas.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah</a>
</div>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari deskripsi..." value="<?php echo e(request('search')); ?>"></div>
            <div class="col-auto">
                <select name="jenis" class="form-select"><option value="">Semua</option><option value="masuk" <?php echo e(request('jenis')=='masuk'?'selected':''); ?>>Kas Masuk</option><option value="keluar" <?php echo e(request('jenis')=='keluar'?'selected':''); ?>>Kas Keluar</option></select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            <?php if(request()->hasAny(['search','jenis'])): ?><div class="col-auto"><a href="<?php echo e(route('admin.jurnal-kas.index')); ?>" class="btn btn-outline-secondary">Reset</a></div><?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Tanggal</th><th>Jenis</th><th>Akun</th><th>Jumlah</th><th>Deskripsi</th><th>Bukti</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $jurnalKas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')); ?></td>
                        <td><span class="badge bg-<?php echo e($item->tipe=='Penerimaan'?'success':'danger'); ?>"><?php echo e($item->tipe=='Penerimaan'?'Kas Masuk':'Kas Keluar'); ?></span></td>
                        <td><?php echo e($item->coaLawan->nama_akun ?? '-'); ?></td>
                        <td><strong>Rp <?php echo e(number_format($item->nominal, 0, ',', '.')); ?></strong></td>
                        <td><?php echo e(\Illuminate\Support\Str::limit($item->deskripsi, 40)); ?></td>
                        <td>
                            <?php if($item->bukti_transaksi): ?>
                                <a href="<?php echo e(Storage::url($item->bukti_transaksi)); ?>" target="_blank" class="badge bg-info text-decoration-none" title="Lihat Bukti"><i class="cil-paperclip"></i> Lihat</a>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.jurnal-kas.edit', $item)); ?>" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.jurnal-kas.destroy', $item)); ?>" class="d-inline" onsubmit="return confirm('Yakin hapus?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($jurnalKas->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($jurnalKas->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/jurnal-kas/index.blade.php ENDPATH**/ ?>