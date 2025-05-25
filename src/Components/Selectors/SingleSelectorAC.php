<?php
namespace Avetify\Components\Selectors;

use Avetify\Fields\JSDatalist;
use Avetify\Fields\JSTextFields\JSACTextField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class SingleSelectorAC extends JSACTextField {
    public bool $disableSubmitOnEnter = true;

    public function presentListField(?WebModifier $webModifier = null){
        echo '<input ';
        Styler::classStartAttribute();
        Styler::addClass("visual-select-box__input");
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("list", $this->listIdentifier);
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
        HTMLInterface::addAttribute("autocomplete", "off");
        if($this->label) HTMLInterface::addAttribute("placeholder", $this->label);
        $this->boundEnterEvent();
        HTMLInterface::applyModifiers($webModifier);
        HTMLInterface::closeSingleTag();
    }

    public function __construct(string $fieldKey, string $childKey, string $initValue,
                                JSDatalist $dlInfo, public SingleSelector $selector) {
        parent::__construct($fieldKey, $childKey, $initValue, $dlInfo);
        $this->enterCallbackName = "updateSingleSelector";
    }

    public function callbackMoreData(): array {
        return $this->selector->selectorMoreData();
    }
}
