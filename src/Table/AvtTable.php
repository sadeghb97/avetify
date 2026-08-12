<?php
namespace Avetify\Table;

use Avetify\Entities\FilterFactors\FilterFactor;
use Avetify\Entities\FilterFactors\FilterField;
use Avetify\Entities\Models\PaginationConfigs;
use Avetify\Entities\SetManager;
use Avetify\Entities\Sorters\SortFactor;
use Avetify\Fields\BaseRecordField;
use Avetify\Interface\RecordFormTrait;
use Avetify\Table\Fields\Containers\TableFieldsContainer;
use Avetify\Table\Fields\EditableFields\CheckboxField;
use Avetify\Table\Fields\EditableFields\EditableField;
use Avetify\Table\Fields\TableField;
use Avetify\Table\Fields\TableFieldWrapper;
use Avetify\Table\Fields\TableSortField;
use Avetify\Themes\Green\GreenTableRenderer;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\SetRenderer;
use Avetify\Themes\Main\AvtTheme;

class AvtTable extends SetManager {
    use RecordFormTrait;

    /** @var TableField[] $fields */
    public array $fields = [];

    public bool $enableSelectRecord = false;
    public bool $enableCreatingRow = false;
    public bool $creatingRowOnTop = false;
    public bool $forcePatchRecords = false;
    public bool $enableAutoPatchCreatedAt = false;
    public bool $enableAutoPatchUpdatedAt = false;

    private array | null $_defaultFilterFactors = null;
    private array | null $_defaultSortFactors = null;

    public function __construct(array $fields, array $rawRecords, string $key, bool $isEditable = false){
        parent::__construct($key);

        $this->isEditable = $isEditable;
        $this->setFields($fields);
        $this->renderer = $this->getTableRenderer();
        $this->loadRawRecords($rawRecords);
        $this->renderer->limit = $this->recordsLimit();
    }

    public function createPaginationConfigs(): ?PaginationConfigs {
        return new PaginationConfigs($this->setKey, $this->getPageRecordsCount());
    }

    public function setFields(array $fields){
        $this->fields = $fields;

        foreach ($this->getAllTableFields() as $field){
            if($field->isEditable()){
                if($field instanceof EditableField) $field->namespace = $this->setKey;
                $field->idGetter = $this;
            }
            if($field->onCreateField != null && $field->onCreateField->isEditable()){
                $field->onCreateField->namespace = $this->setKey;
                $field->onCreateField->idGetter = $this;
                $field->onCreateField->onlyNameIdentifier();
            }
        }
    }

    /** @return BaseRecordField[] */
    public function getPureFields() : array {
        $pureFields = [];
        $rawFields = $this->fields;
        foreach ($rawFields as $field){
            $this->_extractPureFields($pureFields, $field);
        }
        return $pureFields;
    }

    private function _extractPureFields(array &$curFields, $field) : void {
        if($field instanceof TableFieldWrapper){
            if(property_exists($field->recordField, "childs")) {
                foreach ($field->recordField->childs as $pField) {
                    $this->_extractPureFields($curFields, $pField);
                }
            }
            else $this->_extractPureFields($curFields, $field->recordField);
        }
        else $curFields[] = $field;
    }

    /** @return TableField[] */
    public function getAllTableFields() : array {
        $tableFields = [];
        $rawFields = $this->fields;
        foreach ($rawFields as $field){
            $this->_extractTableFields($tableFields, $field);
        }
        return $tableFields;
    }

    private function _extractTableFields(array &$curFields, $field) : void {
        if($field instanceof TableFieldsContainer){
            if(property_exists($field->recordField, "childs")) {
                foreach ($field->recordField->childs as $pField) {
                    $this->_extractTableFields($curFields, $pField);
                }
            }
        }
        else $curFields[] = $field;
    }

    /** @return SortFactor[] An array of MyClass instances */
    public function getDefaultSortFactors() : array {
        if($this->_defaultSortFactors !== null) return $this->_defaultSortFactors;
        $this->_defaultSortFactors = [];
        foreach ($this->getAllTableFields() as $field){
            if($field->isSortable){
                $this->_defaultSortFactors[] = new TableSortField($field);
            }
        }
        return $this->_defaultSortFactors;
    }

    /** @return SortFactor[] An array of MyClass instances */
    public function moreSortFactors() : array {
        return [];
    }

    /** @return SortFactor[] An array of MyClass instances */
    public function finalSortFactors() : array {
        return array_merge($this->getDefaultSortFactors(), $this->moreSortFactors());
    }

