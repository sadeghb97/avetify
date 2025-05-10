<?php

class GalleryRepo {
    private static array $extensions = ["jpg", "jpeg", "png", "webp", "gif"];

    public array $originalVirtualGalsMap = [];
    public array $originalRecordsConfMap = [];

    public array $virtualFolders = [];
    public string $path;
    public string $relativePath = "";
    public string $parentRelativePath = "";

    /** @var GalleryRepo[] $subRepos */
    public array $subRepos = [];

    /** @var GalleryRecord[] $allRecords */
    public array $allRecords = [];

    public null | GalleryRecord $cover = null;
    public int $bigIndex = 0;

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
            $this->originalVirtualGalsMap = $configs['virtual_gals'];
            $this->originalRecordsConfMap = $configs['items'];
        }

        $this->virtualFolders = [];
        $this->allRecords = [];

        $allVirtualGalNames = [];
        foreach ($this->originalVirtualGalsMap as $vgKey => $vg){
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

        $confRecords = $this->originalRecordsConfMap;

        $this->bigIndex = count($files) + 1000;
        foreach ($files as $file){
            $adjustedFilename = Routing::prunePath($file, $this->path);

            $fileGalId = "";
            $imageIndex = $this->bigIndex;
            if(isset($confRecords[$adjustedFilename])){
                $fileGalId = $confRecords[$adjustedFilename]['gid'];
                $imageIndex = $confRecords[$adjustedFilename]['ind'];
            }

            $this->allRecords[] = new GalleryRecord(
                $fileGalId ? $this->virtualFolders[$fileGalId]['index'] : 0,
                $adjustedFilename, $imageIndex);
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

        foreach ($this->allRecords as $recordIndex => $record){
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

    public function renameRepo(){
        $virtualGalsIdMap = [];
        foreach ($this->virtualFolders as $vf){
            $virtualGalsIdMap[$vf['index']] = $vf['id'];
        }

        $recordsIndexMap = [];
        foreach ($this->allRecords as $recordIndex => $record){
            $recordsIndexMap[$record->path] = $recordIndex;
        }

        foreach ($this->allRecords as $recordIndex => $record){
            $currentServerFilename = $this->path . $record->path;
            $pureFilename = Filer::getPureFilename($currentServerFilename);
            $orgExtension = Filer::getFileExtension($currentServerFilename);
            if($orgExtension) $orgExtension = "." . $orgExtension;
            $vgPreName = $record->galleryIndex > 0 ?
                $virtualGalsIdMap[$record->galleryIndex] . "_" : "";
            $targetIndex = $record->imageIndex + 1;
            $targetPureStarterName = $vgPreName . $targetIndex;
            $targetPureFilename = $targetPureStarterName . $orgExtension;
            $targetFilename = $this->path . $targetPureFilename;

            if($currentServerFilename != $targetFilename) {
                if(file_exists($targetFilename)){
                    $tRes = $this->temporaryRename($targetFilename, $targetPureFilename,
                        $targetPureStarterName, $orgExtension,
                        $this->allRecords, $recordsIndexMap);

                    if(!$tRes){
                        Printer::warningPrint("Something went wrong!");
                        return;
                    }
                }

                if (rename($currentServerFilename, $targetFilename)) {
                    echo $currentServerFilename . ' -> ' . $targetFilename . '<br>';
                    if (isset($this->originalRecordsConfMap[$pureFilename])) {
                        $imageDetails = $this->originalRecordsConfMap[$pureFilename];
                        unset($this->originalRecordsConfMap[$pureFilename]);
                        $this->originalRecordsConfMap[$targetPureFilename] = $imageDetails;
                    }
                }
            }

            $this->storeConfigs($this->originalVirtualGalsMap, $this->originalRecordsConfMap);
            $this->loadRecords();
        }
    }

    public function temporaryRename($fullFn, $pureFn, $starter, $extension, &$recs, &$indexMap) : bool {
        $newStarter = $starter . "_" . time();
        $newPureFn = $newStarter . $extension;
        $newFullFn = $this->path . $newPureFn;

        echo "Temp Renaming $fullFn ---> $newFullFn" . br();

        if(rename($fullFn, $newFullFn)){
            $recIndex = $indexMap[$pureFn];
            $recs[$recIndex]->path = $newPureFn;
            unset($indexMap[$pureFn]);
            $indexMap[$newPureFn] = $recIndex;

            if(!empty($this->originalRecordsConfMap[$pureFn])){
                $details = $this->originalRecordsConfMap[$pureFn];
                unset($this->originalRecordsConfMap[$pureFn]);
                $this->originalRecordsConfMap[$newPureFn] = $details;
                return true;
            }
        }
        return false;
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