<?php

class JSONTable extends SBTable {
    public function __construct(array $fields, public string $filename, string $key,
                                bool $isEditable = false,
                                ?IDGetter $idGetter = null){

        parent::__construct($fields, [], $key, $isEditable, $idGetter);
        $this->loadRawRecords($this->fetchRecordsFromJsonFile());
    }

    public function fetchRecordsFromJsonFile() : array {
        $rawRecords = [];
        if(file_exists($this->filename)){
            $tempObject = json_decode(file_get_contents($this->filename), true);
            if(is_array($tempObject)) $rawRecords = $tempObject;
        }
        return $rawRecords;
    }

    public function handleCreatingFields($creatingFields) {
        printPreArray($creatingFields, "CRF");
    }

    public function handleSubmittedFields($itemsFields) {
        printPreArray($itemsFields, "IF");
    }

    public function handleDeletingFields($deletingFields) {
        printPreArray($deletingFields, "DF");
    }
}
