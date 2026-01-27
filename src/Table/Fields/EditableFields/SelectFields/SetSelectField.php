<?php
namespace Avetify\Table\Fields\EditableFields\SelectFields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Selectors\SetSelector;
use Avetify\Fields\JSDatalist;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\EditableFields\EditableField;

class SetSelectField extends EditableField {
    public SetSelector $setSelector;
    public int $selectorWidth = 0;
    public bool $tinyAvatars = false;
    public bool $disableAutoSubmit = false;

    public function __construct(string $title, string $key, public JSDatalist $datalist) {
        parent::__construct($title, $key);
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        NiceDiv::justOpen($webModifier);
        $value = $this->getValue($item);
        $this->setSelector = new SetSelector($this->title,
            $this->getElementIdentifier($item), $value, $this->datalist);
        $this->setSelector->useNameIdentifier = $this->useNameIdentifier;
        $selectorModifier = WebModifier::createInstance();
        if($this->selectorWidth > 0){
            $selectorModifier->styler->pushStyle("width", $this->selectorWidth . "px");
        }
        $this->setSelector->tinyAvatars = $this->tinyAvatars;
        $this->setSelector->disableAutoSubmit = $this->disableAutoSubmit;
        $this->setSelector->isReadonly = $this->isReadonly;
        $this->setSelector->place($selectorModifier);
        HTMLInterface::closeDiv();
    }

    public function loadValueUsingJSStorage(string $key): void {
        $this->setSelector->loadValueUsingJSStorage($key);
    }

    public function setSelectorWidth(int $width) : SetSelectField {
        $this->selectorWidth = $width;
        return $this;
    }

    public function setTinyAvatars() : SetSelectField {
        $this->tinyAvatars = true;
        return $this;
    }

    public function enableAutoSubmit() : SetSelectField {
        $this->disableAutoSubmit = false;
        return $this;
    }

    public function preLoad() {}

    public function isQualified($item, $param): bool {
        $setValue = $this->getValue($item);
        if(!$setValue) return false;

        $filterValue = $param;
        if(!$filterValue) return true;

        $existsList = explode(",", $setValue);
        $filterList = explode(",", $filterValue);

        $existsSet = [];
        foreach ($existsList as $i) $existsSet[$i] = true;

        foreach ($filterList as $filterItem){
            if(empty($existsSet[$filterItem])) return false;
        }
        return true;
    }
}
