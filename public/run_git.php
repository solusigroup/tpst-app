<?php
header('Content-Type: text/plain');
$output = [];
$return_var = 0;
exec('git status 2>&1', $output, $return_var);
echo "Return var: $return_var\n";
echo implode("\n", $output);
