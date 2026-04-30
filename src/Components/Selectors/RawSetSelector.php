<?php
namespace Avetify\Components\Selectors;

use Avetify\Forms\FormUtils;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

class RawSetSelector implements Placeable, IdentifiedElement {
    use IdentifiedElementTrait;
    public bool $isRtl = false;
    public string $placeholder = "";

    public function __construct(public string $label, public string $key, public string $initValue){
        $this->initValue = trim($this->initValue);
    }

    public function place(WebModifier $webModifier = null) {
        echo '<div ';
        HTMLInterface::addAttribute("id", $this->getElementBoxId());
        HTMLInterface::applyModifiers($webModifier);
        Styler::classStartAttribute();
        Styler::addClass("raw-selector-wrapper");
        if(!$this->isRtl) Styler::addClass("raw-selector-ltr");
        else Styler::addClass("raw-selector-rtl");
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        echo '<input ';
        HTMLInterface::addAttribute("id", $this->getElementInputId());
        $finalPlaceHolder = $this->placeholder ?: (!$this->isRtl ? "Type and press Enter" : "بنویس و اینتر بزن");
        HTMLInterface::addAttribute("placeholder", $finalPlaceHolder);
        Styler::classStartAttribute();
        Styler::addClass("raw-selector-input");
        Styler::closeAttribute();
        echo '/>';

        FormUtils::placeHiddenField($this->getElementIdentifier(), $this->initValue, !$this->useNameIdentifier);
        HTMLInterface::closeDiv();

        $initVarJS = "rawSelectorFieldValue_" . $this->key;
        echo '<script>';
        echo 'const ' . $initVarJS . ' = "' . $this->initValue . '";';
        echo $this->loadValueUsingJS($initVarJS);
        echo '</script>';

    }

    public function loadValueUsingJS(string $valueVarName): string {
        return "rawSelectorInit('" . $this->getElementBoxId() . "', '" . $this->getElementInputId() . "', '"
            . $this->getElementIdentifier() . "', " . $valueVarName . ");";
    }

    public function getElementIdentifier($item = null) : string {
        return $this->key;
    }

    public function getElementBoxId() : string {
        return $this->key . "_box";
    }

    public function getElementInputId() : string {
        return $this->key . "_input";
    }
}