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
    $date = now()->translatedFormat('d F Y');
?>

<div class="mt-12 grid grid-cols-3 gap-8 text-center text-sm">
    <div>
        <p class="mb-20">Mengetahui,</p>
        <div class="border-b border-gray-400 dark:border-gray-600 w-3/4 mx-auto pb-1 font-bold">
            <?php echo e($tenant->director_name ?? '..........................'); ?>

        </div>
        <p class="text-xs text-gray-500 mt-1">&nbsp;</p>
    </div>
    
    <div>
        <p class="mb-20">Diperiksa Oleh,</p>
        <div class="border-b border-gray-400 dark:border-gray-600 w-3/4 mx-auto pb-1 font-bold">
            <?php echo e($tenant->manager_name ?? '..........................'); ?>

        </div>
        <p class="text-xs text-gray-500 mt-1">&nbsp;</p>
    </div>

    <div>
        <p class="mb-20">Lamongan, <?php echo e($date); ?><br>Dibuat Oleh,</p>
        <div class="border-b border-gray-400 dark:border-gray-600 w-3/4 mx-auto pb-1 font-bold">
            <?php echo e($tenant->finance_name ?? '..........................'); ?>

        </div>
        <p class="text-xs text-gray-500 mt-1">&nbsp;</p>
    </div>
</div>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\components\report-signatures.blade.php ENDPATH**/ ?>