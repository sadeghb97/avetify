<?php
namespace Avetify\Table;

use Avetify\Interface\Pout;

class JSONTable extends AvtTable {
    public function __construct(array $fields, public string $filename, string $key,
                                bool $isEditable = false){

        parent::__construct($fields, [], $key, $isEditable);
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
        Pout::printPreArray($creatingFields, "CRF");
    }

    public function handleSubmittedFields($itemsFields) {
        Pout::printPreArray($itemsFields, "IF");
    }

    public function handleDeletingFields($deletingFields) {
        Pout::printPreArray($deletingFields, "DF");
    }
}
