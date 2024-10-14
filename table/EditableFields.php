<?php

abstract class SBEditableField extends SBTableField {
    public string | null $namespace = null;

    public function __construct(string $title, string $key){
        parent::__construct($title, $key);
        $this->editable = true;
    }

    public abstract function getItemID($item) : string;

    public function getEditableFieldJSId($item) : string {
        $id = "";
        if($this->namespace != null) $id = $this->namespace . '_';
        $id .= $this->key;
        $id .= "_";
        $id .= $this->getItemID($item);
        return $id;
    }

    public function presentValue($item){
        echo '<input type="text" value="' .
            $this->getValue($item) . '" placeholder="' . $this->title
            . '" id="' . $this->getEditableFieldJSId($item) . '" style="height: 35px;" />';
    }
}
