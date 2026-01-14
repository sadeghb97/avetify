<?php
namespace Avetify\Table\Fields\EditableFields;

use Avetify\Entities\BasicProperties\EntityID;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableField;

class EditableField extends TableField {
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
        if($item != null && $this->idGetter != null) {
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

    public function presentValue($item, ?WebModifier $webModifier = null){
        $value = $this->getValue($item);

        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        if($item != null) {
            HTMLInterface::addAttribute("value", $value);
        }

        HTMLInterface::addAttribute("placeholder", $this->title);
        $this->appendMainAttributes($item);
        HTMLInterface::applyModifiers($webModifier);

        Styler::startAttribute();
        $this->appendMainStyles($item);
        Styler::addStyle("height", "35px");
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();

        Styler::classStartAttribute();
        if($this->isNumeric) Styler::addClass("numeric-text");
        if($this->submitter) Styler::addClass("submitter");
        HTMLInterface::appendClasses($webModifier);
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

    public function preLoad(){}
}






