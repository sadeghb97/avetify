<?php
namespace Avetify\Lister\Models;

use Avetify\Entities\AvtEntityItem;
use Avetify\Files\Filer;
use Avetify\Routing\Routing;

class DirectoryImageItem extends AvtEntityItem {
    public function __construct(public string $imageFilename) {
        parent::__construct([]);
    }

    public function getItemId(): string {
        return Filer::getPureFilename($this->imageFilename);
    }

    public function getItemTitle(): string {
        return Filer::getStarterFilename($this->imageFilename);
    }

    public function getItemImage(): string {
        return Routing::srpToBrp($this->imageFilename);
    }

    public function deleteAllResources() {}

    public function getItemLink(): string {
        return "";
    }
}
