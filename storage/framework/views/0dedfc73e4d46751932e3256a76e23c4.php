<?php $__env->startSection('title', isset($invoice) ? 'Edit Invoice' : 'Buat Invoice'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header"><div><h1><?php echo e(isset($invoice) ? 'Edit' : 'Buat'); ?> Invoice</h1></div></div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="<?php echo e(isset($invoice) ? route('admin.invoice.update', $invoice) : route('admin.invoice.store')); ?>">
        <?php echo csrf_field(); ?> <?php if(isset($invoice)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
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
                    <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k->id); ?>" <?php echo e(old('klien_id', $invoice->klien_id ?? '') == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_klien); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select><?php $__errorArgs = ['klien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Bulan <span class="text-danger">*</span></label>
                <select name="periode_bulan" class="form-select" required>
                    <?php $__currentLoopData = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e(old('periode_bulan', $invoice->periode_bulan ?? '') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                <select name="periode_tahun" class="form-select" required>
                    <?php for($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e(old('periode_tahun', $invoice->periode_tahun ?? '') == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tgl Invoice <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_invoice" class="form-control <?php $__errorArgs = ['tanggal_invoice'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('tanggal_invoice', isset($invoice) ? \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('Y-m-d') : date('Y-m-d'))); ?>" required>
                <?php $__errorArgs = ['tanggal_invoice'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_jatuh_tempo" class="form-control" value="<?php echo e(old('tanggal_jatuh_tempo', isset($invoice) ? \Carbon\Carbon::parse($invoice->tanggal_jatuh_tempo)->format('Y-m-d') : date('Y-m-d', strtotime('+14 days')))); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Total Tagihan (Rp) <span class="text-danger">*</span></label>
                <input type="number" id="total_tagihan" name="total_tagihan" class="form-control <?php $__errorArgs = ['total_tagihan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('total_tagihan', $invoice->total_tagihan ?? '0')); ?>" required readonly>
                <small class="text-muted">Dihitung otomatis berdasarkan item yang dipilih.</small>
                <?php $__errorArgs = ['total_tagihan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <?php $__currentLoopData = ['Draft','Sent','Paid','Canceled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php echo e(old('status', $invoice->status ?? 'Draft') == $s ? 'selected' : ''); ?>><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-12 mt-4">
                <h5 class="border-bottom pb-2">Item Tertagih</h5>
                <div id="loading-items" class="text-muted" style="display: none;">Memuat data...</div>
                <div id="no-items" class="text-muted" style="display: none;">Pilih Klien untuk melihat item yang belum ditagihkan.</div>
                
                <div id="ritase-container" class="mb-3" style="display: none;">
                    <strong>Ritase (Tipping Fee)</strong>
                    <div class="mt-2" id="ritase-list"></div>
                </div>

                <div id="penjualan-container" class="mb-3" style="display: none;">
                    <strong>Penjualan (Hasil Pilahan)</strong>
                    <div class="mt-2" id="penjualan-list"></div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <label class="form-label">Deskripsi Layanan (Opsional)</label>
                <textarea name="deskripsi_layanan" class="form-control" rows="2" placeholder="Gunakan untuk menimpa deskripsi otomatis pada saat cetak invoice"><?php echo e(old('deskripsi_layanan', $invoice->deskripsi_layanan ?? '')); ?></textarea>
                <small class="text-muted">Jika diisi, teks ini akan muncul sebagai rincian utama di PDF. Jika dikosongkan, PDf akan merincikan otomatis berdasarkan rekapan Ritase/Penjualan.</small>
            </div>
            <div class="col-12">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3"><?php echo e(old('keterangan', $invoice->keterangan ?? '')); ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> <?php echo e(isset($invoice) ? 'Perbarui' : 'Simpan'); ?></button>
                <a href="<?php echo e(route('admin.invoice.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const klienSelect = document.querySelector('select[name="klien_id"]');
    const loadingDiv = document.getElementById('loading-items');
    const noItemsDiv = document.getElementById('no-items');
    const ritaseContainer = document.getElementById('ritase-container');
    const ritaseList = document.getElementById('ritase-list');
    const penjualanContainer = document.getElementById('penjualan-container');
    const penjualanList = document.getElementById('penjualan-list');
    const totalTagihanInput = document.getElementById('total_tagihan');
    
    // Check if we are editing an invoice
    const invoiceId = "<?php echo e(isset($invoice) ? $invoice->id : ''); ?>";

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
            total += parseFloat(cb.dataset.price);
        });
        totalTagihanInput.value = total;
    }

    function fetchItems() {
        const klienId = klienSelect.value;
        if (!klienId) {
            ritaseContainer.style.display = 'none';
            penjualanContainer.style.display = 'none';
            noItemsDiv.style.display = 'block';
            return;
        }

        loadingDiv.style.display = 'block';
        noItemsDiv.style.display = 'none';
        ritaseList.innerHTML = '';
        penjualanList.innerHTML = '';
        totalTagihanInput.value = 0; // reset calculated total until fetched

        let url = `<?php echo e(route('admin.invoice-items.pending')); ?>?klien_id=${klienId}`;
        if (invoiceId) url += `&invoice_id=${invoiceId}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                loadingDiv.style.display = 'none';
                
                let hasItems = false;

                // Handle Ritase
                if (data.ritase && data.ritase.length > 0) {
                    hasItems = true;
                    ritaseContainer.style.display = 'block';
                    data.ritase.forEach(item => {
                        const checked = item.selected ? 'checked' : '';
                        ritaseList.innerHTML += `
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" name="selected_ritase[]" value="${item.id}" id="ritase_${item.id}" data-price="${item.price}" ${checked}>
                                <label class="form-check-label" for="ritase_${item.id}">
                                    ${item.label}
                                </label>
                            </div>
                        `;
                    });
                } else {
                    ritaseContainer.style.display = 'none';
                }

                // Handle Penjualan
                if (data.penjualan && data.penjualan.length > 0) {
                    hasItems = true;
                    penjualanContainer.style.display = 'block';
                    data.penjualan.forEach(item => {
                        const checked = item.selected ? 'checked' : '';
                        penjualanList.innerHTML += `
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" name="selected_penjualan[]" value="${item.id}" id="penjualan_${item.id}" data-price="${item.price}" ${checked}>
                                <label class="form-check-label" for="penjualan_${item.id}">
                                    ${item.label}
                                </label>
                            </div>
                        `;
                    });
                } else {
                    penjualanContainer.style.display = 'none';
                }

                if (!hasItems) {
                    noItemsDiv.style.display = 'block';
                    noItemsDiv.textContent = 'Tidak ada tagihan tertunda untuk klien ini.';
                } else {
                    // Attach change event listeners to checkboxes for live re-calculation
                    document.querySelectorAll('.item-checkbox').forEach(cb => {
                        cb.addEventListener('change', calculateTotal);
                    });
                    // Initial calculation for pre-selected items (during edit mode)
                    calculateTotal();
                }
            })
            .catch(err => {
                console.error('Error fetching items:', err);
                loadingDiv.style.display = 'none';
                noItemsDiv.style.display = 'block';
                noItemsDiv.textContent = 'Gagal memuat data. Silakan coba lagi.';
            });
    }

    klienSelect.addEventListener('change', fetchItems);
    
    // Trigger automatically on page load to fetch existing items if Klien is pre-filled
    if (klienSelect.value) {
        fetchItems();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/invoice/form.blade.php ENDPATH**/ ?>