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
        if($item != null) {
            $id .= "_";
            $id .= $this->idGetter->getID($item);
        }
        return $id;
    }

    function setFieldIdentifiers($item){
        if($this->idGetter != null) {
            $fieldIdentifier = $this->getEditableFieldIdentifier($item);
            if ($this->useIDIdentifier) HTMLInterface::addAttribute("id", $fieldIdentifier);
            if ($this->useNameIdentifier) HTMLInterface::addAttribute("name", $fieldIdentifier);
        }
    }

    public function presentValue($item){
        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        if($item != null) {
            HTMLInterface::addAttribute("value", $this->getValue($item));
        }
        HTMLInterface::addAttribute("placeholder", $this->title);
        $this->setFieldIdentifiers($item);
        Styler::startAttribute();
        Styler::addStyle("height", "35px");
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }

    public function onlyNameIdentifier(){
        $this->useNameIdentifier = true;
        $this->useIDIdentifier = false;
    }

    public function onlyIDIdentifier(){
        $this->useIDIdentifier = true;
        $this->useNameIdentifier = false;

    }

    public function bothIdentifier(){
        $this->useIDIdentifier = true;
        $this->useIDIdentifier = true;
    }
}

class CheckboxField extends SBEditableField {
    public bool $isNumeric = true;
    public function presentValue($item){
        echo '<input ';
        HTMLInterface::addAttribute("type", "checkbox");
        if($item != null) {
            $checked = !!$this->getValue($item);
            if($checked) HTMLInterface::addAttribute("checked");
        }
        $this->setFieldIdentifiers($item);
        Styler::startAttribute();
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }
}

class RecordSelectorField extends CheckboxField {
    public function __construct(string $title, IDGetter $idGetter){
        parent::__construct($title, "select_Record", $idGetter);
    }

    public function getValue($item): string {
        return false;
    }
}
