<?php
namespace Avetify\Table;

use Avetify\Entities\SetModifier;
use Avetify\Entities\Sorters\SortFactor;
use Avetify\Fields\JSDataElement;
use Avetify\Table\Fields\EditableFields\CheckboxField;
use Avetify\Table\Fields\EditableFields\EditableField;
use Avetify\Table\Fields\FieldsContainers\FieldsContainer;
use Avetify\Table\Fields\TableField;
use Avetify\Table\Fields\TableSortField;
use Avetify\Themes\Green\GreenTableRenderer;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\SetRenderer;

class AvtTable extends SetModifier {
    /** @var TableField[] $fields */
    public array $fields;

    /** @var JSDataElement[] */
    public array $requiredDatalists = [];

    public bool $enableSelectRecord = false;
    public bool $enableCreatingRow = false;
    public bool $creatingRowOnTop = false;
    public bool $forcePatchRecords = false;
    public bool $enableAutoPatchCreatedAt = false;
    public bool $enableAutoPatchUpdatedAt = false;

    public function __construct(array $fields, array $rawRecords, string $key, bool $isEditable = false){
        parent::__construct($key);

        $this->isEditable = $isEditable;
        $this->renderer = $this->getTableRenderer();

        $this->setFields($fields);
        $this->loadRawRecords($rawRecords);
        $this->renderer->limit = $this->recordsLimit();
    }

    public function setFields(array $fields){
        $this->fields = $fields;

        foreach ($this->getAllFields() as $field){
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

    /** @return TableField[] */
    public function getAllFields() : array {
        $pureFields = [];
        foreach ($this->fields as $field){
            if($field instanceof FieldsContainer){
                foreach ($field->childs as $pField){
                    $pureFields[] = $pField;
                }
            }
            else $pureFields[] = $field;
        }
        return $pureFields;
    }

    /** @return SortFactor[] An array of MyClass instances */
    public function getDefaultSortFactors() : array {
        $out = [];
        foreach ($this->getAllFields() as $field){
            if($field->isSortable){
                $out[] = new TableSortField($field);
            }
        }
        return $out;
    }

    /** @return SortFactor[] An array of MyClass instances */
    public function moreSortFactors() : array {
        return [];
    }

    /** @return SortFactor[] An array of MyClass instances */
    public function finalSortFactors() : array {
        return array_merge($this->getDefaultSortFactors(), $this->moreSortFactors());
    }

    public function recordsLimit() : int {
        return 5000;
    }

    protected function getTableRenderer() : SetRenderer {
        return new GreenTableRenderer($this, new GreenTheme());
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
                foreach ($this->getAllFields() as $field) {
                    if ($field->onCreateField != null) {
                        $crFieldKey = $field->onCreateField->getEditableFieldIdentifier(null);
                        $crKey = $field->onCreateField->key;
                        if ($field->onCreateField instanceof CheckboxField) {
                            $creatingFields[$crKey] = empty($_POST[$crFieldKey]) ? false : true;
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
        foreach ($this->getAllFields() as $field){
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
