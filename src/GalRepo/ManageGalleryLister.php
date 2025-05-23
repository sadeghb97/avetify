<?php
namespace Avetify\GalRepo;

use Avetify\Lister\AvtLister;
use Avetify\Lister\ListerCategory;
use Avetify\Routing\Routing;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\ListerRenderer;

class ManageGalleryLister extends AvtLister {
    public int $maxTitleLength = 20;

    public function __construct(string $key, public GalleryRepo $galleryRepo){
        parent::__construct($key, $this->galleryRepo->allRecords);
        $this->placeDefaultTriggers = false;
    }

    public function getListerRenderer(): ListerRenderer {
        $th = new GreenTheme();
        $th->includesListerTools = true;
        return new GreenGalleryRenderer($this, $th);
    }

    public function getItemId($record): string {
        return $record->path;
    }

    public function getItemTitle($record): string {
        $p = $record->path;

        if(str_ends_with($p, "/")){
            $p = substr($p, 0, strlen($p) - 1);
        }

        if(!str_contains($p, "/")) return $p;

        $pos = strrpos($p, "/");
        return substr($p, $pos + 1);
    }

    public function getItemLink($record): string {
        return $this->getItemImage($record);
    }

    public function getItemImage($record): string {
        if(!$record) return "";
        return Routing::srpToBrp($this->galleryRepo->path . $record->path);
    }

    public function getItemCategoryOriginalPk($item){
        return $item->galleryIndex;
    }

    public function catOrgPkToListIndex($item): int {
        return $this->getItemCategoryOriginalPk($item);
    }

    public function listIndexToNewOrgPk($listIndex): int {
        return $listIndex;
    }

    public function getSecondarySortFactor($item){
        if($item->galleryIndex > 0) return $item->imageIndex;
        if($item->imageIndex < $this->galleryRepo->bigIndex) return $item->imageIndex;
        return "zZ" . "_" . $item->path;
    }

    public function isOpenImageEnabled(): bool{
        return true;
    }

    public function isSecondarySortAscending() : bool {
        return true;
    }

    public function isRelegateAndPromoteEnabled(): bool{
        return false;
    }

    public function getPageTitle(): string {
        return "Manage Gallery";
    }

    public function getPermanentCategoriesCount(): int | null{
        return 1 + count($this->galleryRepo->virtualFolders);
    }

    public function getListTitles(): array {
        return [];
    }

    public function getCategories(): array {
        $categories = [new ListerCategory(0, "Main")];
        foreach ($this->galleryRepo->virtualFolders as $vf){
            $categories[] = new ListerCategory($vf['index'], $vf['id']);
        }

        $perm = $this->getPermanentCategoriesCount();
        for($i=0; 50>$i; $i++) {
            $categories[] = new ListerCategory($perm + $i, "SubGallery " . ($i + 1));
        }
        return $categories;
    }

    public function getDirectMenuCategoriesIndexList(): array {
        $perm = $this->getPermanentCategoriesCount();
        $indexList = [];
        for($i=0; $perm>$i; $i++){
            $indexList[] = $i;
        }
        return $indexList;
    }

    public function catchNewList(){
        parent::catchNewList();
        if(isset($_POST['submit_type']) && $_POST['submit_type'] == "reset"){
            $this->handleSubmittedList([], [], $_POST);
        }
    }

    public function handleSubmittedList(array $lists, array $itemsParams, $allFields){
        $submitType = $allFields['submit_type'];
        if($submitType == "reset"){
            $this->galleryRepo->resetConfigs();
        }
        else {
            $galleriesCount = $allFields['galleries_count'];
            $virtualFoldersDataRaw = $allFields['virtual_folders'];
            $virtualFoldersMap = [];
            $virtualGalleries = [];
            $galleriesItems = [];
            if ($virtualFoldersDataRaw) {
                $pieces = explode(",", $virtualFoldersDataRaw);
                foreach ($pieces as $piece) {
                    $galDataPieces = explode(":", $piece);
                    $galIndex = $galDataPieces[0];
                    $galId = $galDataPieces[1];
                    $virtualFoldersMap[$galIndex] = $galId;

                    $virtualGalleries[$galId] = [];
                    $virtualGalleries[$galId]['id'] = $galId;
                    $virtualGalleries[$galId]['index'] = $galIndex;
                }
            }

            for ($i = 0; $galleriesCount > $i; $i++) {
                $galId = $i > 0 ? $virtualFoldersMap[$i] : "";
                foreach ($lists[$i] as $imageIndex => $image) {
                    $galleriesItems[$image] = [
                        "gid" => $galId,
                        "ind" => $imageIndex
                    ];
                }
            }

            $this->galleryRepo->storeConfigs($virtualGalleries, $galleriesItems);
            $this->galleryRepo->loadRecords();

            if($submitType == "rename"){
                $this->galleryRepo->renameRepo();
            }
            else if($submitType == "finish"){
                $this->galleryRepo->arrangeRepo();
            }
        }

        $this->loadRawRecords($this->galleryRepo->allRecords);
    }
}