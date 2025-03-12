<?php

function deleteDirectory($dir) : bool {
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), ['.', '..']);

    foreach ($files as $file) {
        $filePath = "$dir/$file";
        if (is_dir($filePath)) {
            deleteDirectory($filePath);  // Recursively delete subdirectory
        } else {
            unlink($filePath);  // Delete file
        }
    }

    return rmdir($dir);  // Delete the directory itself
}

function getFileExtension($filename) : string {
    $pos = strrpos($filename, ".");
    if($pos < (strlen($filename) - 1)) return substr($filename, $pos + 1);
    return "";
}

function getDirFiles(string $directory): array {
    if (!is_dir($directory)) return [];

    $files = array_filter(scandir($directory), function ($file) use ($directory) {
        return is_file($directory . DIRECTORY_SEPARATOR . $file);
    });

    return array_values($files); // Re-index array
}
