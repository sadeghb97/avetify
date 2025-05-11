<?php

abstract class DBLister extends SBLister {
    public function __construct(public DBConnection $conn,
                                public string $tableName,
                                public string $dbListKey,
                                public string $dbPriorityKey,
                                public string $dbPrimaryKey = "pk",
                                public bool $pkIsNumeric = true
    ) {
        parent::__construct($this->fetchAllItems());
    }

    abstract public function fetchAllItems() : array;

    public function updateItems(){
        $this->setItems($this->fetchAllItems());
    }

    public function dbHandleLists(array $lists){
        foreach ($lists as $listIndex => $list){
            foreach ($list as $priority => $itemPk){
                $updatedValue = $this->listIndexToNewOrgPk($listIndex);
                $sql = "UPDATE " . $this->tableName . " SET " . $this->dbListKey . "=" . $updatedValue;
                if($this->dbPriorityKey) $sql .= (", " . $this->dbPriorityKey . "=" . $priority);
                $sql .= " WHERE " . $this->dbPrimaryKey . "=";
                if($this->pkIsNumeric) $sql .= "'";
                $sql .= $itemPk;
                if($this->pkIsNumeric) $sql .= "'";

                if(!$this->conn->query($sql)){
                    Printer::warningPrint("error: " . $this->conn->error);
                }
            }
        }
    }

    public function handleSubmittedList(array $lists, array $itemsParams, $allFields) {
        $this->dbHandleLists($lists);
        $this->updateItems();
    }
}
