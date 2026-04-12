<?php $__env->startSection('title', 'Invoice'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Invoice</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Invoice</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="<?php echo e(route('admin.invoice.merge-drafts')); ?>" class="m-0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-warning text-dark" onclick="return confirm('Apakah Anda yakin ingin menggabungkan semua Invoice Draft dari Klien yang sama?')">
                <i class="cil-object-group me-1"></i> Gabung Draft
            </button>
        </form>
        <a href="<?php echo e(route('admin.invoice.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Buat Invoice</a>
    </div>
</div>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari No. Invoice / Klien..." value="<?php echo e(request('search')); ?>" style="min-width: 250px;"></div>
            <div class="col-auto">
                <select name="status" class="form-select"><option value="">Semua Status</option><?php $__currentLoopData = ['Draft','Sent','Paid','Canceled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php echo e(request('status')==$s?'selected':''); ?>><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            <?php if(request()->hasAny(['search','status'])): ?><div class="col-auto"><a href="<?php echo e(route('admin.invoice.index')); ?>" class="btn btn-outline-secondary">Reset</a></div><?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>No. Invoice</th><th>Klien</th><th>Periode</th><th>Total</th><th>Status</th><th>Tgl Invoice</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($item->nomor_invoice ?? '-'); ?></strong></td>
                        <td><?php echo e($item->klien->nama_klien ?? '-'); ?></td>
                        <td><?php echo e($item->periode_bulan); ?>/<?php echo e($item->periode_tahun); ?></td>
                        <td>Rp <?php echo e(number_format($item->total_tagihan, 0, ',', '.')); ?></td>
                        <td>
                            <?php $invColors = ['Paid'=>'success','Sent'=>'info','Draft'=>'warning','Canceled'=>'danger']; ?>
                            <span class="badge bg-<?php echo e($invColors[$item->status] ?? 'secondary'); ?>"><?php echo e($item->status); ?></span>
                        </td>
                        <td><?php echo e(\Carbon\Carbon::parse($item->tanggal_invoice)->format('d/m/Y')); ?></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('invoices.print', $item)); ?>" target="_blank" class="btn btn-outline-success" title="Cetak"><i class="cil-print"></i></a>
                                <a href="<?php echo e(route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\Invoice'), 'ref_id' => $item->id])); ?>" class="btn btn-outline-info" title="Buat Jurnal"><i class="cil-book"></i></a>
                                <a href="<?php echo e(route('admin.invoice.edit', $item)); ?>" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.invoice.destroy', $item)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
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
    <?php if($invoices->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($invoices->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/invoice/index.blade.php ENDPATH**/ ?>