<?php

/**
 * Prepare Package Script
 *
 * This script copies the current boilerplate files to the stubs directory
 * for the Composer package structure.
 */

$sourceDirs = [
    'app' => 'stubs/app',
    'config' => 'stubs/config',
    'database' => 'stubs/database',
    'routes' => 'stubs/routes',
    'resources' => 'stubs/resources',
    'public' => 'stubs/public',
];

$sourceFiles = [
    '.env.example' => 'stubs/.env.example',
    'vite.config.js' => 'stubs/vite.config.js',
    'package.json' => 'stubs/package.json',
];

echo "Preparing package structure...\n";

// Copy directories
foreach ($sourceDirs as $source => $destination) {
    if (is_dir($source)) {
        echo "Copying {$source} to {$destination}...\n";
        copyDirectory($source, $destination);
    } else {
        echo "Warning: Source directory {$source} not found\n";
    }
}

// Copy individual files
foreach ($sourceFiles as $source => $destination) {
    if (file_exists($source)) {
        echo "Copying {$source} to {$destination}...\n";
        copy($source, $destination);
    } else {
        echo "Warning: Source file {$source} not found\n";
    }
}

echo "Package structure prepared successfully!\n";

/**
 * Recursively copy a directory
 */
function copyDirectory($source, $destination) {
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $sourcePath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;

            if (is_dir($sourcePath)) {
                copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }
    }
    closedir($dir);
}
