<?php
namespace Avetify\Entities;

use Avetify\Fields\BaseRecordField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class EntityField extends BaseRecordField {
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

        $this->postConstruct();
    }

    public function postConstruct(){}

    public function setType(string $type) : EntityField {
        $this->type = $type;
        return $this;
    }

    public function setPath(string $path) : EntityField {
        $this->path = $path;
        return $this;
    }

    public function setRtl() : EntityField {
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

    public function setMaxImageSize(string $imageSize) : EntityField {
        $this->maxImageSize = $imageSize;
        return $this;
    }

    public function setImageForcedRatio(int $widthDim, int $heightDim) : EntityField {
        $this->forcedWidthDimension = $widthDim;
        $this->forcedHeightDimension = $heightDim;
        return $this;
    }

    public function getImageForcedRatio() : float {
        if($this->forcedWidthDimension > 0 && $this->forcedHeightDimension > 0)
            return $this->forcedWidthDimension / $this->forcedHeightDimension;
        return 0;
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

        $classApplier = new Styler();
        $classApplier->pushClass("empty");
        if($this->numeric) $classApplier->pushClass("numeric-text");

        echo '<input ';
        HTMLInterface::addAttribute("type","text");
        $this->placeElementIdAttributes();
        HTMLInterface::addAttribute("value", $value);
        HTMLInterface::addAttribute("placeholder", $title);

        Styler::classStartAttribute();
        $classApplier->appendClasses();
        Styler::closeAttribute();

        Styler::startAttribute();
        Styler::addStyle("width", "80%");
        Styler::addStyle("font-size", "14pt");
        Styler::addStyle("margin-top", "8px");
        Styler::addStyle("margin-bottom", "8px");
        Styler::addStyle("padding-left", "8px");
        Styler::addStyle("padding-right", "8px");
        Styler::addStyle("padding-top", "4px");
        Styler::addStyle("padding-bottom", "4px");
        if($this->rtl) {
            Styler::addFontFaceStyle("IranSans");
            Styler::addStyle("direction", "rtl");
        }
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }

    public function presentReadonlyField($item, ?WebModifier $webModifier = null) {
        $value = $this->getValue($item);
        if(strlen($value) > 0) {
            echo '<div ';
            Styler::startAttribute();
            Styler::addStyle("margin-top", "6px");
            Styler::addStyle("margin-bottom", "6px");
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
