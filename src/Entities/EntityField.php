<?php
namespace Avetify\Entities;

use Avetify\Fields\BaseRecordField;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\WebModifier;

class EntityField extends BaseRecordField implements IdentifiedElement {
    use IdentifiedElementTrait;

    public bool $hidden = false;
    public bool $rtl = false;
    public bool $writable = false; // add field to edit and add forms
    public bool $protected = false; // no patch on insert or create queries
    public bool $printable = true; // print in forms
    public bool $required = false; // must have value in add and edit forms
    public bool $numeric = false;
    public bool $special = false; //ignore it on auto insert and update queries
    public bool $autoTimeCreate = false; //na special na writable
    public bool $autoTimeUpdate = false; //na special na writable
    //auto generated fields na special hastan na writable

    public function __construct(string $key, string $title){
        parent::__construct($key, $title);
        $this->useNameIdentifier = true;

        $this->baseModifier->pushStyle("font-size", "14pt");
        $this->baseModifier->pushStyle("margin-top", "8px");
        $this->baseModifier->pushStyle("margin-bottom", "8px");
        $this->baseModifier->pushStyle("padding-left", "8px");
        $this->baseModifier->pushStyle("padding-right", "8px");
        $this->baseModifier->pushStyle("padding-top", "4px");
        $this->baseModifier->pushStyle("padding-bottom", "4px");

        $this->postConstruct();
    }

    public function postConstruct(){}

    public function setRtl() : EntityField {
        $this->baseModifier->popStyle("padding-top");
        $this->baseModifier->popStyle("padding-bottom");
        $this->baseModifier->pushStyle("direction", "rtl");
        $this->baseModifier->pushStyle("font-family", "'IranSans', sans-serif");
        $this->rtl = true;
        return $this;
    }

    public function setHidden() : EntityField {
        $this->hidden = true;
        return $this;
    }


    public function setWritable() : EntityField {
        $this->writable = true;
        return $this;
    }

    public function setProtected() : EntityField {
        $this->protected = true;
        return $this;
    }

    public function notPrintable() : EntityField {
        $this->printable = false;
        return $this;
    }

    public function setWritableOnCreate() : EntityField {
        $this->writable = "create";
        return $this;
    }

    public function setRequired() : EntityField {
        $this->required = true;
        return $this;
    }

    public function setNumeric() : EntityField {
        $this->numeric = true;
        return $this;
    }

    public function setSpecial() : EntityField {
        $this->special = true;
        return $this;
    }

    public function setAutoTimeCreate() : EntityField {
        $this->autoTimeCreate = true;
        return $this;
    }

    public function setAutoTimeUpdate() : EntityField {
        $this->autoTimeUpdate = true;
        return $this;
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        if($this->writable){
            $this->presentWritableField($item, $webModifier);
        }
        else if($this->printable){
            $this->presentReadonlyField($item, $webModifier);
        }
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $title = $this->title;
        $key = $this->key;
        $value = $this->getValue($item);
        $cloneModifier = clone $webModifier;

        $cloneModifier->pushClass("empty");
        if($this->numeric) $cloneModifier->pushClass("numeric-text");

        echo '<input ';
        HTMLInterface::addAttribute("type","text");
        $this->placeElementIdAttributes();
        HTMLInterface::addAttribute("value", $value);
        HTMLInterface::addAttribute("placeholder", $title);
        $cloneModifier->htmlModifier->applyModifiers();

        Styler::classStartAttribute();
        $cloneModifier->styler->appendClasses();
        Styler::closeAttribute();

        Styler::startAttribute();
        $cloneModifier->styler->appendStyles();
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }

    public function presentReadonlyField($item, ?WebModifier $webModifier = null) {
        $cloneModifier = clone $webModifier;
        $value = $this->getValue($item);
        if(strlen($value) > 0) {
            echo '<div ';
            $cloneModifier->htmlModifier->applyModifiers();

            Styler::classStartAttribute();
            $cloneModifier->styler->appendClasses();
            Styler::closeAttribute();

            Styler::startAttribute();
            Styler::addStyle("margin-top", "6px");
            Styler::addStyle("margin-bottom", "6px");
            $cloneModifier->styler->appendStyles();
            Styler::closeAttribute();
            HTMLInterface::closeTag();
            echo $this->title . ': ' . $value;
            HTMLInterface::closeDiv();
        }
    }

    public function getElementIdentifier($item = null) {
        return $this->key;
    }
}
