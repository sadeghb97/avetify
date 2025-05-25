<?php
namespace Avetify\Forms\Buttons;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;

class AbsoluteFormButton extends FormButton {
    public function __construct(string $formIdentifier,
                                string $triggerIdentifier,
                                public array $position,
                                public string $img,
                                string $formTriggerElementId = ""){
        parent::__construct($formIdentifier, $triggerIdentifier, "", "", $formTriggerElementId);
    }

    public function renderButton(){
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("position", "fixed");
        Styler::addStyle("cursor", "pointer");
        foreach ($this->position as $posKey => $pos){
            Styler::addStyle($posKey, $pos);
        }
        Styler::closeAttribute();
        HTMLInterface::addAttribute("type", "button");
        HTMLInterface::addAttribute("id", $this->triggerIdentifier);
        HTMLInterface::closeTag();

        echo '<img ';
        HTMLInterface::addAttribute("src", $this->img);
        HTMLInterface::addAttribute("alt", "Icon");
        HTMLInterface::addAttribute("width", $this->iconSize . "px");
        HTMLInterface::addAttribute("height", $this->iconSize . "px");
        HTMLInterface::closeSingleTag();

        HTMLInterface::closeDiv();
    }
}
