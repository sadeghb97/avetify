<?php

class GalleryRepo {
    private static array $extensions = ["jpg", "jpeg", "png"];

    public array $virtualFolders = [];

    /**
     * @var GalleryRecord[] $allRecords
     * An associative array
     */
    public array $allRecords = [];

    public function __construct(public string $path){
        $this->loadRecords();
    }

    public function loadRecords(){
        $conFilename = self::getGalleryConfigFilename($this->path);
        if(!file_exists($this->path)){
            mkdir($this->path);
        }

        if(!file_exists($conFilename)){
            $def = self::createConfigsData([], []);
            file_put_contents($conFilename, json_encode($def));
        }

        $rawConfs = file_get_contents($conFilename);
        $configs = json_decode($rawConfs, true);

        $this->virtualFolders = [];
        $this->allRecords = [];


        $allVirtualGalNames = [];
        foreach ($configs['virtual_gals'] as $vgKey => $vg){
            $allVirtualGalNames[] = $vgKey;
        }
        sort($allVirtualGalNames);

        $index = 1;
        foreach ($allVirtualGalNames as $vgKey){
            $virtualGallery = [];
            $virtualGallery['id'] = $vgKey;
            $virtualGallery['index'] = $index++;
            $this->virtualFolders[$vgKey] = $virtualGallery;
        }

        $files = [];
        foreach (self::$extensions as $ext) {
            $files = array_merge($files, glob($this->path . "*.$ext"));
        }

        $confRecords = $configs['items'];

        foreach ($files as $file){
            $adjustedFilename = Routing::prunePath($file, $this->path);

            if(isset($confRecords[$adjustedFilename])){
                $fileGalId = $confRecords[$adjustedFilename]['gid'];
                $imageIndex = $confRecords[$adjustedFilename]['ind'];
                $this->allRecords[] = new GalleryRecord($this->virtualFolders[$fileGalId]['index'],
                    $adjustedFilename, $imageIndex);
            }
            else {
                $this->allRecords[] = new GalleryRecord(0, $adjustedFilename, 0);
            }
        }
    }

    public function arrangeRepo(){
        //printPreArray($this->virtualFolders);
        //printPreArray($this->allRecords);

        $virtualGalsIdMap = [];
        foreach ($this->virtualFolders as $vf){
            $virtualGalsIdMap[$vf['index']] = $vf['id'];
        }
        //printPreArray($virtualGalsIdMap);

        foreach ($this->allRecords as $imageIndex => $record){
            if($record->galleryIndex > 0) {
                $currentServerFilename = $this->path . $record->path;
                $targetDir = $this->path . $virtualGalsIdMap[$record->galleryIndex] . '/';
                if (!file_exists($targetDir)) mkdir($targetDir);
                $simplifiedFilename = self::getSimplifiedFilename($currentServerFilename);
                $targetFilename = $targetDir . $simplifiedFilename;
                if(rename($currentServerFilename, $targetFilename)){
                    echo $currentServerFilename . ' -> ' . $targetFilename . '<br>';
                }
            }
        }
        $this->resetConfigs();
    }

    public static function getSimplifiedFilename($filename) : string {
        if(!str_contains($filename, "/")) return $filename;
        $pos = strrpos($filename, "/");
        if(strlen($filename) == ($pos + 1)) return $filename;
        return substr($filename, $pos + 1);
    }

    public function resetConfigs(){
        unlink(self::getGalleryConfigFilename($this->path));
        $this->loadRecords();
    }

    public function storeConfigs($vgMap, $imagesMap){
        $newConfigsData = self::createConfigsData($vgMap, $imagesMap);
        file_put_contents(self::getGalleryConfigFilename($this->path), json_encode($newConfigsData));
    }

    public static function createConfigsData($vgMap, $imagesMap) : array {
        return [
            "virtual_gals" => $vgMap,
            "items" => $imagesMap
        ];
    }

    public static function getGalleryConfigFilename($galleryPath) : string {
        return $galleryPath . '.galconfigs.json';
    }
}

class GalleryRecord {
    public function __construct(public int $galleryIndex, public string $path, public int $imageIndex){}
}