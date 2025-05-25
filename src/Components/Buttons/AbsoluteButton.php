<?php
namespace Avetify\Components\Buttons;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

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