    /** @return FilterFactor[] An array of MyClass instances */
    public function getDefaultFilterFactors() : array {
        if($this->_defaultFilterFactors !== null) return $this->_defaultFilterFactors;
        $this->_defaultFilterFactors = [];
        foreach ($this->getAllTableFields() as $field){
            if($field->isFilterable){
                $clonedField = clone $field;
                $clonedField->isReadonly = false;
                if(property_exists($clonedField, "useNameIdentifier")){
                    $clonedField->useNameIdentifier = true;
                }
                if(property_exists($clonedField, "namespace")){
                    $clonedField->namespace = FilterFactor::getStorageIdentifier($clonedField->namespace);
                }
                $this->_defaultFilterFactors[] = new FilterField($clonedField);
            }
        }
        return $this->_defaultFilterFactors;
    }

    /** @return FilterFactor[] An array of MyClass instances */
    public function moreFilterFactors() : array {
        return [];
    }

    /** @return FilterFactor[] An array of MyClass instances */
    public function finalFilterFactors() : array {
        $filters = array_merge($this->getDefaultFilterFactors(), $this->moreFilterFactors());
        foreach ($filters as $filter){
            if(property_exists($filter, "namespace")) $filter->namespace = $this->setKey;
        }
        return $filters;
    }

    public function recordsLimit() : int {
        return 5000;
    }

    protected function createBaseTheme() : AvtTheme {
        return new GreenTheme();
    }

    final protected function getFinalTheme() : AvtTheme {
        $theme = $this->createBaseTheme();

        $fields = $this->getPureFields();
        foreach ($fields as $field){
            $field->attachRequirementsToTheme($theme);
        }

        return $theme;
    }

    protected function getTableRenderer() : SetRenderer {
        return new GreenTableRenderer($this, $this->getFinalTheme());
    }

    public function catchSubmittedFields(){
        $tableRenderer = $this->renderer;
        $currentTrigger = $tableRenderer->form->getCurrentTrigger();

        $selectedRecords = [];
        if(!empty($_POST[$tableRenderer->getFormSelectorName()])){
            $activeSelectElements = json_decode($_POST[$tableRenderer->getFormSelectorName()], true);
            foreach ($activeSelectElements as $selectElementID){
                $lastPos = strrpos($selectElementID, "_");
                $itemId = substr($selectElementID, $lastPos + 1);
                $selectedRecords[] = $itemId;
            }
        }

        if($currentTrigger == $tableRenderer->getDeleteButtonID()) {
            $this->handleDeletingFields($selectedRecords);
        }
        else if (isset($_POST[$tableRenderer->getFormFieldsName()])) {
            if($this->enableCreatingRow) {
                $creatingFields = [];
                foreach ($this->getAllTableFields() as $field) {
                    if ($field->onCreateField != null) {
                        $crFieldKey = $field->onCreateField->getElementIdentifier(null);
                        $crKey = $field->onCreateField->key;
                        if ($field->onCreateField instanceof CheckboxField) {
                            $creatingFields[$crKey] = !empty($_POST[$crFieldKey]);
                        } else {
                            $creatingFields[$crKey] = $_POST[$crFieldKey];
                        }
                    }
                }
                $this->handleCreatingFields($creatingFields);
            }

            $itemsFields = [];
            $tableFieldsRaw = $_POST[$tableRenderer->getFormFieldsName()];
            $tableFieldsList = json_decode($tableFieldsRaw, true);

            foreach ($tableFieldsList as $tableFieldRawKey => $tableFieldValue) {
                $lastPos = strrpos($tableFieldRawKey, "_");
                if (strlen($tableFieldRawKey) > ($lastPos + 1)) {
                    $itemId = substr($tableFieldRawKey, $lastPos + 1);
                    $remains = substr($tableFieldRawKey, 0, $lastPos);

                    if (strlen($remains) > (strlen($this->setKey) + 1)) {
                        $itemFieldKey = substr($remains, (strlen($this->setKey) + 1));
                        if (!isset($itemsFields[$itemId])) $itemsFields[$itemId] = [];
                        $itemsFields[$itemId][$itemFieldKey] = $tableFieldValue;
                    }
                }
            }

            $this->handleSubmittedFields($itemsFields);
        }
    }

    public function getFieldsMap() : array {
        $fieldsMap = [];
        foreach ($this->getAllTableFields() as $field){
            $fieldsMap[$field->key] = $field;
        }
        return $fieldsMap;
    }

    public function handleSubmittedFields($itemsFields){}

    public function handleCreatingFields($creatingFields){}

    public function handleDeletingFields($deletingFields){}

    public function getEntityRecords(): array {
        return $this->records;
    }
}
