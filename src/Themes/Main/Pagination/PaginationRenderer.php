<?php
namespace Avetify\Themes\Main\Pagination;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Entities\Models\PaginationConfigs;
use Avetify\Interface\WebModifier;
use Avetify\Network\URLBuilder;
use Avetify\Themes\Classic\ClassicLabel;

class PaginationRenderer {
    public string $pageBG = "";
    public string $pageColor = "";
    public string $activePageBG = "";
    public string $activePageColor = "";
    public string $navBG = "";
    public string $navColor = "";

    public function __construct(public PaginationConfigs | null $configs, public int $labelsCount = 7) {}

    public function getTargetPageLink(int $targetPage) : string {
        $urlBuilder = URLBuilder::fromCurrent();
        $urlBuilder->addParam($this->configs->getPageKey(), $targetPage);
        return $urlBuilder->buildUrl();
    }

    public function place(WebModifier | null $webModifier = null) : void {
        if(!$this->configs) return;
        $curPage = $this->configs->getCurrentPage();
        $lastPage = $this->configs->getLatestPage();
        $finalLabelsCount = (($this->labelsCount % 2) == 0) ? $this->labelsCount + 1 : $this->labelsCount;
        $leftLabelsCount = intval($finalLabelsCount / 2);
        $rightLabelsCount = $leftLabelsCount;

        $this->openBox($webModifier);
        $this->placeFirstPageItem();
        $this->placePreviousPageItem();

        if(($curPage - $leftLabelsCount) > 1) $this->placePagesGap();
        if(($curPage - $leftLabelsCount) <= 0) $rightLabelsCount += ($leftLabelsCount - $curPage + 1);

        $startIndex = $curPage - $leftLabelsCount;
        if($startIndex < 1) $startIndex = 1;
        $p = $startIndex;
        for (; $lastPage >= $p && ($p - $curPage) <= $rightLabelsCount; $p++) $this->placePageItem($p);

        if($p < $lastPage) $this->placePagesGap();

        $this->placeNextPageItem();
        $this->placeLastPageItem();
        $this->closeBox();
    }

    public function placePageItem(int $targetPage, WebModifier | null $webModifier = null) : void {
        $curPage = $this->configs->getCurrentPage();
        $isActive = $targetPage == $curPage;
        $finalBg = $isActive ? $this->activePageBG : $this->pageBG;
        $finalColor = $isActive ? $this->activePageColor : $this->pageColor;

        $label = new ClassicLabel($targetPage, $this->getTargetPageLink($targetPage), $finalBg, $finalColor);
        $label->place();
    }

    public function placePagesGap(WebModifier | null $webModifier = null) : void {
        echo ' ... ';
    }

    public function placeFirstPageItem(WebModifier | null $webModifier = null) : void {
        $targetPage = 1;
        $label = new ClassicLabel("<<", $this->getTargetPageLink($targetPage), $this->navBG, $this->navColor);
        $label->place();
    }

    public function placePreviousPageItem(WebModifier | null $webModifier = null) : void {
        $targetPage = $this->configs->getCurrentPage() - 1;
        if($targetPage < 1) $targetPage = 1;
        $label = new ClassicLabel("<", $this->getTargetPageLink($targetPage), $this->navBG, $this->navColor);
        $label->place();
    }

    public function placeNextPageItem(WebModifier | null $webModifier = null) : void {
        $targetPage = $this->configs->getCurrentPage() + 1;
        $lastPage = $this->configs->getLatestPage();
        if($targetPage > $lastPage) $targetPage = $lastPage;

        $label = new ClassicLabel(">", $this->getTargetPageLink($targetPage), $this->navBG, $this->navColor);
        $label->place();
    }

    public function placeLastPageItem(WebModifier | null $webModifier = null) : void {
        $targetPage = $this->configs->getLatestPage();
        $label = new ClassicLabel(">>", $this->getTargetPageLink($targetPage), $this->navBG, $this->navColor);
        $label->place();
    }

    public function openBox (WebModifier | null $webModifier = null) : void {
        $placeOnBottom = $this->configs->paginationOnBottom;
        $niceDiv = new NiceDiv(0);
        $boxModifier = WebModifier::createInstance();
        $boxModifier->pushStyle("margin-top", $placeOnBottom ? "8px" : "16px");
        $boxModifier->pushStyle("margin-bottom",$placeOnBottom ? "16px" : "4px");
        $niceDiv->open($boxModifier);
    }

    public function closeBox (WebModifier | null $webModifier = null) : void {
        echo '</div>';
    }
}