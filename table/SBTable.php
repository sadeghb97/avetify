<?php

class SBTable extends SetModifier {
    public array $records;

    /** @var SBTableField[] $fields */
    public array $fields;
    public bool $printRowIndex = true;
    public bool $enableCreatingRow = false;

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
                    $field->onCreateField->useIDIdentifier = false;
                    $field->onCreateField->useNameIdentifier = true;
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

            JSInterface::declareGlobalJSArgs($this->getJSArgsName());
            FormUtils::readyFormToCatchNoNamedFields(
                $this->getJSArgsName(),
                $this->getTableFormName(),
                $this->getRawTableFieldsName(),
                $allJSEditableFields,
                $this->isEditable
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
        self::closeTR();
    }

    public function renderTable(){
        if($this->isEditable) echo '<form method="post" id="' . $this->getTableFormName() .
            '" name="' . $this->getTableFormName() . '">';
        echo '<div class="tables_panel">';
        echo '<table class="table" style="';
        $this->tableStyles();
        echo '"';
        echo ' >';

        $this->openHeaderTR();
        if($this->printRowIndex) SBTableField::renderIndexTH("Row");
        foreach ($this->fields as $field){
            $field->renderHeaderTH();
        }
        self::closeTR();

        $recIndex = 1;
        foreach ($this->currentRecords as $record){
            $this->openNormalTR($record);
            if($this->printRowIndex) SBTableField::renderIndexTD($recIndex);
            $recIndex++;
            foreach ($this->fields as $field){
                $field->renderRecord($record);
            }
            self::closeTR();
        }
        if($this->enableCreatingRow) $this->renderCreatingTr();

        echo '</table>';
        echo '</div>';
        if($this->isEditable) {
            echo '<input type="hidden" id="' . $this->getRawTableFieldsName() .
                '" name="' . $this->getRawTableFieldsName() . '">';
            echo '<button type="submit" class="btn btn-primary">Update</button>';
            echo '</form>';
        }
        $this->placeJSUtils();
    }

    public function renderPage(string $title){
        $theme = $this->getTheme();
        $theme->placeHeader($title);
        if($this->isEditable) $this->catchSubmittedFields();
        $this->renderSortLabels();
        $this->renderTable();
    }

    public function getTheme() : ThemesManager {
        return new GreenTheme();
    }

    private function catchSubmittedFields(){
        if(isset($_POST[$this->getRawTableFieldsName()])){
            $creatingFields = [];
            foreach ($this->fields as $field){
                if($field->onCreateField != null){
                    $crFieldKey = $field->onCreateField->getEditableFieldIdentifier(null);
                    $crKey = $field->onCreateField->key;
                    $creatingFields[$crKey] = $_POST[$crFieldKey];
                }
            }
            $this->handleCreatingFields($creatingFields);

            $itemsFields = [];
            $tableFieldsRaw = $_POST[$this->getRawTableFieldsName()];
            $tableFieldsList = json_decode($tableFieldsRaw, true);

            foreach ($tableFieldsList as $tableFieldRawKey => $tableFieldValue){
                $lastPos = strrpos($tableFieldRawKey, "_");
                if(strlen($tableFieldRawKey) > ($lastPos + 1)){
                    $itemId = substr($tableFieldRawKey, $lastPos + 1);
                    $remains = substr($tableFieldRawKey, 0, $lastPos);

                    if(strlen($remains) > (strlen($this->setKey) + 1)) {
                        $itemFieldKey = substr($remains, (strlen($this->setKey) + 1));
                        if (!isset($itemsFields[$itemId])) $itemsFields[$itemId] = [];
                        $itemsFields[$itemId][$itemFieldKey] = $tableFieldValue;
                    }
                }
            }

            $this->handleSubmittedFields($itemsFields);
        }
    }

    public function handleSubmittedFields($itemsFields){}

    public function handleCreatingFields($creatingFields){}

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

    public function getJSArgsName() : string {
        return $this->setKey . "_" . "args";
    }

    private static function closeTR(){
        echo '</td>';
    }

    public function getEntityRecords(): array {
        return $this->records;
    }
}
