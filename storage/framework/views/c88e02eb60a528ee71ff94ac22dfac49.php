<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['tenant' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['tenant' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $tenant = $tenant ?? auth()->user()->tenant;
?>
<div class="mb-4 border-bottom border-dark pb-3 text-center">
    <h1 class="display-6 fw-bold text-uppercase text-dark mb-1" style="letter-spacing: -1px;">
        <?php echo e(!empty($tenant?->name) ? $tenant->name : 'PT Tatabumi Adilimbah'); ?>

    </h1>
    <p class="small text-secondary fw-medium mb-0" style="line-height: 1.6;">
        <?php echo e($tenant?->address ?? ''); ?><br>
        <?php if($tenant?->email): ?> Email: <?php echo e($tenant?->email); ?> <?php endif; ?>
        <?php if($tenant?->bank_name): ?> 
            <span class="mx-2 text-black-50 fw-light">|</span> 
            Bank: <?php echo e($tenant?->bank_name); ?> - <?php echo e($tenant?->bank_account_number); ?> 
            (<?php echo e($tenant?->bank_account_name); ?>)
        <?php endif; ?>
    </p>
</div>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/components/kop-surat.blade.php ENDPATH**/ ?>