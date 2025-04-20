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

function dirSubFiles(string $path, string $type = 'all'): array {
    $path = rtrim($path, '/\\'); // remove trailing / or \ depending on OS
    $items = glob($path . '/*');
    $result = [];

    foreach ($items as $item) {
        switch ($type) {
            case 'files':
                if (is_file($item)) {
                    $result[] = $item;
                }
                break;
            case 'dirs':
                if (is_dir($item) && !in_array(basename($item), ['.', '..'])) {
                    $result[] = $item;
                }
                break;
            case 'all':
                $result[] = $item;
                break;
            default:
                throw new InvalidArgumentException("Invalid type: $type. Allowed: files, dirs, all");
        }
    }

    return $result;
}
