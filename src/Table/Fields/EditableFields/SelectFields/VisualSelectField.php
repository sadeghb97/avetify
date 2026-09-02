<?php
namespace Avetify\Table\Fields\EditableFields\SelectFields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Selectors\SingleSelector;
use Avetify\DB\Filters\DBFilterInterface;
use Avetify\Fields\JSDatalist;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\EditableFields\EditableField;

class VisualSelectField extends EditableField {
    public ?SingleSelector $selector = null;
    public int $maxSelectorWidth = 0;
    public bool $disableAutoSubmit = true;

    public function __construct(string $title, string $key, public JSDatalist $datalist) {
        parent::__construct($title, $key);
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        NiceDiv::justOpen($webModifier);
        $value = $this->getValue($item);

        $this->selector = new SingleSelector($this->title,
            $this->getElementIdentifier($item), $value, $this->datalist);
        $this->selector->readOnly = $this->isReadonly;
        $this->selector->useNameIdentifier = $this->useNameIdentifier;
        $selectorModifier = WebModifier::createInstance();
        if($this->maxSelectorWidth > 0){
            $selectorModifier->styler->pushStyle("max-width", $this->maxSelectorWidth . "px");
        }
        $this->selector->disableAutoSubmit = $this->disableAutoSubmit;
        $this->selector->place($selectorModifier);
        HTMLInterface::closeDiv();
    }

    public function loadValueUsingJSStorage(string $key): void {
        if($this->selector) {
            $this->selector->loadValueUsingJSStorage($key);
        }
    }

    public function setMaxSelectorWidth(int $width) : static {
        $this->maxSelectorWidth = $width;
        return $this;
    }

    public function enableAutoSubmit() : static {
        $this->disableAutoSubmit = false;
        return $this;
    }

    public function preLoad() {}

    public function dbQualifyingFilter($paramValue): DBFilterInterface | null {
        if(!$paramValue) return null;
        return parent::dbQualifyingFilter($paramValue);
    }
}
