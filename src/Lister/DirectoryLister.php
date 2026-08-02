<?php
namespace Avetify\Lister;

use Avetify\Files\Filer;
use Avetify\Lister\Models\DirectoryImageItem;

class DirectoryLister extends JsonLister {
    public function __construct(string $key, public string $directoryPath) {
        $items = [];
        $rawImages = Filer::subImages($this->directoryPath);

        foreach ($rawImages as $rawImage){
            $entityImage = new DirectoryImageItem($rawImage);
            $items[] = $entityImage;
        }

        parent::__construct($key, $items);
    }

    public function getPageTitle(): string {
        $pureDirectory = Filer::getPureFilename($this->directoryPath);
        return ucfirst($pureDirectory);
    }

    public function getJsonStorageFilePath(): string {
        $normDirPath = !str_ends_with($this->directoryPath, "/") ? $this->directoryPath . "/" : $this->directoryPath;
        return $normDirPath . ".avt_lister.json";
    }
}