<?php

class EntityDisabledField extends EntityField implements EntityView {
    public function __construct($key, $title, public string $defaultValue = "") {
        parent::__construct($key, $title);
    }

    public function place($record, ?WebModifier $modifier = null){
        $value = $record ? EntityUtils::getSimpleValue($record, $this->key) : $this->defaultValue;

        $niceDiv = new NiceDiv();
        $niceDiv->open();

        $labelModifier = WebModifier::createInstance();
        HTMLInterface::placeSpan($this->title . ": ", $labelModifier);
        $niceDiv->separate();

        $valueModifier = WebModifier::createInstance();
        HTMLInterface::placeSpan($value, $valueModifier);

        $niceDiv->close();

        FormUtils::placeHiddenField($this->key, $value);
    }
}
