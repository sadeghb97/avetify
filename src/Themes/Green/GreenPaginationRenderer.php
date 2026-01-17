<?php
namespace Avetify\Themes\Green;

use Avetify\Interface\WebModifier;
use Avetify\Themes\Classic\ClassicLabel;
use Avetify\Themes\Main\Pagination\PaginationRenderer;

class GreenPaginationRenderer extends PaginationRenderer {
    public string $pageBG = "#c2c3c4";
    public string $pageColor = "Black";
    public string $activePageBG = "Cyan";
    public string $activePageColor = "Black";
    public string $navBG = "Gray";
    public string $navColor = "#252525";

    public function placePageItem(int $targetPage, WebModifier | null $webModifier = null) : void {
        $curPage = $this->configs->getCurrentPage();
        $isActive = $targetPage == $curPage;
        $finalBg = $isActive ? $this->activePageBG : $this->pageBG;
        $finalColor = $isActive ? $this->activePageColor : $this->pageColor;

        $label = new ClassicLabel($targetPage, $this->getTargetPageLink($targetPage), $finalBg, $finalColor);
        $label->place();
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
}
