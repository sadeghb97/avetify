<?php
namespace Avetify\Fields;

use Avetify\Components\NiceDiv;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

abstract class JSTextField extends JSInputField {
    public string $listIdentifier = "";
    public bool $disableSubmitOnEnter = true;

    public function __construct(public string $fieldKey, public string $childKey,
                                public string $initValue){
    }

    public function getFieldIdentifier() : string {
        if($this->childKey) return $this->fieldKey . "_" . $this->childKey;
        return $this->fieldKey;
    }

    public function place(?WebModifier $webModifier = null){
        $div = new NiceDiv(0);
        $div->baseOpen();
        HTMLInterface::closeTag();

        echo '<input ';
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
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
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
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

class APITextField extends JSTextField {
    public function __construct(string $fieldKey, string $childKey, string $initValue,
                                public string $apiEndpoint){
        parent::__construct($fieldKey, $childKey, $initValue);
    }

    public function applyText() : string {
        return 'apiTextEnterAction(\'' . $this->getFieldIdentifier() . '\', \'' . $this->childKey .
            '\', \'' . $this->fieldKey . '\', \'' .
            $this->apiEndpoint . '\', ' . $this->applyTextCallback() . ');';
    }

    public function applyTextCallback() : string {
        return "(data) => {" .
            "console.log('DATA', data)" .
            "}";
    }
}
