<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => null, 'date' => null, 'periode' => null]));

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

foreach (array_filter((['title', 'subtitle' => null, 'date' => null, 'periode' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="report-header text-center mb-6 pb-4 border-b-2 border-gray-800 dark:border-gray-200">
    <h1 class="text-lg font-bold uppercase tracking-wide text-gray-900 dark:text-white">
        <?php echo e(auth()->user()->tenant->name ?? 'Perusahaan'); ?>

    </h1>
    <h2 class="text-base font-bold uppercase mt-1 text-gray-800 dark:text-gray-100"><?php echo e($title); ?></h2>
    <?php if($date): ?>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Per <?php echo e(\Carbon\Carbon::parse($date)->translatedFormat('d F Y')); ?></p>
    <?php endif; ?>
    <?php if($periode): ?>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5"><?php echo e($periode); ?></p>
    <?php endif; ?>
    <?php if($subtitle): ?>
        <p class="text-xs text-gray-500 dark:text-gray-500 italic mt-0.5"><?php echo e($subtitle); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\components\report-header.blade.php ENDPATH**/ ?>