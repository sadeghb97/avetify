<?php
namespace Avetify\Components\Images;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\HTMLModifier;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

class IconButton implements Placeable {
    public function __construct(public string $src, public int $size, public string $onClickRaw = ""){}

    public function place(WebModifier $webModifier = null){
        if($webModifier == null) $webModifier = WebModifier::createInstance();
        $webModifier->styler->pushStyle("cursor", "pointer");
        $webModifier->styler->pushStyle("width", $this->size . "px");
        $webModifier->styler->pushStyle("height", $this->size . "px");
        if($this->onClickRaw) {
            if ($webModifier->htmlModifier == null) $webModifier->htmlModifier = new HTMLModifier();
            $webModifier->htmlModifier->pushModifier("onclick", $this->onClickRaw);
        }

        HTMLInterface::placeImage($this->src, $webModifier);
    }
}
