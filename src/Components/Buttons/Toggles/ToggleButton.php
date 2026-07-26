<?php
namespace Avetify\Components\Buttons\Toggles;

use Avetify\AvetifyManager;
use Avetify\Components\Buttons\LinkAbsoluteButton;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

abstract class ToggleButton implements Placeable {
    public string $imageSrc = "";

    public function __construct(public array $positionStyles) {
        $this->imageSrc = AvetifyManager::imageUrl("view_alt.svg");
    }

    abstract public function buildNextPageUrl() : string;

    public function place(?WebModifier $webModifier = null) {
        if(!$this->isToggleActive()) return;

        $button = new LinkAbsoluteButton($this->imageSrc, $this->positionStyles, $this->buildNextPageUrl());
        $button->isBlank = false;
        $button->place($webModifier);
    }

    public function isToggleActive() : bool {
        return true;
    }
}
