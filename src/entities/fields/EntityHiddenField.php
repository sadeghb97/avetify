<?php

class EntityHiddenField extends EntityField implements EntityView {
    public function __construct($key, $title) {
        parent::__construct($key, $title);
        $this->setHidden();
    }

    public function place($record, ?WebModifier $modifier = null){
        $value = EntityUtils::getSimpleValue($record, $this->key);
        echo '<input ';
        HTMLInterface::addAttribute("type","hidden");
        HTMLInterface::addAttribute("name", $this->key);
        HTMLInterface::addAttribute("id", $this->key);
        HTMLInterface::addAttribute("value", $value ? $value : "");
        HTMLInterface::closeSingleTag();
    }
}
