<?php

abstract class SelectField extends SBEditableField {
    public function presentValue($item) {
        $dynamicSelect = new JSDynamicSelect(
            "",
            $this->getEditableFieldIdentifier($item),
            $this->getValue($item),
            $this->getDataSetKey()
        );
        $dynamicSelect->setNameIdentifier = false;
        $dynamicSelect->place();
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
