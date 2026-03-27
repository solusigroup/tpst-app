<?php $__env->startSection('title', isset($jurnal) ? 'Edit Jurnal' : 'Tambah Jurnal'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1><?php echo e(isset($jurnal) ? 'Edit' : 'Tambah'); ?> Jurnal</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?php echo e(route('admin.jurnal.index')); ?>">Jurnal</a></li><li class="breadcrumb-item active"><?php echo e(isset($jurnal) ? 'Edit' : 'Tambah'); ?></li></ol></nav>
    </div>
</div>

<form method="POST" action="<?php echo e(isset($jurnal) ? route('admin.jurnal.update', $jurnal) : route('admin.jurnal.store')); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?> <?php if(isset($jurnal)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <?php if(isset($refType) && isset($refId)): ?>
        <input type="hidden" name="referensi_type" value="<?php echo e($refType); ?>">
        <input type="hidden" name="referensi_id" value="<?php echo e($refId); ?>">
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Informasi Jurnal</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('tanggal', isset($jurnal) ? \Carbon\Carbon::parse($jurnal->tanggal)->format('Y-m-d') : '')); ?>" required>
                            <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Referensi</label>
                            <input type="text" class="form-control bg-light" value="<?php echo e($jurnal->nomor_referensi ?? 'Otomatis'); ?>" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"><?php echo e(old('deskripsi', $jurnal->deskripsi ?? ($defaultDeskripsi ?? ''))); ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bukti Transaksi</label>
                            <input type="file" name="bukti_transaksi" class="form-control" accept="image/*">
                            <?php if(isset($jurnal) && $jurnal->bukti_transaksi): ?>
                                <img src="<?php echo e(asset('storage/' . $jurnal->bukti_transaksi)); ?>" class="mt-2 rounded" style="max-height:100px;" alt="bukti">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Detail Jurnal</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()"><i class="cil-plus me-1"></i> Tambah Baris</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="detail-table">
                            <thead class="bg-light">
                            <thead class="bg-light">
                                <tr><th>Akun</th><th>Mitra (Opsional)</th><th style="width:180px;">Debit</th><th style="width:180px;">Kredit</th><th style="width:50px;"></th></tr>
                            </thead>
                            <tbody id="detail-body">
                                <?php if(isset($jurnal) && $jurnal->jurnalDetails->count()): ?>
                                    <?php $__currentLoopData = $jurnal->jurnalDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <select name="details[<?php echo e($i); ?>][coa_id]" class="form-select form-select-sm" required>
                                                <option value="">-- Pilih --</option>
                                                <?php $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php echo e($detail->coa_id == $c->id ? 'selected' : ''); ?>><?php echo e($c->kode_akun); ?> - <?php echo e($c->nama_akun); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="details[<?php echo e($i); ?>][contactable_type_id]" class="form-select form-select-sm">
                                                <option value="">-- Tanpa Mitra --</option>
                                                <optgroup label="Klien">
                                                    <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="App\Models\Klien:<?php echo e($k->id); ?>" <?php echo e(($detail->contactable_type === 'App\Models\Klien' && $detail->contactable_id == $k->id) ? 'selected' : ''); ?>><?php echo e($k->nama_klien); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                                <optgroup label="Vendor">
                                                    <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="App\Models\Vendor:<?php echo e($v->id); ?>" <?php echo e(($detail->contactable_type === 'App\Models\Vendor' && $detail->contactable_id == $v->id) ? 'selected' : ''); ?>><?php echo e($v->nama_vendor); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                            </select>
                                        </td>
                                        <td><input type="number" name="details[<?php echo e($i); ?>][debit]" class="form-control form-control-sm debit-input" value="<?php echo e($detail->debit); ?>" oninput="updateTotals()"></td>
                                        <td><input type="number" name="details[<?php echo e($i); ?>][kredit]" class="form-control form-control-sm kredit-input" value="<?php echo e($detail->kredit); ?>" oninput="updateTotals()"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td><select name="details[0][coa_id]" class="form-select form-select-sm" required><option value="">-- Pilih --</option><?php $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->kode_akun); ?> - <?php echo e($c->nama_akun); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></td>
                                        <td>
                                            <select name="details[0][contactable_type_id]" class="form-select form-select-sm">
                                                <option value="">-- Tanpa Mitra --</option>
                                                <optgroup label="Klien"><?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="App\Models\Klien:<?php echo e($k->id); ?>"><?php echo e($k->nama_klien); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></optgroup>
                                                <optgroup label="Vendor"><?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="App\Models\Vendor:<?php echo e($v->id); ?>"><?php echo e($v->nama_vendor); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></optgroup>
                                            </select>
                                        </td>
                                        <td><input type="number" name="details[0][debit]" class="form-control form-control-sm debit-input" value="<?php echo e(old('details.0.debit', rtrim(rtrim(number_format($defaultNominal ?? 0, 2, '.', ''), '0'), '.') ?: 0)); ?>" oninput="updateTotals()"></td>
                                        <td><input type="number" name="details[0][kredit]" class="form-control form-control-sm kredit-input" value="0" oninput="updateTotals()"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td><select name="details[1][coa_id]" class="form-select form-select-sm" required><option value="">-- Pilih --</option><?php $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->kode_akun); ?> - <?php echo e($c->nama_akun); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></td>
                                        <td>
                                            <select name="details[1][contactable_type_id]" class="form-select form-select-sm">
                                                <option value="">-- Tanpa Mitra --</option>
                                                <optgroup label="Klien"><?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="App\Models\Klien:<?php echo e($k->id); ?>"><?php echo e($k->nama_klien); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></optgroup>
                                                <optgroup label="Vendor"><?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="App\Models\Vendor:<?php echo e($v->id); ?>"><?php echo e($v->nama_vendor); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></optgroup>
                                            </select>
                                        </td>
                                        <td><input type="number" name="details[1][debit]" class="form-control form-control-sm debit-input" value="0" oninput="updateTotals()"></td>
                                        <td><input type="number" name="details[1][kredit]" class="form-control form-control-sm kredit-input" value="<?php echo e(old('details.1.kredit', rtrim(rtrim(number_format($defaultNominal ?? 0, 2, '.', ''), '0'), '.') ?: 0)); ?>" oninput="updateTotals()"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="fw-bold text-end" colspan="2">Total</td>
                                    <td class="fw-bold" id="total-debit">0</td>
                                    <td class="fw-bold" id="total-kredit">0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="balance-alert" class="alert alert-danger m-3 d-none">
                        <i class="cil-warning me-1"></i> Total debit dan kredit harus seimbang!
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> <?php echo e(isset($jurnal) ? 'Perbarui' : 'Simpan'); ?></button>
                    <a href="<?php echo e(route('admin.jurnal.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let rowIndex = <?php echo e(isset($jurnal) ? $jurnal->jurnalDetails->count() : 2); ?>;

