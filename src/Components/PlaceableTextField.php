<?php
namespace Avetify\Components;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

class PlaceableTextField implements Placeable {
    use IdentifiedElementTrait;

    public function __construct(public string $fieldId, public string $value = ""){
        $this->useNameIdentifier = true;
    }

    public function getElementIdentifier($item = null) {
        return $this->fieldId;
    }

    public function place(WebModifier $webModifier = null) {
        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("value", $this->value);
        $this->placeElementIdAttributes();

        if($webModifier != null && $webModifier->htmlModifier != null){
            $webModifier->htmlModifier->applyModifiers();
        }

        if($webModifier != null && $webModifier->styler != null){
            $webModifier->styler->applyStyles();
        }

        HTMLInterface::closeSingleTag();
    }
}
