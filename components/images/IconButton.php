<?php

class IconButton extends PlaceableImage {
    public function __construct(string $src, int $size, public string $onClickRaw = ""){
        parent::__construct($src, $size, true);
    }

    public function place(WebModifier $webModifier = null){
        if($webModifier == null) $webModifier = new WebModifier();
        if($webModifier->styler == null) $webModifier->styler = new Styler();
        $webModifier->styler->pushStyle("cursor", "pointer");
        if($this->onClickRaw) {
            if ($webModifier->htmlModifier == null) $webModifier->htmlModifier = new HTMLModifier();
            $webModifier->htmlModifier->pushModifier("onclick", $this->onClickRaw);
        }
        parent::place($webModifier);
    }
}
