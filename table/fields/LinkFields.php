<?php

abstract class SBTableLinkField extends SBTableField {
    public null | string $color = "Black";
    public bool $isBlank = false;

    public function __construct(string $title, string $key){
        parent::__construct($title, $key);
    }

    abstract function getLinkValue($item) : string;

    public function presentValue($item){
        $title = $this->getValue($item);
        $link = $this->getLinkValue($item);
        echo '<a href="' . $link . '" style="';
        if($this->color != null) Styler::addStyle("color", $this->color);
        echo '" ';
        if($this->isBlank) HTMLInterface::addAttribute("target", "_blank");
        HTMLInterface::closeTag();
        echo $title;
        echo '</a>';
    }
}

class SBTableSimpleLinkField extends SBTableLinkField {
    public function __construct(string $title, string $key, public string $linkKey){
        parent::__construct($title, $key);
    }

    public function getValue($item): string {
        return SBTableSimpleField::getValueByKey($item, $this->key);
    }

    function getLinkValue($item): string {
        return SBTableSimpleField::getValueByKey($item, $this->linkKey);
    }
}

