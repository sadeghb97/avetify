<?php
namespace Avetify\Table\Fields\EditableFields\SelectFields;

use Avetify\Fields\JSDataSet;
use Avetify\Fields\JSDynamicSelect;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\EditableFields\EditableField;

abstract class SelectField extends EditableField {
    public function presentValue($item, ?WebModifier $webModifier = null) {
        $dynamicSelect = new JSDynamicSelect(
            "",
            $this->getElementIdentifier($item),
            $this->getValue($item),
            $this->getDataSetKey()
        );
        $dynamicSelect->useNameIdentifier = $this->useNameIdentifier;
        $dynamicSelect->place($webModifier);
    }

    public function preLoad() {
        $dataSet = $this->getDataSet($this->getDataSetKey());
        $dataSet->place();
    }

    public function getDataSetKey() : string {
        return $this->key . "_" . "dataset";
    }

    public abstract function getDataSet($dataSetKey) : JSDataSet;
}
