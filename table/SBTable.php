<?php

class SBTable extends SetModifier {
    public array $records;

    /** @var SBTableField[] $fields */
    public array $fields;
    public RecordSelectorField | null $selectorField = null;
    public SBForm | null $form = null;
    public bool $printRowIndex = true;
    public bool $enableSelectRecord = false;
    public bool $enableCreatingRow = false;
    public bool $useClassicButtons = false;
    public bool $isSortable = true;

    public function __construct(array $fields, array $rawRecords, string $key,
                                public bool $isEditable = false, public IDGetter | null $idGetter = null){
        parent::__construct($key);
        if($this->isEditable && $this->idGetter == null){
            $this->idGetter = new SimpleIDGetter("id");
        }
        $this->setFields($fields);
        $this->loadRawRecords($rawRecords);
    }

    public function setFields(array $fields){
        $this->fields = $fields;

        foreach ($this->fields as $field){
            if($field->isEditable()){
                if($field instanceof SBEditableField) $field->namespace = $this->setKey;
                $field->idGetter = $this->idGetter;
                if($field->onCreateField != null){
                    $field->onCreateField->namespace = $this->setKey;
                    $field->onCreateField->idGetter = $this->idGetter;
                    $field->onCreateField->onlyNameIdentifier();
                }
            }
        }
    }

