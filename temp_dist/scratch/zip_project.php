<?php
// PHP Script to create a standard ZIP archive compatible with cPanel Linux unzip

$sourceDir = realpath(__DIR__ . '/..');
$tempDir = $sourceDir . '/temp_dist';
$zipPath = $sourceDir . '/lms-taxcenter-dist-with-vendor.zip';

// 1. Clean up old temp & zip
if (file_exists($tempDir)) {
    deleteDirectory($tempDir);
}
if (file_exists($zipPath)) {
    unlink($zipPath);
}

mkdir($tempDir, 0777, true);

// 2. Copy files recursively excluding git, node_modules, temp, .env, and db
$excludeList = ['.git', 'node_modules', 'temp_dist', '.env', '.phpunit.result.cache', 'lms-taxcenter-dist-with-vendor.zip', 'lms-taxcenter-dist.zip', 'update-lms-fixed.zip', 'lms-taxcenter-update.zip'];

$dirIterator = new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $item) {
    $relativePath = substr($item->getPathname(), strlen($sourceDir) + 1);
    
    // Check if path starts with any excluded directory/file
    $shouldExclude = false;
    foreach ($excludeList as $exclude) {
        if ($relativePath === $exclude || strpos($relativePath, $exclude . DIRECTORY_SEPARATOR) === 0 || strpos($relativePath, $exclude . '/') === 0) {
            $shouldExclude = true;
            break;
        }
    }
    
    // Exclude any sqlite files inside database folder
    if (strpos($relativePath, 'database' . DIRECTORY_SEPARATOR) === 0 || strpos($relativePath, 'database/') === 0) {
        if (pathinfo($item->getFilename(), PATHINFO_EXTENSION) === 'sqlite') {
            $shouldExclude = true;
        }
    }
    
    if ($shouldExclude) {
        continue;
    }
    
    $destPath = $tempDir . DIRECTORY_SEPARATOR . $relativePath;
    
    if ($item->isDir()) {
        if (!file_exists($destPath)) {
            mkdir($destPath, 0777, true);
        }
    } else {
        $parentDir = dirname($destPath);
        if (!file_exists($parentDir)) {
            mkdir($parentDir, 0777, true);
        }
        copy($item->getPathname(), $destPath);
    }
}

// 3. Create ZipArchive
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    $count = 0;
    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen(realpath($tempDir)) + 1);
            // Replace backslashes with forward slashes for Linux compatibility
            $relativePath = str_replace('\\', '/', $relativePath);
            $zip->addFile($filePath, $relativePath);
            $count++;
        }
    }
    $zip->close();
    echo "SUCCESS: ZIP archive created successfully with {$count} files.\n";
} else {
    echo "ERROR: Failed to create ZIP archive.\n";
}

// 4. Clean up temp directory
deleteDirectory($tempDir);

// Helper function to recursively delete directory
function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}
