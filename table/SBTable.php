<?php

class SBTable extends SetModifier {
    public array $records;

    /** @var SBTableField[] $fields */
    public array $fields;

    public bool $enableSelectRecord = false;
    public bool $enableCreatingRow = false;
    public bool $forcePatchRecords = false;
    public SetRenderer | null $tableRenderer = null;

    public function __construct(array $fields, array $rawRecords, string $key, bool $isEditable = false){
        parent::__construct($key);

        $this->isEditable = $isEditable;
        $this->tableRenderer = $this->getTableRenderer();

        $this->setFields($fields);
        $this->loadRawRecords($rawRecords);
        $this->tableRenderer->limit = $this->recordsLimit();
    }

    public function setFields(array $fields){
        $this->fields = $fields;

        foreach ($this->fields as $field){
            if($field->isEditable()){
                if($field instanceof SBEditableField) $field->namespace = $this->setKey;
                $field->idGetter = $this;
                if($field->onCreateField != null){
                    $field->onCreateField->namespace = $this->setKey;
                    $field->onCreateField->idGetter = $this;
                    $field->onCreateField->onlyNameIdentifier();
                }
            }
        }
    }

    public function loadRawRecords($rawRecords){
        $this->records = $rawRecords;
        $this->adjustRecords();
    }

    /** @return SortFactor[] An array of MyClass instances */
    public function getDefaultSortFactors() : array {
        $out = [];
        foreach ($this->fields as $field){
            if($field->isSortable){
                $out[] = new SBTableSortField($field);
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

    public function openPage(string $title){
        $this->tableRenderer->title = $title;
        $this->tableRenderer->openPage();
    }

    public function renderBody(){
        $this->tableRenderer->renderBody();
    }

    public function renderPage(string $title){
        $this->tableRenderer->title = $title;
        $this->tableRenderer->renderPage();
    }

    protected function getTableRenderer() : SetRenderer {
        return new GreenTableRenderer($this, new GreenTheme());
    }

    public function catchSubmittedFields(){
        $tableRenderer = $this->tableRenderer;
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
                foreach ($this->fields as $field) {
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
        foreach ($this->fields as $field){
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
