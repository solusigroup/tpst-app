<?php
header('Content-Type: text/plain');

function run($cmd) {
    echo "Executing: $cmd\n";
    $output = [];
    $return_var = 0;
    exec($cmd . ' 2>&1', $output, $return_var);
    echo implode("\n", $output) . "\n";
    echo "Return Code: $return_var\n\n";
    return $return_var;
}

// 1. Delete public/run_git.php if it exists
if (file_exists('run_git.php')) {
    unlink('run_git.php');
    echo "Deleted run_git.php\n\n";
}

// 2. Set Git Config to avoid errors
run('git config user.email "developer@solusigroup.co.id"');
run('git config user.name "Solusi Group Developer"');

// 3. Git status
run('git status');

// 4. Git add all modified/new files
run('git add .');

// 5. Git status again to verify
run('git status');

// 6. Git commit
run('git commit -m "Add Statistik Komparatif module"');

// 7. Set remote URL
run('git remote set-url origin https://github.com/solusigroup/tpst-app.git');

// 8. Git push
run('git push origin main');

// 9. Self-destruct
unlink(__FILE__);
echo "Deleted git_push.php self-destructed successfully!\n";
