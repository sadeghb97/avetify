<?php
namespace Avetify\Table\Fields;

use Avetify\Entities\EntityUtils;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\EditableFields\EditableField;

class TableField {
    public bool $isNumeric = false;
    public bool $rtl = false;
    public bool $isCentered = true;
    public bool $isUnbreakable = false;
    public bool $isSortable = false;
    public bool $isAscending = false;
    public array $tieBreaks = [];
    protected bool $editable = false;
    public bool $skipEmpties = false;
    public bool $submitter = false;
    public bool $isDefaultSort = false;
    public EditableField | null $onCreateField = null;
    public bool $requiredOnCreate = false;
    public string | null $backgroundColor = null;
    public string | null $color = null;
    public string | null $width = null;
    public string | null $maxWidth = null;
    public string | null $minWidth = null;
    public string | null $fontSize = null;
    public string | null $fontWeight = null;

    public function __construct(public string $title, public string $key){
        $this->postConstruct();
    }

    public function postConstruct(){}

    public function getValue($item) : string {
        if(!$item) return "";
        if(!is_array($item) && !is_object($item)) return $item;
        if(str_contains($this->key, "~")) $finalKeys = explode("~", $this->key);
        else $finalKeys = $this->key;
        return EntityUtils::getSimpleValue($item, $finalKeys);
    }

    public function headerCellStyles(){
        if($this->rtl) {
            Styler::addFontFaceStyle("IranSans");
            Styler::addStyle("direction", "rtl");
        }
        if($this->isCentered) Styler::addStyle("text-align", "center");
        if($this->width != null) Styler::addStyle("width", $this->width);
        if($this->maxWidth != null) Styler::addStyle("max-width", $this->maxWidth);
        if($this->minWidth != null) Styler::addStyle("min-width", $this->minWidth);
        if(!$this->isUnbreakable) Styler::addStyle("word-wrap", "break-word");
    }

    public function normalCellStyles($item){
        $this->headerCellStyles();
        if($this->backgroundColor != null) Styler::addStyle("background-color", $this->backgroundColor);
        if($this->color != null) Styler::addStyle("color", $this->color);
        if($this->fontSize != null) Styler::addStyle("font-size", $this->fontSize);
        if($this->fontWeight != null) Styler::addStyle("font-weight", $this->fontWeight);
    }

    public function renderHeaderTH(){
        echo '<th style="';
        $this->headerCellStyles();
        echo '">';
        echo $this->title;
        self::closeTH();
    }

    public function openNormalTD($item){
        echo '<td style="';
        $this->normalCellStyles($item);
        echo '">';
    }

    public function renderRecord($item){
        $this->openNormalTD($item);
        $this->presentValue($item);
        self::closeTD();
    }

    public function presentValue($item, ?WebModifier $webModifier = null){
        echo $this->getValue($item);
    }

    public function setNumeric() : TableField {
        $this->isNumeric = true;
        return $this;
    }

    public function setDefaultSort() : TableField {
        $this->isDefaultSort = true;
        return $this;
    }

    public function setRtl() : TableField {
        $this->rtl = true;
        return $this;
    }

    public function noCentered() : TableField {
        $this->isCentered = false;
        return $this;
    }

    public function setUnbreakable() : TableField {
        $this->isUnbreakable = true;
        return $this;
    }

    public function setSortable() : TableField {
        $this->isSortable = true;
        return $this;
    }

    public function setAscending() : TableField {
        $this->isAscending = true;
        return $this;
    }

    public function setTiebreaks(array $tieBreaks) : TableField {
        $this->tieBreaks = $tieBreaks;
        return $this;
    }

    public function setBackgroundColor(string | null $bg) : TableField {
        $this->backgroundColor = $bg;
        return $this;
    }

    public function setColor(string | null $color) : TableField {
        $this->color = $color;
        return $this;
    }

    public function setWidth(string $w) : TableField {
        $this->width = $w;
        return $this;
    }

    public function setMaxWidth(string $w) : TableField {
        $this->maxWidth = $w;
        return $this;
    }

    public function setMinWidth(string $w) : TableField {
        $this->minWidth = $w;
        return $this;
    }

    public function setFontSize(string | null $fs) : TableField {
        $this->fontSize = $fs;
        return $this;
    }

    public function setFontWeight(string | null $fw) : TableField {
        $this->fontWeight = $fw;
        return $this;
    }

    public function setSkipEmpties() : TableField {
        $this->skipEmpties = true;
        return $this;
    }

    public function setSubmitter() : TableField {
        $this->submitter = true;
        return $this;
    }

    public function bold() : TableField {
       return $this->setFontWeight("bold");
    }

    public function isEditable() : bool {
        return $this->editable;
    }

    public function isQualified($item) : bool {
        if(!$this->skipEmpties) return true;
        $value = $this->getValue($item);
        if(!$value) return false;
        return true;
    }

    public function setEditableOnCreate(bool $required, EditableField $editableField) : TableField {
        $this->onCreateField = $editableField;
        $this->onCreateField->requiredOnCreate = $required;
        $this->onCreateField->useNameIdentifier = true;
        return $this;
    }

    public function autoEditableOnCreate(bool $required = false) : TableField {

        if($this instanceof EditableField) $this->onCreateField = clone $this;
        else $this->onCreateField = new EditableField($this->title, $this->key);
        $this->onCreateField->rtl = $this->rtl;

        $this->onCreateField->requiredOnCreate = $required;
        $this->onCreateField->useNameIdentifier = true;
        return $this;
    }

    public function getForcedEditableClone() : EditableField {
        $feField = clone $this->onCreateField;
        $feField->useNameIdentifier = false;
        $feField->useIDIdentifier = true;
        return $feField;
    }

    public static function renderIndexTH($rowTitle){
        echo '<th style="text-align: center">';
        echo $rowTitle;
        echo '</th>';
    }

    public static function renderIndexTD($value, $link = ""){
        echo '<td style="text-align: center">';

        if($link) {
            echo '<a ';
            HTMLInterface::addAttribute("target", "_blank");
            HTMLInterface::addAttribute("href", $link);
            Styler::startAttribute();
            Styler::addStyle("color", "black");
            Styler::closeAttribute();
            HTMLInterface::closeTag();
        }

        echo $value;

        if($link) echo '</a>';
        echo '</td>';
    }

    private static function closeTH(){
        echo '</th>';
    }

    private static function closeTD(){
        echo '</td>';
    }
}