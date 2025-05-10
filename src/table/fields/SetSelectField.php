<?php

class SetSelectField extends SBEditableField {
    public int $selectorWidth = 0;
    public bool $tinyAvatars = false;
    public bool $disableAutoSubmit = false;

    public function __construct(string $title, string $key, public JSDatalist $datalist) {
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        NiceDiv::justOpen();
        $value = $this->getValue($item);
        $setSelector = new SetSelector($this->title,
            $this->getEditableFieldIdentifier($item), $value, $this->datalist);
        $selectorModifier = WebModifier::createInstance();
        if($this->selectorWidth > 0){
            $selectorModifier->styler->pushStyle("width", $this->selectorWidth . "px");
        }
        $setSelector->tinyAvatars = $this->tinyAvatars;
        $setSelector->disableAutoSubmit = $this->disableAutoSubmit;
        $setSelector->place($selectorModifier);
        HTMLInterface::closeDiv();
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
}
