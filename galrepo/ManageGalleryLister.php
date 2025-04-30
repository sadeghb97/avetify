<?php
class ManageGalleryLister extends SBLister {
    public function __construct(public GalleryRepo $galleryRepo){
        parent::__construct($this->galleryRepo->allRecords);
        $this->placeDefaultTriggers = false;
    }

    private function getCandidateMainValue(GalleryRecord $record): string{
        return $record->path;
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

    public function getItemImage($item): string {
        if(!$item) return "";
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
        if($item->galleryIndex > 0) return $item->imageIndex;
        return $item->path;
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
        $this->placeSubRepos();

        if($this->galleryRepo->parentRelativePath){
            $prevRepo = $this->galleryRepo->parentRelativePath;
            $prevLink = Routing::addParamToCurrentLink("gp", $prevRepo);
            $prevButton = new AbsoluteButton(Routing::getAvtImage("arrow_left.svg"),
                ["top" => "20px", "left" => "20px"],
                "redir('" . $prevLink . "');");
            $prevButton->place();
        }

        if(!$this->galleryRepo->readOnly) {
            $addButton = new AbsoluteButton(Routing::getAvtImage("add_box.svg"),
                ["bottom" => "20px", "left" => "20px"], "addVirtualGallery()");
            $addButton->place();

            $updateButton = new AbsoluteButton(Routing::getAvtImage("commit.svg"),
                ["bottom" => "20px", "left" => "90px"], "updateGalleryConfigs(jsArgs)");
            $updateButton->place();

            $submitButton = new AbsoluteButton(Routing::getAvtImage("send.svg"),
                ["bottom" => "20px", "right" => "20px"], "submitGalleries(jsArgs)");
            $submitButton->place();

            $resetButton = new AbsoluteButton(Routing::getAvtImage("layers_clear.svg"),
                ["bottom" => "20px", "right" => "90px"], "resetGalleryConfigs()");
            $resetButton->place();
        }
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

    public function placeSubRepos(){
        echo '<div class="magham-box">';
        echo '<span class="magham-degree">' . "Sub Repos" . '</span>';
        echo '</div>';

        echo '<div class="row" ';
        Styler::startAttribute();
        Styler::addStyle("overflow", "auto");
        Styler::addStyle("position", "relative");
        Styler::addStyle("justify-content", "center");
        Styler::closeAttribute();
        echo ' >';

        foreach ($this->galleryRepo->subRepos as $srIndex => $subRepo){
            $this->printSubRepo($subRepo, $srIndex + 1);
        }

        echo '</div>';
        echo '<hr />';
        echo '</div>';
    }

    public function printSubRepo(GalleryRepo $subRepo, $itemRank){
        echo '<div class="grid-square" ';
        echo '>';

        $avatar = $subRepo->cover ?
            Routing::serverPathToBrowserPath($subRepo->path . $subRepo->cover->path) : "";
        echo '<img src="' . $avatar . '" class="lister-item-img" ';
        echo '/>';

        HTMLInterface::placeVerticalDivider(12);

        echo '<div>';
        if($this->isPrintRankEnabled()){
            $rankStyler = new Styler();
            $rankStyler->pushStyle("font-size", "0.875rem");
            $rankStyler->pushStyle("font-weight", "bold");
            echo '<span ';
            $rankStyler->applyStyles();
            echo ' >';
            echo $itemRank;
            echo '</span>';
            echo '<span ';
            $rankStyler->applyStyles();
            echo '>: </span>';
        }

        $title = $this->getItemTitle($subRepo);
        $newPath = str_replace("/", "~", $subRepo->relativePath);
        $link = Routing::addParamToCurrentLink("gp", $newPath);

        if($link) {
            echo '<a ';
            HTMLInterface::addAttribute("href", $link);
            HTMLInterface::addAttribute("class", "lister-item-link");
            HTMLInterface::closeTag();
        }

        echo '<span class="lister-item-name">' . $title . '</span>';

        if($link) HTMLInterface::closeLink();

        echo '</div>';
        echo '</div>';
    }
}