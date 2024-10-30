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