function addRow() {
    const coaOptions = `<option value="">-- Pilih --</option><?php $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->kode_akun); ?> - <?php echo e($c->nama_akun); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>`;
    const mitraOptions = `<option value="">-- Tanpa Mitra --</option><optgroup label="Klien"><?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="App\\Models\\Klien:<?php echo e($k->id); ?>"><?php echo e($k->nama_klien); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></optgroup><optgroup label="Vendor"><?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="App\\Models\\Vendor:<?php echo e($v->id); ?>"><?php echo e($v->nama_vendor); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></optgroup>`;
    
    const row = `<tr>
        <td><select name="details[${rowIndex}][coa_id]" class="form-select form-select-sm" required>${coaOptions}</select></td>
        <td><select name="details[${rowIndex}][contactable_type_id]" class="form-select form-select-sm">${mitraOptions}</select></td>
        <td><input type="number" name="details[${rowIndex}][debit]" class="form-control form-control-sm debit-input" value="0" oninput="updateTotals()"></td>
        <td><input type="number" name="details[${rowIndex}][kredit]" class="form-control form-control-sm kredit-input" value="0" oninput="updateTotals()"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
    </tr>`;
    document.getElementById('detail-body').insertAdjacentHTML('beforeend', row);
    rowIndex++;
}

function updateTotals() {
    let totalDebit = 0, totalKredit = 0;
    document.querySelectorAll('.debit-input').forEach(el => totalDebit += parseFloat(el.value) || 0);
    document.querySelectorAll('.kredit-input').forEach(el => totalKredit += parseFloat(el.value) || 0);
    document.getElementById('total-debit').textContent = totalDebit.toLocaleString('id-ID');
    document.getElementById('total-kredit').textContent = totalKredit.toLocaleString('id-ID');
    document.getElementById('balance-alert').classList.toggle('d-none', Math.abs(totalDebit - totalKredit) < 0.01);
}

document.addEventListener('DOMContentLoaded', updateTotals);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/jurnal/form.blade.php ENDPATH**/ ?>