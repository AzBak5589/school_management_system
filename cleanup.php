<?php

require __DIR__.'/vendor/autoload.php';

echo "Cleaning up Laravel application...\n";

// Clear bootstrap cache
$bootstrapCache = __DIR__.'/bootstrap/cache';
if (is_dir($bootstrapCache)) {
    echo "Clearing bootstrap cache...\n";
    $files = glob($bootstrapCache.'/*.php');
    foreach ($files as $file) {
        echo "Removing: ".basename($file)."\n";
        @unlink($file);
    }
}

// Clear framework caches
$storagePaths = [
    __DIR__.'/storage/framework/views',
    __DIR__.'/storage/framework/cache',
    __DIR__.'/storage/framework/sessions',
];

foreach ($storagePaths as $path) {
    if (is_dir($path)) {
        echo "Clearing ".basename($path)."...\n";
        $files = glob($path.'/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}

echo "Ensuring storage directories exist...\n";
$storageStructure = [
    'app/public',
    'framework/cache/data',
    'framework/sessions',
    'framework/views',
    'logs',
];

foreach ($storageStructure as $dir) {
    $path = __DIR__.'/storage/'.$dir;
    if (!is_dir($path)) {
        echo "Creating: ".$dir."\n";
        mkdir($path, 0755, true);
    }
}

echo "Cleanup completed!\n";
echo "Now manually update config/app.php to include necessary service providers.\n";