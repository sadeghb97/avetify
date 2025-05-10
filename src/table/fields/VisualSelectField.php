<?php

class VisualSelectField extends SBEditableField {
    public int $selectorWidth = 0;
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
        if($this->selectorWidth > 0){
            $selectorModifier->styler->pushStyle("width", $this->selectorWidth . "px");
        }
        $selector->disableAutoSubmit = $this->disableAutoSubmit;
        $selector->place($selectorModifier);
        HTMLInterface::closeDiv();
    }

    public function setSelectorWidth(int $width) : VisualSelectField {
        $this->selectorWidth = $width;
        return $this;
    }

    public function enableAutoSubmit() : VisualSelectField {
        $this->disableAutoSubmit = false;
        return $this;
    }

    public function preLoad() {}
}
