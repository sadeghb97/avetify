<?php

class AbsoluteButton implements Placeable {
    public function __construct(public string $imageSrc,
                                public array $positionStyles,
                                public string $rawOnclick = ""){}


    public function place(WebModifier $webModifier = null) {
        echo '<div ';
        Styler::classStartAttribute();
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        Styler::addStyle("position", "fixed");
        Styler::addStyle("cursor", "pointer");
        foreach ($this->positionStyles as $psKey => $psValue){
            Styler::addStyle($psKey, $psValue);
        }
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::addAttribute("class", "img-button");
        if($this->rawOnclick) HTMLInterface::addAttribute("onclick", $this->rawOnclick);
        HTMLInterface::applyModifiers($webModifier);
        HTMLInterface::closeTag();
        echo '<img src="' . $this->imageSrc . '" alt="Icon" style="width: 50px; height: 50px; border-radius: 50%;">';
        echo '</div>';
    }
}

class PrimaryButton extends AbsoluteButton {
    public function __construct(string $rawOnclick = "") {
        parent::__construct(AvetifyManager::imageUrl("sync.svg"),
            ["bottom" => "20px", "inset-inline-end" => "20px"], $rawOnclick);
    }
}

class LinkAbsoluteButton extends AbsoluteButton {
    public bool $isBlank = true;

    public function __construct(string $imageSrc, array $positionStyles, public string $link) {
        parent::__construct($imageSrc, $positionStyles, "");
    }

    public function place(WebModifier $webModifier = null) {
        echo '<a ';
        HTMLInterface::addAttribute("href", $this->link);
        if($this->isBlank) HTMLInterface::addAttribute("target", "_blank");
        HTMLInterface::closeTag();
        parent::place($webModifier);
        echo '</a>';
    }
}
