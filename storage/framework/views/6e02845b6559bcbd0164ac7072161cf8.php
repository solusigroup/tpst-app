<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($title ?? 'Laporan'); ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; margin: 0; padding: 15px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .pt-3 { padding-top: 1rem; }
        .pb-3 { padding-bottom: 1rem; }
        .w-100 { width: 100%; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        th, td {
            padding: 0.5rem;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        th {
            background-color: #f8f9fa;
        }
        .table-borderless th, .table-borderless td {
            border: none;
        }
        .border-top { border-top: 1px solid #dee2e6; }
        .header-section { margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header-title { font-size: 18px; margin: 0; text-transform: uppercase; font-weight: bold; }
        .header-subtitle { font-size: 11px; margin-top: 5px; color: #555; }
    </style>
</head>
<body>
    <div class="header-section text-center">
        <?php
            $tenant = auth()->user() ? auth()->user()->tenant : null;
        ?>
        <h1 class="header-title"><?php echo e(!empty($tenant?->name) ? $tenant?->name : 'PT Tatabumi Adilimbah'); ?></h1>
        <p class="header-subtitle">
            <?php echo e($tenant?->address ?? ''); ?><br>
            <?php if(!empty($tenant?->email)): ?> Email: <?php echo e($tenant?->email); ?> <?php endif; ?>
            <?php if(!empty($tenant?->bank_name)): ?> 
                | Bank: <?php echo e($tenant?->bank_name); ?> - <?php echo e($tenant?->bank_account_number); ?> (<?php echo e($tenant?->bank_account_name); ?>)
            <?php endif; ?>
        </p>
    </div>

    <?php echo $__env->yieldContent('content'); ?>
</body>
</html>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/exports/layout.blade.php ENDPATH**/ ?>