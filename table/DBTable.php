<?php

abstract class DBTable extends SBTable {
    public function __construct(public DBConnection $conn, public string $dbTableName, public string $primaryKey,
                                array $fields, string $key){

        $idGetter = new SimpleIDGetter($this->primaryKey);
        parent::__construct($fields, $this->fetchDBRecords(), $key, true, $idGetter);
    }

    public function getItemId($item): string {
        return $item->pk;
    }

    public function handleSubmittedFields($itemsFields) {
        $queryBuilder = new QueryBuilder($this->conn, $this->dbTableName);
        $indexesMap = $this->getCurrentRecordsIndexes();

        $queryDone = false;
        foreach ($itemsFields as $itemPk => $itemFields){
            $queryBuilder->clear();
            $queryRequired = false;
            $oldRecord = $this->currentRecords[$indexesMap[$itemPk]];

            foreach ($itemFields as $fk => $fv){
                $queryBuilder->addField($fv, false, $fk);
                if(EntityUtils::getSimpleValue($oldRecord, $fk) != $fv) $queryRequired = true;
            }

            if($queryRequired) {
                $sql = $queryBuilder->createUpdate(new QueryField($itemPk, true, "pk"));
                echo $sql . br();
                $queryDone = true;
            }
        }

        if($queryDone) $this->updateRecords();
    }

    public function handleDeletingFields($deletingFields) {
        if(count($deletingFields) > 0) {
            $queryBuilder = new QueryBuilder($this->conn, $this->dbTableName);

            foreach ($deletingFields as $itemPk) {
                $sql = $queryBuilder->createDelete(new QueryField($itemPk, true, "pk"));
                echo $sql . br();
            }
            $this->updateRecords();
        }
    }

    public function handleCreatingFields($creatingFields) {
        printPreArray($creatingFields);
    }

    public function updateRecords (){
        $this->loadRawRecords($this->fetchDBRecords());
    }

    abstract public function fetchDBRecords() : array;
}
