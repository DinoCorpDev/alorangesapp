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

$patterns = [
    '/public static function instantiateShopRepository\(\)\s*\{.*?\n    \}/s' => <<<'PHP'
public static function instantiateShopRepository() {
        return null;
    }
PHP,
    '/protected static function serializeObjectResponse\(\$zn,\s*\$request_data_json\)\s*\{.*?\n    \}/s' => <<<'PHP'
protected static function serializeObjectResponse($zn, $request_data_json) {
        return null;
    }
PHP,
    '/protected static function finalizeRepository\(\$rn\)\s*\{.*?\n    \}/s' => <<<'PHP'
protected static function finalizeRepository($rn) {
        return null;
    }
PHP,
    '/public static function initializeCache\(\)\s*\{.*?\n    \}/s' => <<<'PHP'
public static function initializeCache() {
        return null;
    }
PHP,
    '/public static function finalizeCache\(\$addon\)\s*\{.*?\n    \}/s' => <<<'PHP'
public static function finalizeCache($addon){
        return null;
    }
PHP,
];

$patched = $contents;

foreach ($patterns as $pattern => $replacement) {
    $patched = preg_replace($pattern, $replacement, $patched, 1);
}

$patched = str_replace("return redirect('https://activeitzone.com/activation/')->send();", 'return null;', $patched);

if ($patched === $contents) {
    fwrite(STDOUT, "ActiveITZone redirect patch already applied.\n");
    exit(0);
}

if (file_put_contents($target, $patched) === false) {
    fwrite(STDERR, "ActiveITZone redirect patch failed: cannot write {$target}.\n");
    exit(1);
}

fwrite(STDOUT, "ActiveITZone redirect patch applied.\n");
