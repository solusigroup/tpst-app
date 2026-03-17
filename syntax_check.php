<?php

$directories = [
    'd:\PROJECT_HERD\tpst-app\app\Models',
    'd:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin',
    'd:\PROJECT_HERD\tpst-app\app\Policies',
    'd:\PROJECT_HERD\tpst-app\app\Services'
];

$passed = 0;
$errors = 0;
$error_details = [];

foreach ($directories as $dir) {
    $files = @scandir($dir);
    if ($files === false) continue;
    
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            $output = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
            
            if (strpos($output, 'No syntax errors detected') === false) {
                $errors++;
                $error_details[] = "❌ " . $filePath . "\n" . $output;
                echo "❌ " . $filePath . "\n";
            } else {
                $passed++;
            }
        }
    }
}

echo "\n";
echo "Summary: " . $passed . " passed, " . $errors . " failed\n";

if ($errors === 0) {
    echo "✅ All files have valid PHP syntax!\n";
} else {
    echo "❌ " . $errors . " file(s) with syntax errors\n";
    echo "\nDetails:\n";
    foreach ($error_details as $detail) {
        echo $detail . "\n";
    }
}
