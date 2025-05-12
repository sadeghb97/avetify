<?php

class GreenGalleryRenderer extends GreenListerRenderer {
    public ?ManageGalleryLister $galleryLister = null;
    public ?GalleryRepo $galRepo = null;

    public function postConstruct() {
        /** @var ManageGalleryLister $mgl */
        $mgl = $this->setModifier;
        $this->galleryLister = $mgl;
        $this->galRepo = $this->galleryLister->galleryRepo;

        parent::postConstruct();
        $this->focusMode = true;
        $this->galleryMode = false;
        $this->noCacheMode = true;
        $this->cardImageWidth = 300;
    }

    public function formMoreFields(){
        $gcIdentifier = "galleries_count";
        $vfIdentifier = "virtual_folders";
        $stIdentifier = "submit_type";
        $gcValue = $this->lister->getPermanentCategoriesCount();

        $vfValue = "";
        foreach ($this->galRepo->virtualFolders as $vf){
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

        if($this->galRepo->parentRelativePath){
            $prevRepo = $this->galRepo->parentRelativePath;
            $prevLink = Routing::addParamToCurrentLink("gp", $prevRepo);

            $prevButton = new AbsoluteButton(AssetsManager::getImage("arrow_left.svg"),
                ["top" => "20px", "left" => "20px"],
                "redir('" . $prevLink . "');");
            $prevButton->place();

            $prevCloneButton = new AbsoluteButton(AssetsManager::getImage("tab_duplicate.svg"),
                ["top" => "70px", "left" => "20px"],
                "openTab('" . $prevLink . "');");
            $prevCloneButton->place();
        }

        $toggleButton = new AbsoluteButton(AssetsManager::getImage("view_alt.svg"),
            ["top" => "20px", "right" => "20px"], $this->jsToggleGalleryMode());
        $toggleButton->place();

        if(!$this->galRepo->readOnly) {
            $addButton = new AbsoluteButton(AssetsManager::getImage("add_box.svg"),
                ["bottom" => "20px", "left" => "20px"], "addVirtualGallery()");
            $addButton->place();

            $updateButton = new AbsoluteButton(AssetsManager::getImage("commit.svg"),
                ["bottom" => "20px", "left" => "90px"], "updateGalleryConfigs(jsArgs)");
            $updateButton->place();

            $submitButton = new AbsoluteButton(AssetsManager::getImage("send.svg"),
                ["bottom" => "20px", "right" => "20px"], "submitGalleries(jsArgs)");
            $submitButton->place();

            $submitButton = new AbsoluteButton(AssetsManager::getImage("pen.svg"),
                ["bottom" => "20px", "right" => "90px"], "renameGalleries(jsArgs)");
            $submitButton->place();

            $resetButton = new AbsoluteButton(AssetsManager::getImage("layers_clear.svg"),
                ["bottom" => "20px", "right" => "160px"], "resetGalleryConfigs()");
            $resetButton->place();
        }
    }

    public function placeSubRepos(){
        echo '<div class="magham-box">';
        echo '<span class="magham-degree">' . "Sub Repos" . '</span>';
        echo '</div>';

        echo '<div class="row focus-force" ';
        Styler::startAttribute();
        Styler::addStyle("overflow", "auto");
        Styler::addStyle("position", "relative");
        Styler::addStyle("justify-content", "center");
        Styler::closeAttribute();
        echo ' >';

        foreach ($this->galRepo->subRepos as $srIndex => $subRepo){
            $this->printSubRepo($subRepo, $srIndex + 1);
        }

        echo '</div>';
        echo '<hr />';
        echo '</div>';
    }

    public function printSubRepo(GalleryRepo $subRepo, $itemRank){
        echo '<div class="grid-square" ';
        Styler::startAttribute();
        $this->appendCardStyles();
        Styler::closeAttribute();
        echo '>';

        $avatar = $subRepo->cover ?
            Routing::srpToBrp($subRepo->path . $subRepo->cover->path) : "";
        echo '<img src="' . $avatar . '" class="lister-item-img" ';
        Styler::startAttribute();
        $this->appendImageWidthStyles();
        Styler::closeAttribute();
        echo '/>';

        HTMLInterface::placeVerticalDivider(12);

        $newPath = str_replace("/", "~", $subRepo->relativePath);
        $link = Routing::addParamToCurrentLink("gp", $newPath);

        if($link) {
            echo '<a ';
            HTMLInterface::addAttribute("href", $link);
            HTMLInterface::addAttribute("class", "lister-item-link");
            HTMLInterface::closeTag();
        }

        echo '<div>';
        $rankModifier = WebModifier::createInstance();
        $rankModifier->styler->pushStyle("font-size", "0.8rem");
        $rankModifier->styler->pushStyle("font-weight", "bold");

        if($this->galleryLister->isPrintRankEnabled()){
            HTMLInterface::placeSpan($itemRank, $rankModifier);
            HTMLInterface::placeSpan(": ", $rankModifier);
        }

        $title = $this->galleryLister->getItemTitle($subRepo);
        echo '<span class="lister-item-name">' . $title . '</span>';

        if(count($subRepo->allRecords) > 0){
            $subRepoImagesCount = count($subRepo->allRecords);
            HTMLInterface::placeSpan(" (" . $subRepoImagesCount . ")", $rankModifier);
        }

        echo '</div>';
        if($link) HTMLInterface::closeLink();

        echo '</div>';
    }

    function jsToggleGalleryMode(){
        $out = "const container = document.getElementById('" . $this->containerId . "');";
        $out .= ("if (container.classList.contains('focus')) {");
        $out .= ("container.classList.remove('focus');");
        $out .= ("container.classList.add('gallery');");
        $out .= ("} else {");
        $out .= ("container.classList.remove('gallery');");
        $out .= ("container.classList.add('focus');");
        $out .= ("}");
        return $out;
    }
}
