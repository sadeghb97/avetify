<?php
namespace Avetify\Files;

use InvalidArgumentException;

class Filer {
    public static function deleteDirectory($dir) : bool {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $filePath = "$dir/$file";
            if (is_dir($filePath)) {
                self::deleteDirectory($filePath);
            } else {
                unlink($filePath);  // Delete file
            }
        }

        return rmdir($dir);  // Delete the directory itself
    }

    public static function getFileExtension($filename) : string {
        $pos = strrpos($filename, ".");
        if($pos < (strlen($filename) - 1)) return substr($filename, $pos + 1);
        return "";
    }

    public static function pruneLastSlash(string $filename) : string {
        $cloneFilename = $filename;
        if(str_ends_with($cloneFilename, "/")){
            return substr($cloneFilename, 0, strlen($cloneFilename) - 1);
        }
        return $filename;
    }

    public static function getPureFilename($filename) : string {
        $cloneFilename = self::pruneLastSlash($filename);
        $pos = strrpos($cloneFilename, "/");
        if($pos === false) return $cloneFilename;
        if($pos == (strlen($cloneFilename) - 1)) return "";
        return substr($cloneFilename, $pos + 1);
    }

    public static function getStarterFilename($filename) : string {
        $cloneFilename = self::getPureFilename($filename);
        $pos = strrpos($cloneFilename, ".");
        if($pos === false) return $cloneFilename;
        return substr($cloneFilename, 0, $pos);
    }

    public static function getParentFilename($filename) : string {
        $cloneFilename = self::pruneLastSlash($filename);
        $pos = strrpos($cloneFilename, "/");
        if($pos == false) return "";
        return substr($cloneFilename, 0, $pos);
    }

    private static function dirSubFiles(string $path, string $type = 'all'): array {
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

    public static function pathContents(string $path): array {
        return self::dirSubFiles($path, "all");
    }

    public static function subFiles(string $path): array {
        return self::dirSubFiles($path, "files");
    }

    public static function subDirs(string $path): array {
        return self::dirSubFiles($path, "dirs");
    }
}