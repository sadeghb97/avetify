<?php

class SBTableField {
    public bool $isNumeric = false;
    public bool $isPersian = false;
    public bool $isCentered = true;
    public bool $isUnbreakable = false;
    public bool $isSortable = false;
    public bool $isAscending = false;
    protected bool $editable = false;
    public string | null $backgroundColor = null;
    public string | null $color = null;
    public string | null $width = null;
    public string | null $maxWidth = null;
    public string | null $fontSize = null;
    public string | null $fontWeight = null;

    public function __construct(public string $title, public string $key){}

    public function getValue($item) : string {
        return ((array) $item)[$this->key];
    }

    public function headerCellStyles(){
        if($this->isPersian) {
            SBTable::addStyle("font-family", "IranSans");
            SBTable::addStyle("direction", "rtl");
        }
        if($this->isCentered) SBTable::addStyle("text-align", "center");
        if($this->width != null) SBTable::addStyle("width", $this->width);
        if($this->maxWidth != null) SBTable::addStyle("max-width", $this->maxWidth);
        if(!$this->isUnbreakable) SBTable::addStyle("word-wrap", "break-word");
    }

    public function normalCellStyles($item){
        $this->headerCellStyles();
        if($this->backgroundColor != null) SBTable::addStyle("background-color", $this->backgroundColor);
        if($this->color != null) SBTable::addStyle("color", $this->color);
        if($this->fontSize != null) SBTable::addStyle("font-size", $this->fontSize);
        if($this->fontWeight != null) SBTable::addStyle("font-weight", $this->fontWeight);
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

    public function presentValue($item){
        echo $this->getValue($item);
    }

    public function setNumeric() : SBTableField {
        $this->isNumeric = true;
        return $this;
    }

    public function setPersian() : SBTableField {
        $this->isPersian = true;
        return $this;
    }

    public function noCentered() : SBTableField {
        $this->isCentered = false;
        return $this;
    }

    public function setUnbreakable() : SBTableField {
        $this->isUnbreakable = true;
        return $this;
    }

    public function setSortable() : SBTableField {
        $this->isSortable = true;
        return $this;
    }

    public function setAscending() : SBTableField {
        $this->isAscending = true;
        return $this;
    }

    public function setBackgroundColor(string | null $bg) : SBTableField {
        $this->backgroundColor = $bg;
        return $this;
    }

    public function setColor(string | null $color) : SBTableField {
        $this->color = $color;
        return $this;
    }

    public function setWidth(string $w) : SBTableField {
        $this->width = $w;
        return $this;
    }

    public function setMaxWidth(string $w) : SBTableField {
        $this->maxWidth = $w;
        return $this;
    }

    public function setFontSize(string | null $fs) : SBTableField {
        $this->fontSize = $fs;
        return $this;
    }

    public function setFontWeight(string | null $fw) : SBTableField {
        $this->fontWeight = $fw;
        return $this;
    }

    public function bold() : SBTableField {
       return $this->setFontWeight("bold");
    }

    public function isEditable() : bool {
        return $this->editable;
    }

    public static function renderIndexTH($rowTitle){
        echo '<th style="text-align: center">';
        echo $rowTitle;
        echo '</th>';
    }

    public static function renderIndexTD($value){
        echo '<td style="text-align: center">';
        echo $value;
        echo '</td>';
    }

    private static function closeTH(){
        echo '</th>';
    }

    private static function closeTD(){
        echo '</td>';
    }
}

class SBTableSimpleField extends SBTableField {
    public function getValue($item): string {
        return self::getValueByKey($item, $this->key);
    }

    public static function getValueByKey($item, $key) : string {
        if(is_array($item) && isset($item[$key])) return $item[$key];
        $arItem = (array) $item;
        if(isset($arItem[$key])) return $arItem[$key];
        return "";
    }
}