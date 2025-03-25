<?php

class IconButton implements Placeable {
    public function __construct(public string $src, public int $size, public string $onClickRaw = ""){}

    public function place(WebModifier $webModifier = null){
        if($webModifier == null) $webModifier = ImageModifiers::imageSquareModifier($this->size);
        if($webModifier->styler == null) $webModifier->styler = new Styler();
        $webModifier->styler->pushStyle("cursor", "pointer");
        if($this->onClickRaw) {
            if ($webModifier->htmlModifier == null) $webModifier->htmlModifier = new HTMLModifier();
            $webModifier->htmlModifier->pushModifier("onclick", $this->onClickRaw);
        }

        HTMLInterface::placeImage($this->src, $webModifier);
    }
}
