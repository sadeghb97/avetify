<?php
namespace Avetify\Components;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

class PlaceableTextField implements Placeable {
    public function __construct(public string $fieldId, public string $value = ""){
    }

    public function place(WebModifier $webModifier = null) {
        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("value", $this->value);
        HTMLInterface::addAttribute("id", $this->fieldId);
        HTMLInterface::addAttribute("name", $this->fieldId);

        if($webModifier != null && $webModifier->htmlModifier != null){
            $webModifier->htmlModifier->applyModifiers();
        }

        if($webModifier != null && $webModifier->styler != null){
            $webModifier->styler->applyStyles();
        }

        HTMLInterface::closeSingleTag();
    }
}
