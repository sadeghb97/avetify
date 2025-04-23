<?php

class GalleryRepo {
    private static array $extensions = ["jpg", "jpeg", "png", "webp"];

    public array $virtualFolders = [];
    public string $path;
    public string $relativePath = "";
    public string $parentRelativePath = "";

    /** @var GalleryRepo[] $subRepos */
    public array $subRepos = [];

    /** @var GalleryRecord[] $allRecords */
    public array $allRecords = [];

    public null | GalleryRecord $cover = null;

    public function __construct(string $relativePath, public bool $recursive = true,
                                public bool $readOnly = false){
        if(!str_ends_with($relativePath, "/")) $relativePath = $relativePath . "/";
        $this->relativePath = $relativePath;
        $this->parentRelativePath = Filer::getParentFilename($relativePath);
        $this->path = Routing::serverRootPath($relativePath);
        $this->loadRecords();
    }

    public function loadRecords(){
        if(!file_exists($this->path)){
            if(!$this->readOnly) mkdir($this->path);
            else return;
        }

        $configs = self::createConfigsData([], []);
        $conFilename = self::getGalleryConfigFilename($this->path);
        if (!$this->readOnly && !file_exists($conFilename)) {
            $def = self::createConfigsData([], []);
            file_put_contents($conFilename, json_encode($def));
        }

        if(file_exists($conFilename)) {
            $rawConfs = file_get_contents($conFilename);
            $configs = json_decode($rawConfs, true);
        }

        $this->virtualFolders = [];
        $this->allRecords = [];

        $allVirtualGalNames = [];
        foreach ($configs['virtual_gals'] as $vgKey => $vg){
            $allVirtualGalNames[] = $vgKey;
        }
        natcasesort($allVirtualGalNames);

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

        if($this->recursive) {
            $dirs = Filer::subDirs($this->path);
            natcasesort($dirs);
            foreach ($dirs as $dir) {
                $pureDir = Filer::getPureFilename($dir);
                $this->subRepos[] = new GalleryRepo($this->relativePath . $pureDir, false);
            }
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

        if(count($this->allRecords) > 0){
            $this->cover = $this->allRecords[0];
        }
    }

    public function arrangeRepo(){
        $virtualGalsIdMap = [];
        foreach ($this->virtualFolders as $vf){
            $virtualGalsIdMap[$vf['index']] = $vf['id'];
        }

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