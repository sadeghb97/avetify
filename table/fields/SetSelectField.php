<?php

class SetSelectField extends SBEditableField {
    public function __construct(string $title, string $key, public JSDatalist $datalist) {
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        NiceDiv::justOpen();
        $value = $this->getValue($item);
        $setSelector = new SetSelector($this->title,
            $this->getEditableFieldIdentifier($item), $value, $this->datalist);
        $setSelector->place();
        HTMLInterface::closeDiv();
    }

    public function preLoad() {
        $this->datalist->place();
    }
}