    public function initForm(){
        $this->form = new SBForm($this->getTableFormName());
        $this->form->addHiddenElement(new FormHiddenProperty($this->getRawTableFieldsName(), ""));
        $this->form->addHiddenElement(new FormHiddenProperty($this->getTableSelectorName(), ""));

        $deleteConfirmMessage = "Are you sure?";
        if($this->useClassicButtons) {
            $this->form->addTrigger(new FormButton($this->getTableFormName(), $this->getUpdateButtonID(),
                "Update"));

            $deleteTrigger = new FormButton($this->getTableFormName(), $this->getDeleteButtonID(),
                "Delete", "warning");
            $deleteTrigger->enableConfirmMessage($deleteConfirmMessage);
            $this->form->addTrigger($deleteTrigger);
        }
        else {
            $this->form->addTrigger(new PrimaryFormButton($this->getTableFormName(), $this->getUpdateButtonID()));

            $deleteTrigger = new DeleteFormButton($this->getTableFormName(), $this->getDeleteButtonID());
            $deleteTrigger->enableConfirmMessage($deleteConfirmMessage);
            $this->form->addTrigger($deleteTrigger);
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

    public function placeJSUtils(){
        if($this->isEditable) {
            $allJSEditableFields = [];
            foreach ($this->fields as $field) {
                if ($field instanceof SBEditableField) {
                    foreach ($this->currentRecords as $record) {
                        $allJSEditableFields[] = $field->getEditableFieldIdentifier($record);
                    }
                }
            }

            $allSelectFields = null;
            if($this->selectorField != null){
                $allSelectFields = [];
                foreach ($this->currentRecords as $record) {
                    $allSelectFields[] = $this->selectorField->getEditableFieldIdentifier($record);
                }
            }

            JSInterface::declareGlobalJSArgs($this->getJSArgsName());
            FormUtils::readyFormToCatchNoNamedFields(
                $this->getJSArgsName(),
                $this->getTableFormName(),
                $this->getRawTableFieldsName(),
                $allJSEditableFields,
                $this->isEditable,
                $this->enableSelectRecord ? $this->getTableSelectorName() : null,
                $allSelectFields
            );
        }
    }

    private function placeEmptyTD(){
        echo '<td></td>';
    }

    public function renderCreatingTr(){
        $this->openNormalTR(null);
        if($this->printRowIndex) $this->placeEmptyTD();
        foreach ($this->fields as $field){
            if($field->onCreateField != null){
                $field->onCreateField->renderRecord(null);
            }
            else $this->placeEmptyTD();
        }
        if($this->enableSelectRecord) $this->placeEmptyTD();
        self::closeTR();
    }

    public function renderTable(WebModifier $webModifier = null, int $marginTop = 0){
        $this->initForm();
        if($marginTop > 0){
            echo '<div style="height: ' . $marginTop . 'px;"></div>';
        }

        if($this->isEditable) $this->catchSubmittedFields();
        if($this->isSortable) $this->renderSortLabels();

        if($this->isEditable){
            $this->form->openForm();
        }
        echo '<div class="tables_panel" ';
        if($webModifier != null) $webModifier->apply();
        HTMLInterface::closeTag();
        echo '<table class="table" style="';
        $this->tableStyles();
        echo '"';
        echo ' >';

        $this->openHeaderTR();
        if($this->printRowIndex) SBTableField::renderIndexTH("Row");
        foreach ($this->fields as $field){
            $field->renderHeaderTH();
        }
        if($this->enableSelectRecord) SBTableField::renderIndexTH("Action");
        self::closeTR();

        $recIndex = 1;
        if($this->enableSelectRecord) {
            $this->selectorField = new RecordSelectorField("Action", $this->idGetter);
            $this->selectorField->namespace = $this->setKey;
            $this->selectorField->onlyIDIdentifier();
        }
        foreach ($this->currentRecords as $record){
            $this->openNormalTR($record);
            if($this->printRowIndex) SBTableField::renderIndexTD($recIndex);
            $recIndex++;
            foreach ($this->fields as $field){
                $field->renderRecord($record);
            }
            if($this->enableSelectRecord) $this->selectorField->renderRecord($record);
            self::closeTR();

            if($recIndex > $this->recordsLimit()) break;
        }
        if($this->enableCreatingRow) $this->renderCreatingTr();

        echo '</table>';
        echo '</div>';
        if($this->isEditable) {
            $this->form->placeTriggers();
            $this->form->closeForm();
        }
        $this->placeJSUtils();
    }

    public function openPage(string $title){
        $theme = $this->getTheme();
        $theme->placeHeader($title);
        $theme->loadHeaderElements();
    }

    public function renderPage(string $title){
        $this->openPage($title);
        $this->renderTable();
    }

    public function getTheme() : ThemesManager {
        return new GreenTheme();
    }

    private function catchSubmittedFields(){
        $currentTrigger = $this->form->getCurrentTrigger();

        $selectedRecords = [];
        if(!empty($_POST[$this->getTableSelectorName()])){
            $activeSelectElements = json_decode($_POST[$this->getTableSelectorName()], true);
            foreach ($activeSelectElements as $selectElementID){
                $lastPos = strrpos($selectElementID, "_");
                $itemId = substr($selectElementID, $lastPos + 1);
                $selectedRecords[] = $itemId;
            }
        }

        if($currentTrigger == $this->getDeleteButtonID()) {
            $this->handleDeletingFields($selectedRecords);
        }
        else {
            if (isset($_POST[$this->getRawTableFieldsName()])) {
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

                $itemsFields = [];
                $tableFieldsRaw = $_POST[$this->getRawTableFieldsName()];
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

    public function tableStyles(){}

    public function openHeaderTR(){
        echo '<tr style="';
        $this->headerTRStyles();
        echo '">';
    }

    public function openNormalTR($record){
        echo '<tr style="';
        $this->normalTRStyles($record);
        echo '">';
    }

    public function headerTRStyles(){}

    public function normalTRStyles($record){
        $this->headerTRStyles();
    }

    public function getTableFormName() : string {
        return $this->setKey . "_" . "table_form";
    }

    public function getRawTableFieldsName() : string {
        return $this->setKey . "_" . "table_fields";
    }

    public function getTableSelectorName() : string {
        return $this->setKey . "_" . "selector_field";
    }

    public function getJSArgsName() : string {
        return $this->setKey . "_" . "args";
    }

    public function getUpdateButtonID() : string {
        return $this->setKey . '_update';
    }

    public function getDeleteButtonID() : string {
        return $this->setKey . '_delete';
    }

    private static function closeTR(){
        echo '</td>';
    }

    public function getEntityRecords(): array {
        return $this->records;
    }

    public function recordsLimit() : int {
        return 5000;
    }
}
