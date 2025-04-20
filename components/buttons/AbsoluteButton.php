<?php

class AbsoluteButton implements Placeable {
    public function __construct(public string $imageSrc,
                                public array $positionStyles,
                                public string $rawOnclick = ""){}


    public function place(WebModifier $webModifier = null) {
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("position", "fixed");
        Styler::addStyle("cursor", "pointer");
        foreach ($this->positionStyles as $psKey => $psValue){
            Styler::addStyle($psKey, $psValue);
        }
        if($webModifier && $webModifier->styler) $webModifier->styler->appendStyles();
        Styler::closeAttribute();
        HTMLInterface::addAttribute("class", "img-button");
        if($this->rawOnclick) HTMLInterface::addAttribute("onclick", $this->rawOnclick);
        if($webModifier && $webModifier->htmlModifier) $webModifier->htmlModifier->applyModifiers();
        HTMLInterface::closeTag();
        echo '<img src="' . $this->imageSrc . '" alt="Icon" style="width: 50px; height: 50px; border-radius: 50%;">';
        echo '</div>';
    }
}

class PrimaryButton extends AbsoluteButton {
    public function __construct(string $rawOnclick = "") {
        parent::__construct(Routing::browserPathFromAventador("assets/img/sync.svg"),
            ["bottom" => "20px", "left" => "20px"], $rawOnclick);
    }
}
