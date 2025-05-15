<?php

class SingleSelector implements Placeable {
    public bool $useNameIdentifier = false;
    public bool $disableAutoSubmit = false;

    public function __construct(public string $label,
                                public string $key,
                                public string $initValue,
                                public JSDatalist $dlInfo
    ){
        $this->initValue = trim($this->initValue);
    }

    public function place(WebModifier $webModifier = null) {
        $record = ($this->initValue) ? $this->dlInfo->getRecordById($this->initValue) : null;
        $avatar = $record ? $this->dlInfo->getItemImage($record) : "";

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("visual-select-box");
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();

        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();

        HTMLInterface::applyModifiers($webModifier);
        HTMLInterface::closeTag();

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("visual-select-box__image-wrapper");
        Styler::closeAttribute();
        Styler::startAttribute();
        if(!$avatar) Styler::addStyle("display", "none");
        Styler::closeAttribute();
        HTMLInterface::addAttribute("id", $this->getImageBoxId());
        HTMLInterface::closeTag();
        echo '<img ';
        HTMLInterface::addAttribute("id", $this->getImageId());
        HTMLInterface::addAttribute("src", $avatar);
        HTMLInterface::addAttribute("alt", "Avatar");
        Styler::startAttribute();
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
        HTMLInterface::closeDiv();

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("visual-select-box__input-wrapper");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        $selectorAc = new SingleSelectorAC($this->key, $this->key, "", $this->dlInfo, $this);
        $selectorAc->label = $this->label;
        $selectorAc->place();
        HTMLInterface::closeDiv();

        FormUtils::placeHiddenField($this->getMainElementId(), $this->initValue, !$this->useNameIdentifier);
        HTMLInterface::closeDiv();
    }

    public function selectorMoreData() : array {
        return [
            "disable_auto_submit" => $this->disableAutoSubmit
        ];
    }

    public function getMainElementId() : string {
        return $this->key;
    }

    public function getImageBoxId() : string {
        return $this->key . "_avatar_box";
    }

    public function getImageId() : string {
        return $this->key . "_avatar";
    }
}

class SingleSelectorAC extends JSACTextField {
    public bool $disableSubmitOnEnter = true;

    public function presentListField(?WebModifier $webModifier = null){
        echo '<input ';
        Styler::classStartAttribute();
        Styler::addClass("visual-select-box__input");
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("list", $this->listIdentifier);
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
        HTMLInterface::addAttribute("autocomplete", "off");
        if($this->label) HTMLInterface::addAttribute("placeholder", $this->label);
        $this->boundEnterEvent();
        HTMLInterface::applyModifiers($webModifier);
        HTMLInterface::closeSingleTag();
    }

    public function __construct(string $fieldKey, string $childKey, string $initValue,
                                JSDatalist $dlInfo, public SingleSelector $selector) {
        parent::__construct($fieldKey, $childKey, $initValue, $dlInfo);
        $this->enterCallbackName = "updateSingleSelector";
    }

    public function callbackMoreData(): array {
        return $this->selector->selectorMoreData();
    }
}