<?php $__env->startSection('title', isset($penjualan) ? 'Edit Penjualan' : 'Tambah Penjualan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1><?php echo e(isset($penjualan) ? 'Edit' : 'Tambah'); ?> Penjualan</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?php echo e(route('admin.penjualan.index')); ?>">Penjualan</a></li><li class="breadcrumb-item active"><?php echo e(isset($penjualan) ? 'Edit' : 'Tambah'); ?></li></ol></nav>
    </div>
</div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="<?php echo e(isset($penjualan) ? route('admin.penjualan.update', $penjualan) : route('admin.penjualan.store')); ?>">
        <?php echo csrf_field(); ?> <?php if(isset($penjualan)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Klien <span class="text-danger">*</span></label>
                <select name="klien_id" class="form-select <?php $__errorArgs = ['klien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="">-- Pilih --</option>
                    <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k->id); ?>" <?php echo e(old('klien_id', $penjualan->klien_id ?? '') == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_klien); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['klien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('tanggal', isset($penjualan) ? \Carbon\Carbon::parse($penjualan->tanggal)->format('Y-m-d') : '')); ?>" required>
                <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-12">
                <label class="form-label">Jenis Produk <span class="text-danger">*</span></label>
                <select name="jenis_produk" id="jenis_produk" class="form-select <?php $__errorArgs = ['jenis_produk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required onchange="updateMaxStock()">
                    <option value="" data-stock="0">-- Pilih Jenis Pilahan --</option>
                    <?php $__currentLoopData = $stokPilahan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenis => $sisaStok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($jenis); ?>" data-stock="<?php echo e($sisaStok); ?>" <?php echo e(old('jenis_produk', $penjualan->jenis_produk ?? '') == $jenis ? 'selected' : ''); ?>>
                            <?php echo e($jenis); ?> (Stok: <?php echo e($sisaStok); ?> kg)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['jenis_produk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Berat (kg) <span class="text-danger">*</span> <span id="max_stock_label" class="badge bg-secondary ms-2" style="display:none;"></span></label>
                <input type="number" step="0.01" name="berat_kg" id="berat_kg" class="form-control <?php $__errorArgs = ['berat_kg'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('berat_kg', $penjualan->berat_kg ?? '')); ?>" required oninput="calcTotal(); checkStock()">
                <?php $__errorArgs = ['berat_kg'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="harga_satuan" id="harga_satuan" class="form-control <?php $__errorArgs = ['harga_satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('harga_satuan', $penjualan->harga_satuan ?? '')); ?>" required oninput="calcTotal()">
                <?php $__errorArgs = ['harga_satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Total Harga</label>
                <input type="text" id="total_harga_display" class="form-control bg-light" value="Rp <?php echo e(number_format(old('total_harga', $penjualan->total_harga ?? 0), 0, ',', '.')); ?>" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jumlah Bayar (DP/Tunai)</label>
                <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control <?php $__errorArgs = ['jumlah_bayar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('jumlah_bayar', $penjualan->jumlah_bayar ?? 0)); ?>" required oninput="calcTotal()">
                <?php $__errorArgs = ['jumlah_bayar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sisa (Piutang)</label>
                <input type="text" id="sisa_bayar_display" class="form-control bg-light" value="Rp 0" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Akun Pembayaran (Kas/Bank) <span class="text-danger">*</span></label>
                <select name="coa_pembayaran_id" class="form-select <?php $__errorArgs = ['coa_pembayaran_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <?php $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e(old('coa_pembayaran_id', $penjualan->coa_pembayaran_id ?? '') == $c->id ? 'selected' : ( !isset($penjualan) && str_contains($c->nama_akun, 'Bank') ? 'selected' : '' )); ?>>
                            <?php echo e($c->kode_akun); ?> - <?php echo e($c->nama_akun); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['coa_pembayaran_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> <?php echo e(isset($penjualan) ? 'Perbarui' : 'Simpan'); ?></button>
                <a href="<?php echo e(route('admin.penjualan.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function calcTotal() {
    const berat = parseFloat(document.getElementById('berat_kg').value) || 0;
    const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
    const bayar = parseFloat(document.getElementById('jumlah_bayar').value) || 0;
    
    const total = berat * harga;
    const sisa = total - bayar;
    
    document.getElementById('total_harga_display').value = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('sisa_bayar_display').value = 'Rp ' + Math.max(0, sisa).toLocaleString('id-ID');
}

let maxStock = 0;

function updateMaxStock() {
    const select = document.getElementById('jenis_produk');
    const selectedOption = select.options[select.selectedIndex];
    maxStock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
    
    const label = document.getElementById('max_stock_label');
    if (select.value) {
        label.textContent = 'Maksimal: ' + maxStock + ' kg';
        label.style.display = 'inline-block';
    } else {
        label.style.display = 'none';
    }
    
    document.getElementById('berat_kg').max = maxStock;
    checkStock();
}

function checkStock() {
    const input = document.getElementById('berat_kg');
    const val = parseFloat(input.value) || 0;
    
    if (document.getElementById('jenis_produk').value && val > maxStock) {
        input.classList.add('is-invalid');
        input.setCustomValidity('Stok tidak mencukupi! Maksimal ' + maxStock + ' kg');
    } else {
        input.classList.remove('is-invalid');
        input.setCustomValidity('');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateMaxStock();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/penjualan/form.blade.php ENDPATH**/ ?>