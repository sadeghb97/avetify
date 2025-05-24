<?php
namespace Avetify\Files;

use Avetify\Routing\Routing;

class RecycleCan {
    protected static string | null $canRepoFileName = null;
    protected static bool $initialised = false;
    protected static array|null $canFiles = null;

    protected static function init(){
        if(!self::$initialised) {
            self::$canRepoFileName = Routing::getBackupFilesDir() . "can.json";
            if(!file_exists(self::$canRepoFileName)){
                file_put_contents(self::$canRepoFileName, "{}");
            }

            self::$canFiles = json_decode(file_get_contents(self::$canRepoFileName), true);
            self::$initialised = true;
        }
    }

    public static function getCanFilename(string $fileKey) : string | null {
        self::init();
        if(isset(self::$canFiles[$fileKey]) && file_exists(self::$canFiles[$fileKey])){
            return self::$canFiles[$fileKey];
        }
        return null;
    }

    public static function removeCanFile(string $fileKey){
        self::init();
        $oldFile = self::getCanFilename($fileKey);
        if($oldFile){
            if(unlink($oldFile)){
                unset(self::$canFiles[$fileKey]);
                self::updateCanRepoFile();
            }
        }
    }

    public static function restoreCanFile(string $fileKey, string $targetFilename){
        self::init();
        $oldFile = self::getCanFilename($fileKey);
        if($oldFile){
            if(rename($oldFile, $targetFilename)){
                unset(self::$canFiles[$fileKey]);
                self::updateCanRepoFile();
            }
        }
    }

    public static function updateCanRepoFile(){
        self::init();
        file_put_contents(self::$canRepoFileName, json_encode(self::$canFiles));
    }

    public static function saveBackupFile(string $fileKey, string $filePath){
        self::init();
        $ext = Filer::getFileExtension($filePath);
        $backupFilename = Routing::getBackupFilesDir() . time();
        if($ext) $backupFilename .= ("." . $ext);

        self::removeCanFile($fileKey);
        if(copy($filePath, $backupFilename)){
            self::$canFiles[$fileKey] = $backupFilename;
            self::updateCanRepoFile();
        };
    }
}
