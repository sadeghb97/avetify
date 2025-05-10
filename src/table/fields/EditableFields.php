<?php

class SBEditableField extends SBTableField {
    public bool $useIDIdentifier = true;
    public bool $useNameIdentifier = false;

    public function __construct(string $title, string $key,
                                public EntityID | null $idGetter = null,
                                public string | null $namespace = null){
        parent::__construct($title, $key);
        $this->editable = true;
    }

    public function getEditableFieldIdentifier($item) : string {
        $id = "";
        if($this->namespace != null) $id = $this->namespace . '_';
        $id .= $this->key;
        if($item != null) {
            $id .= "_";
            $id .= $this->idGetter->getItemId($item);
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

    function appendMainAttributes($item){
        $this->setFieldIdentifiers($item);
    }

    function appendMainStyles($item){
        Styler::addStyle("font-family", "inherit");
    }

    public function presentValue($item){
        $value = $this->getValue($item);
        $classApplier = $this->getItemClassApplier($item);

        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        if($item != null) {
            HTMLInterface::addAttribute("value", $value);
        }

        HTMLInterface::addAttribute("placeholder", $this->title);
        $this->appendMainAttributes($item);
        Styler::startAttribute();
        $this->appendMainStyles($item);
        Styler::addStyle("height", "35px");
        Styler::closeAttribute();

        if(count($classApplier->classes) > 0){
            $classApplier->applyClasses();
        }

        HTMLInterface::closeSingleTag();
    }

    public function getItemClassApplier($item) : Styler {
        $classApplier = new Styler();
        if($this->isNumeric) $classApplier->pushClass("numeric-text");
        if($this->submitter) $classApplier->pushClass("submitter");
        return $classApplier;
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

    public function preLoad(){}
}

class TextAreaTableField extends SBEditableField {
    public int $rows = 10;
    public int $columns = 50;

    public function presentValue($item) {
        echo '<textarea ';
        HTMLInterface::addAttribute("placeholder", $this->title);
        HTMLInterface::addAttribute("rows", $this->rows);
        HTMLInterface::addAttribute("cols", $this->columns);
        $this->appendMainAttributes($item);
        Styler::startAttribute();
        $this->appendMainStyles($item);
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        if($item != null) {
            echo $this->getValue($item);
        }

        echo '</textarea>';
    }

    public function setRows(int $rows) : TextAreaTableField {
        $this->rows = $rows;
        return $this;
    }

    public function setColumns(int $columns) : TextAreaTableField {
        $this->columns = $columns;
        return $this;
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
        $this->appendMainAttributes($item);
        Styler::startAttribute();
        $this->appendMainStyles($item);
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }
}

class RecordSelectorField extends CheckboxField {
    public function __construct(string $title, EntityID $idGetter){
        parent::__construct($title, "select_Record", $idGetter);
    }

    public function getValue($item): string {
        return false;
    }
}
