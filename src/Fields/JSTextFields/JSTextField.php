<?php
namespace Avetify\Fields\JSTextFields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

abstract class JSTextField extends JSInputField implements Placeable, IdentifiedElement {
    use IdentifiedElementTrait;

    public string $listIdentifier = "";
    public bool $disableSubmitOnEnter = true;

    public function __construct(public string $fieldKey, public string $childKey,
                                public string $initValue){
    }

    public function getElementIdentifier($item = null) : string {
        if($this->childKey) return $this->fieldKey . "_" . $this->childKey;
        return $this->fieldKey;
    }

    public function place(?WebModifier $webModifier = null){
        $div = new NiceDiv(0);
        $div->baseOpen();
        HTMLInterface::closeTag();

        echo '<input ';
        $this->placeElementIdAttributes();
        HTMLInterface::addAttribute("type", "text");
        if($this->label) HTMLInterface::addAttribute("placeholder", $this->label);
        $this->boundEnterEvent();
        HTMLInterface::applyModifiers($webModifier);
        Styler::classStartAttribute();
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();

        $div->close();
    }

    abstract public function applyText() : string;

    public function boundEnterEvent(){
        HTMLInterface::addAttribute("onkeydown",
            "if (event.key === 'Enter') {" .
            (!$this->disableSubmitOnEnter ? "if(event.target.value) " : "") . "event.preventDefault();" .
            $this->applyText() .
            "}");
    }

    public function presentListField(?WebModifier $webModifier = null){
        echo '<input ';
        HTMLInterface::addAttribute("list", $this->listIdentifier);
        $this->placeElementIdAttributes();
        HTMLInterface::addAttribute("autocomplete", "off");
        if($this->label) HTMLInterface::addAttribute("placeholder", $this->label);
        $this->boundEnterEvent();
        HTMLInterface::applyModifiers($webModifier);
        Styler::classStartAttribute();
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }
}
