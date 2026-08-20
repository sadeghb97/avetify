<?php
namespace Avetify\Files;

use InvalidArgumentException;

class Filer {
    const IMAGE_EXTENSIONS = ["jpg", "jpeg", "png", "webp", "avif", "gif"];

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
        if($pos !== false && $pos < (strlen($filename) - 1)) return substr($filename, $pos + 1);
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

    public static function subFiles(string $path, ?array $targetExtensions = null): array {
        $files = self::dirSubFiles($path, "files");
        if($targetExtensions == null) return $files;

        $chosenFiles = [];
        foreach ($files as $file){
            $ext = self::getFileExtension($file);
            if(in_array($ext, $targetExtensions)) $chosenFiles[] = $file;
        }
        return $chosenFiles;
    }

    private static function _subFilesRecursive(array &$chosenFiles, string $path, ?array $targetExtensions = null): array {
        $newFiles = self::subFiles($path, $targetExtensions);
        $chosenFiles = array_merge($chosenFiles, $newFiles);

        $subDirs = Filer::subDirs($path);
        foreach ($subDirs as $subDir){
            self::_subFilesRecursive($chosenFiles, $subDir, $targetExtensions);
        }

        return $chosenFiles;
    }

    public static function subFilesRecursive(string $path, ?array $targetExtensions = null): array {
        $chosenFiles = [];
        return self::_subFilesRecursive($chosenFiles, $path, $targetExtensions);
    }

    public static function subImages(string $path): array {
        return self::subFiles($path, self::IMAGE_EXTENSIONS);
    }

    public static function subImagesRecursive(string $path): array {
        return self::subFilesRecursive($path, self::IMAGE_EXTENSIONS);
    }

    public static function isImageExtension(string $filePath) : bool {
        $ext = self::getFileExtension($filePath);
        return in_array($ext, self::IMAGE_EXTENSIONS);
    }

    public static function subDirs(string $path): array {
        return self::dirSubFiles($path, "dirs");
    }
}