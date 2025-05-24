<?php
namespace Avetify\Table\Fields;

use Avetify\Components\NiceDiv;
use Avetify\Components\SingleSelector;
use Avetify\Fields\JSDatalist;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;

class VisualSelectField extends EditableField {
    public int $maxSelectorWidth = 0;
    public bool $disableAutoSubmit = true;

    public function __construct(string $title, string $key, public JSDatalist $datalist) {
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        NiceDiv::justOpen();
        $value = $this->getValue($item);

        $selector = new SingleSelector($this->title,
            $this->getEditableFieldIdentifier($item), $value, $this->datalist);
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
