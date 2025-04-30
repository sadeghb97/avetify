<?php

class DBTable extends SBTable {
    public bool $pkIsNumeric = true;

    public function __construct(public DBConnection $conn, public string $dbTableName, public string $primaryKey,
                                array $fields, string $key){

        parent::__construct($fields, $this->fetchDBRecords(), $key, true);
    }

    public function getItemId($item): string {
        return EntityUtils::getSimpleValue($item, $this->primaryKey);
    }

    public function handleSubmittedFields($itemsFields) {
        $queryBuilder = new QueryBuilder($this->conn, $this->dbTableName);
        $indexesMap = $this->getCurrentRecordsIndexes();
        $fieldsMap = $this->getFieldsMap();

        $titlePrinter = new Printer(color: "#1e8449");
        $messagePrinter = new Printer();
        $queryDone = false;
        foreach ($itemsFields as $itemPk => $itemFields){
            $queryBuilder->clear();
            $queryRequired = false;
            $oldRecord = $this->currentRecords[$indexesMap[$itemPk]];

            foreach ($itemFields as $fk => $fv){
                $qr = false;
                $fieldDetails = $fieldsMap[$fk];
                $isNumericField = $fieldDetails && $fieldDetails->isNumeric;

                $oldValue = EntityUtils::getSimpleValue($oldRecord, $fk);
                if(!$isNumericField){
                    $qr = $oldValue !== $fv;
                }
                else {
                    if(!$fv) $fv = 0;
                    if(!$oldValue) $oldValue = 0;
                    if(!$fv) $qr = $fv !== $oldValue;
                    else $qr = $fv != $oldValue;
                    if(!$fv) $fv = "0";
                }
                $queryBuilder->addField($fv, $isNumericField, $fk);
                if($qr) $queryRequired = true;
            }

            if($queryRequired) {
                $sql = $queryBuilder->createUpdate(new QueryField($itemPk, $this->pkIsNumeric, $this->primaryKey));
                if($this->conn->query($sql)) {
                    $titlePrinter->print($this->getItemTitle($oldRecord));
                    $messagePrinter->print(": Updated" . br());
                    $queryDone = true;
                }
            }
        }

        if($queryDone){
            $this->updateRecords();
        }
    }

    public function handleDeletingFields($deletingFields) {
        if(count($deletingFields) > 0) {
            $queryBuilder = new QueryBuilder($this->conn, $this->dbTableName);
            $indexesMap = $this->getCurrentRecordsIndexes();

            $titlePrinter = new Printer(color: "#c0392b");
            $messagePrinter = new Printer();

            foreach ($deletingFields as $itemPk) {
                $oldRecord = $this->currentRecords[$indexesMap[$itemPk]];
                $sql = $queryBuilder->createDelete(new QueryField($itemPk, $this->pkIsNumeric, $this->primaryKey));

                if($this->conn->query($sql)) {
                    if($oldRecord instanceof SBEntityItem){
                        $oldRecord->deleteAllResources();
                    }
                    $titlePrinter->print($this->getItemTitle($oldRecord));
                    $messagePrinter->print(": Deleted" . br());
                }
            }

            $this->updateRecords();
            endline();
        }
    }

    public function handleCreatingFields($creatingFields) {
        $isEnoughToInsert = true;
        foreach ($this->fields as $field){
            if($field->onCreateField != null && $field->onCreateField->requiredOnCreate){
               if(empty($creatingFields[$field->onCreateField->key])) {
                   $isEnoughToInsert = false;
                   break;
               }
            }
        }

        if($isEnoughToInsert){
            $fieldsMap = $this->getFieldsMap();
            $queryBuilder = new QueryBuilder($this->conn, $this->dbTableName);
            $titlePrinter = new Printer(fontWeight: "bold", color: "#1e8449");
            $messagePrinter = new Printer();

            foreach ($creatingFields as $key => $value){
                $fieldDetails = $fieldsMap[$key];
                $isNumericField = $fieldDetails && $fieldDetails->isNumeric;
                $queryBuilder->addField($value, $isNumericField, $key);
            }

            $sql = $queryBuilder->createInsert(true);
            if($this->conn->query($sql)) {
                $titlePrinter->print($this->getItemTitle($creatingFields));
                $messagePrinter->print(": Inserted" . br());

                $this->updateRecords();
                endline();
            }
        }
    }

    public function updateRecords (){
        $this->loadRawRecords($this->fetchDBRecords());
    }

    public function fetchDBRecords() : array {
        return $this->conn->fetchSet("SELECT * FROM {$this->dbTableName}");
    }
}
