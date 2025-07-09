<?php
namespace Avetify\GalRepo;

use Avetify\AvetifyManager;
use Avetify\Components\Buttons\AbsoluteButton;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Routing\Routing;
use Avetify\Themes\Green\GreenListerRenderer;

class GreenGalleryRenderer extends GreenListerRenderer {
    public ?ManageGalleryLister $galleryLister = null;
    public ?GalleryRepo $galRepo = null;
    public int $topButtonsMargin = 0;

    public function postConstruct() {
        parent::postConstruct();

        /** @var ManageGalleryLister $mgl */
        $mgl = $this->setModifier;
        $this->galleryLister = $mgl;
        $this->galRepo = $this->galleryLister->galleryRepo;

        $this->focusMode = true;
        $this->galleryMode = false;
        $this->noCacheMode = true;
        $this->mainLinksBlank = true;
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

            $prevButton = new AbsoluteButton(AvetifyManager::imageUrl("arrow_left.svg"),
                ["top" => ($this->topButtonsMargin + 20) . "px", "left" => "20px"],
                "redir('" . $prevLink . "');");
            $prevButton->place();

            $prevCloneButton = new AbsoluteButton(AvetifyManager::imageUrl("tab_duplicate.svg"),
                ["top" => ($this->topButtonsMargin + 70) . "px", "left" => "20px"],
                "openTab('" . $prevLink . "');");
            $prevCloneButton->place();
        }

        $toggleButton = new AbsoluteButton(AvetifyManager::imageUrl("view_alt.svg"),
            ["top" => ($this->topButtonsMargin + 20) . "px", "right" => "20px"], $this->jsToggleGalleryMode());
        $toggleButton->place();

        if(!$this->galRepo->readOnly) {
            $addButton = new AbsoluteButton(AvetifyManager::imageUrl("add_box.svg"),
                ["bottom" => "20px", "left" => "20px"], "addVirtualGallery()");
            $addButton->place();

            $updateButton = new AbsoluteButton(AvetifyManager::imageUrl("commit.svg"),
                ["bottom" => "20px", "left" => "90px"], "updateGalleryConfigs(jsArgs)");
            $updateButton->place();

            $submitButton = new AbsoluteButton(AvetifyManager::imageUrl("send.svg"),
                ["bottom" => "20px", "right" => "20px"], "submitGalleries(jsArgs)");
            $submitButton->place();

            $submitButton = new AbsoluteButton(AvetifyManager::imageUrl("pen.svg"),
                ["bottom" => "20px", "right" => "90px"], "renameGalleries(jsArgs)");
            $submitButton->place();

            $resetButton = new AbsoluteButton(AvetifyManager::imageUrl("layers_clear.svg"),
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
        $this->appendRecordCardStyles();

        Styler::closeAttribute();
        echo '>';

        $avatar = $subRepo->cover ?? "";
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

    function jsToggleGalleryMode() : string {
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
