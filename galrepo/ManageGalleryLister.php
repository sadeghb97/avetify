<?php
class ManageGalleryLister extends SBLister {
    public function __construct(public GalleryRepo $galleryRepo){
        parent::__construct($this->galleryRepo->allRecords);
    }

    private function getCandidateMainValue(GalleryRecord $record): string{
        return $record->path;
    }

    public function getItemId($item): string {
        return $item->path;
    }

    public function getItemTitle($item): string {
        $p = $item->path;
        if(!str_contains($p, "/")) return $p;
        $pos = strrpos($p, "/");
        return substr($p, $pos + 1);
    }

    public function getItemImage($item): string {
        return Routing::serverPathToBrowserPath($this->galleryRepo->path . $item->path);
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
        return $item->imageIndex;
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

    public function getCategories(): array {
        $categories = [new SBListCategory(0, "Uncategorized")];
        foreach ($this->galleryRepo->virtualFolders as $vf){
            $categories[] = new SBListCategory($vf['index'], $vf['id']);
        }

        $perm = $this->getPermanentCategoriesCount();
        for($i=0; 50>$i; $i++) {
            $categories[] = new SBListCategory($perm + $i, "SubGallery " . ($i + 1));
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

    public function formMoreFields(){
        $gcIdentifier = "galleries_count";
        $vfIdentifier = "virtual_folders";
        $stIdentifier = "submit_type";
        $gcValue = $this->getPermanentCategoriesCount();

        $vfValue = "";
        foreach ($this->galleryRepo->virtualFolders as $vf){
            if($vfValue) $vfValue .= ",";
            $vfValue .= ($vf['index'] . ':' . $vf['id']);
        }

        echo '<input type="hidden" id="' . $gcIdentifier
            . '" name="' . $gcIdentifier . '" value="' . $gcValue . '">';

        echo '<input type="hidden" id="' . $vfIdentifier
            . '" name="' . $vfIdentifier . '" value="' . $vfValue . '">';

        echo '<input type="hidden" id="' . $stIdentifier
            . '" name="' . $stIdentifier . '" value="' . 'normal' . '">';
    }

    public function moreBodyContents(){
        $iconImage = "img/kiss.png";
        echo '<div style="position: fixed; bottom: 20px; left: 20px;" onclick="addVirtualGallery()">
            <img src="' . $iconImage . '" alt="Icon" style="width: 50px; height: 50px; border-radius: 50%;">
        </div>';

        $iconImage = "img/update.png";
        echo '<div style="position: fixed; bottom: 20px; left: 90px;" onclick="updateGalleryConfigs(jsArgs)">
            <img src="' . $iconImage . '" alt="Icon" style="width: 50px; height: 50px; border-radius: 50%;">
        </div>';

        $iconImage = "img/submit.png";
        echo '<div style="position: fixed; bottom: 20px; right: 20px;" onclick="submitGalleries(jsArgs)">
            <img src="' . $iconImage . '" alt="Icon" style="width: 50px; height: 50px; border-radius: 50%;">
        </div>';

        $iconImage = "img/reset.png";
        echo '<div style="position: fixed; bottom: 20px; right: 90px;" onclick="resetGalleryConfigs()">
            <img src="' . $iconImage . '" alt="Icon" style="width: 50px; height: 50px; border-radius: 50%;">
        </div>';
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

            for ($i = 1; $galleriesCount > $i; $i++) {
                $galId = $virtualFoldersMap[$i];
                foreach ($lists[$i] as $imageIndex => $image) {
                    $galleriesItems[$image] = [
                        "gid" => $galId,
                        "ind" => $imageIndex
                    ];
                }
            }

            $this->galleryRepo->storeConfigs($virtualGalleries, $galleriesItems);
            $this->galleryRepo->loadRecords();

            if($submitType == "finish"){
                $this->galleryRepo->arrangeRepo();
            }
        }

        $this->setItems($this->galleryRepo->allRecords);
    }
}