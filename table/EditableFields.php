<?php

class SBEditableField extends SBTableField {
    public string | null $namespace = null;
    public bool $useIDIdentifier = true;
    public bool $useNameIdentifier = false;

    public function __construct(string $title, string $key, public IDGetter | null $idGetter = null){
        parent::__construct($title, $key);
        $this->editable = true;
    }

    public function getEditableFieldIdentifier($item) : string {
        $id = "";
        if($this->namespace != null) $id = $this->namespace . '_';
        $id .= $this->key;
        $id .= "_";
        $id .= $this->idGetter->getID($item);
        return $id;
    }

    public function presentValue($item){
        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("value", $this->getValue($item));
        HTMLInterface::addAttribute("placeholder", $this->title);
        if($this->idGetter != null) {
            $fieldIdentifier = $this->getEditableFieldIdentifier($item);
            if ($this->useIDIdentifier) HTMLInterface::addAttribute("id", $fieldIdentifier);
            if ($this->useNameIdentifier) HTMLInterface::addAttribute("name", $fieldIdentifier);
        }
        Styler::startAttribute();
        Styler::addStyle("height", "35px");
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }
}
