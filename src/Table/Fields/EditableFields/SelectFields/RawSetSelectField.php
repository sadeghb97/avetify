<?php
namespace Avetify\Table\Fields\EditableFields\SelectFields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Selectors\RawSetSelector;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

class RawSetSelectField extends BaseSetSelectField {
    public RawSetSelector $rawSetSelector;
    public int $selectorWidth = 0;
    public string $placeholder = "";

    public function presentValue($item, ?WebModifier $webModifier = null) {
        NiceDiv::justOpen($webModifier);
        $value = $this->getValue($item);
        $this->rawSetSelector = new RawSetSelector($this->title, $this->getElementIdentifier($item), $value);
        $this->rawSetSelector->useNameIdentifier = $this->useNameIdentifier;
        $this->rawSetSelector->isRtl = $this->rtl;
        $this->rawSetSelector->placeholder = $this->placeholder;
        $selectorModifier = WebModifier::createInstance();
        if($this->selectorWidth > 0){
            $selectorModifier->styler->pushStyle("width", $this->selectorWidth . "px");
        }
        $this->rawSetSelector->place($selectorModifier);
        HTMLInterface::closeDiv();
    }

    public function loadValueUsingJSStorage(string $key): void {
        $this->rawSetSelector->loadValueUsingJSStorage($key);
    }

    public function setSelectorWidth(int $width) : self {
        $this->selectorWidth = $width;
        return $this;
    }

    public function setPlaceholder(string $placeholder) : self {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function preLoad() {}
}
