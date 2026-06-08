<?php

$target = __DIR__ . '/../vendor/mehedi-iitdu/core-component-repository/src/CoreComponentRepository.php';

if (! file_exists($target)) {
    fwrite(STDOUT, "ActiveITZone redirect patch skipped: package file not found.\n");
    exit(0);
}

$contents = file_get_contents($target);

if ($contents === false) {
    fwrite(STDERR, "ActiveITZone redirect patch failed: cannot read {$target}.\n");
    exit(1);
}

$redirect = "return redirect('https://activeitzone.com/activation/')->send();";
$replacement = 'return null;';

if (strpos($contents, $redirect) === false) {
    fwrite(STDOUT, "ActiveITZone redirect patch already applied.\n");
    exit(0);
}

$patched = str_replace($redirect, $replacement, $contents);

if (file_put_contents($target, $patched) === false) {
    fwrite(STDERR, "ActiveITZone redirect patch failed: cannot write {$target}.\n");
    exit(1);
}

fwrite(STDOUT, "ActiveITZone redirect patch applied.\n");
