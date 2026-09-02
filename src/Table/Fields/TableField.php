<?php
namespace Avetify\Table\Fields;

use Avetify\DB\Filters\DBFilter;
use Avetify\DB\Filters\DBFilterInterface;
use Avetify\Entities\FilterFactors\Qualifier;
use Avetify\Fields\BaseRecordField;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Table\Fields\EditableFields\EditableField;

class TableField extends BaseRecordField implements Qualifier {
    public bool $rtl = false;
    public bool $isCentered = true;
    public bool $isUnbreakable = false;
    public bool $isSortable = false;
    public bool $isFilterable = false;
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

    public function __construct(string $title, string $key){
        parent::__construct($key, $title);
        $this->postConstruct();
    }

    public function postConstruct(){}

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
        $this->placeField($item);
        self::closeTD();
    }

    public function setDefaultSort() : static {
        $this->isDefaultSort = true;
        return $this;
    }

    public function setRtl() : static {
        $this->rtl = true;
        return $this;
    }

    public function noCentered() : static {
        $this->isCentered = false;
        return $this;
    }

    public function setUnbreakable() : static {
        $this->isUnbreakable = true;
        return $this;
    }

    public function setSortable() : static {
        $this->isSortable = true;
        return $this;
    }

    public function setFilterable() : static {
        $this->isFilterable = true;
        return $this;
    }

    public function setAscending() : static {
        $this->isAscending = true;
        return $this;
    }

    public function setTiebreaks(array $tieBreaks) : static {
        $this->tieBreaks = $tieBreaks;
        return $this;
    }

    public function setBackgroundColor(string | null $bg) : static {
        $this->backgroundColor = $bg;
        return $this;
    }

    public function setColor(string | null $color) : static {
        $this->color = $color;
        return $this;
    }

    public function setWidth(string $w) : static {
        $this->width = $w;
        return $this;
    }

    public function setMaxWidth(string $w) : static {
        $this->maxWidth = $w;
        return $this;
    }

    public function setMinWidth(string $w) : static {
        $this->minWidth = $w;
        return $this;
    }

    public function setFontSize(string | null $fs) : static {
        $this->fontSize = $fs;
        return $this;
    }

    public function setFontWeight(string | null $fw) : static {
        $this->fontWeight = $fw;
        return $this;
    }

    public function setSkipEmpties() : static {
        $this->skipEmpties = true;
        return $this;
    }

    public function setSubmitter() : static {
        $this->submitter = true;
        return $this;
    }

    public function bold() : static {
       return $this->setFontWeight("bold");
    }

    public function appendDBValueMapper($orgValue, $mappedValue) : static {
        $this->dbValueMappers[$orgValue] = $mappedValue;
        return $this;
    }

    public function setMaxFieldCharacters(int $maxFieldCharacters) : static {
        $this->maxFieldCharacters = $maxFieldCharacters;
        return $this;
    }

    public function isEditable() : bool {
        return $this->editable;
    }

    public function isQualified($item, $param): bool {
        return !!$this->getValue($item);
    }

    public function dbQualifyingFilter($paramValue): DBFilterInterface | null {
        return new DBFilter($this->getDbSelectorExpression(), "=", $paramValue, $this->isNumeric);
    }

    public function sortQualified($item) : bool {
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