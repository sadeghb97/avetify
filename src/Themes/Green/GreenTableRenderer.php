<?php
namespace Avetify\Themes\Green;

use Avetify\Entities\SetModifier;
use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Table\AvtTable;
use Avetify\Table\Fields\RecordSelectorField;
use Avetify\Table\Fields\TableField;
use Avetify\Themes\Main\SetRenderer;
use Avetify\Themes\Main\ThemesManager;

class GreenTableRenderer extends SetRenderer {
    public function __construct(SetModifier $setModifier, ThemesManager $theme,
                                string $title = "Set", bool|int $limit = 5000){
        parent::__construct($setModifier, $theme, $title, $limit);
    }

    public function renderCreatingElements() {
        if(!($this->setModifier instanceof AvtTable)) return;

        /** @var AvtTable $sbTable */
        $sbTable = $this->setModifier;

        $this->openRecord(null);
        if($this->printRowIndex) $this->placeEmptyTD();
        foreach ($sbTable->fields as $field){
            if($field->onCreateField != null){
                $field->onCreateField->renderRecord(null);
            }
            else $this->placeEmptyTD();
        }
        if($sbTable->enableSelectRecord) $this->placeEmptyTD();
        $this->closeRecord(null);
    }

    public function openCollection(WebModifier $webModifier = null){
        /** @var AvtTable $sbTable */
        $sbTable = $this->setModifier;

        echo '<div ';
        Styler::classStartAttribute();
        //Styler::addClass("tables_panel");
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        Styler::addStyle(CSS::width, "100%");
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::applyModifiers($webModifier);
        HTMLInterface::closeTag();

        echo '<table class="table" style="';
        $this->tableStyles();
        echo '"';
        echo ' >';

        $this->openHeaderTR();
        if($this->printRowIndex) TableField::renderIndexTH("Row");
        foreach ($sbTable->fields as $field){
            $field->renderHeaderTH();
        }
        if($sbTable->enableSelectRecord) TableField::renderIndexTH("Action");
        $this->closeRecord(null);

        if($sbTable->enableSelectRecord) {
            $this->selectorField = new RecordSelectorField("Action", $sbTable);
            $this->selectorField->namespace = $sbTable->setKey;
            $this->selectorField->onlyIDIdentifier();
        }
    }

    public function closeCollection(WebModifier $webModifier = null){
        /** @var AvtTable $sbTable */
        $sbTable = $this->setModifier;

        if($sbTable->enableCreatingRow) $this->renderCreatingElements();
        echo '</table>';
        echo '</div>';
    }

    public function openRecord($record){
        echo '<tr style="';
        $this->normalTRStyles($record);
        echo '">';
    }

    public function closeRecord($record){
        echo '</td>';
    }

    public function renderRecordMain($item, $index) {
        /** @var AvtTable $sbTable */
        $sbTable = $this->setModifier;

        $this->openRecord($item);
        if($this->printRowIndex) TableField::renderIndexTD(
            $index + 1, $sbTable->getItemLink($item));
        foreach ($sbTable->fields as $field){
            if($sbTable->forcePatchRecords && !$field->isEditable() && $field->onCreateField != null){
                $feField = $field->getForcedEditableClone();
                $feField->renderRecord($item);
            }
            else $field->renderRecord($item);
        }
        if($sbTable->enableSelectRecord) $this->selectorField->renderRecord($item);
        $this->closeRecord($item);
    }

    public function getTheme() : ThemesManager {
        return new GreenTheme();
    }

    protected function placeEmptyTD(){
        echo '<td></td>';
    }

    protected function openHeaderTR(){
        echo '<tr style="';
        $this->headerTRStyles();
        echo '">';
    }

    protected function headerTRStyles(){}

    protected function normalTRStyles($record){
        $this->headerTRStyles();
    }

    protected function tableStyles(){}
}
