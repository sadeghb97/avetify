<?php
namespace Avetify\Table\Fields\EditableFields\SelectFields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Selectors\SingleSelector;
use Avetify\Fields\JSDatalist;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\EditableFields\EditableField;

class VisualSelectField extends EditableField {
    public int $maxSelectorWidth = 0;
    public bool $disableAutoSubmit = true;

    public function __construct(string $title, string $key, public JSDatalist $datalist) {
        parent::__construct($title, $key);
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        NiceDiv::justOpen($webModifier);
        $value = $this->getValue($item);

        $selector = new SingleSelector($this->title,
            $this->getElementIdentifier($item), $value, $this->datalist);
        $selector->useNameIdentifier = $this->useNameIdentifier;
        $selectorModifier = WebModifier::createInstance();
        if($this->maxSelectorWidth > 0){
            $selectorModifier->styler->pushStyle("max-width", $this->maxSelectorWidth . "px");
        }
        $selector->disableAutoSubmit = $this->disableAutoSubmit;
        $selector->place($selectorModifier);
        HTMLInterface::closeDiv();
    }

    public function setMaxSelectorWidth(int $width) : VisualSelectField {
        $this->maxSelectorWidth = $width;
        return $this;
    }

    public function enableAutoSubmit() : VisualSelectField {
        $this->disableAutoSubmit = false;
        return $this;
    }

    public function preLoad() {}
}
