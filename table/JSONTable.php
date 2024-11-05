<?php

class JSONTable extends SBTable {
    public function __construct(array $fields, public string $filename, string $key,
                                bool $isEditable = false,
                                ?IDGetter $idGetter = null){

        $rawRecords = [];
        if(file_exists($this->filename)){
            $tempObject = json_decode(file_get_contents($this->filename), true);
            if(is_array($tempObject)) $rawRecords = $tempObject;
        }

        parent::__construct($fields, $rawRecords, $key, $isEditable, $idGetter);
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
