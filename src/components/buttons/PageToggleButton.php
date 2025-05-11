<?php

class PageToggleButton implements Placeable {
    public string $nextPage = "";

    public function __construct(public array $pages, public array $positionStyles) {
        $curScript = Routing::currentScriptName();
        $key = array_search($curScript, $this->pages);

        if($key === false){
            $this->nextPage = $this->pages[0];
        }
        else {
            $nextIndex = $key + 1;
            if($nextIndex >= count($this->pages)) $nextIndex = 0;
            $this->nextPage = $this->pages[$nextIndex];
        }
    }

    public function buildNextPageUrl() : string {
        $urlBuilder = URLBuilder::fromCurrent();
        return $urlBuilder->buildUrl($this->nextPage);
    }

    public function place(WebModifier $webModifier = null) {
        $button = new LinkAbsoluteButton(AssetsManager::getImage("view_alt.svg"),
            $this->positionStyles, $this->buildNextPageUrl());
        $button->isBlank = false;
        $button->place($webModifier);
    }
}
