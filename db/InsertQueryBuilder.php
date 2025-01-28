<?php

class InsertQueryBuilder {
    /** @var QueryField[]  */
    public array $fields = [];

    public function __construct(public DBConnection $conn, public string $table,
                                public bool $ignoreMode, public bool $includeKeys = false){
    }

    public function addField($value, bool $isNumeric, string $key = ""){
        $this->fields[] = new QueryField($value, $isNumeric, $key);
    }

    public function clear(){
        $this->fields = [];
    }

    public function create() : string {
        $sql = "INSERT " .
            ($this->ignoreMode ? "IGNORE " : "") .
            ("INTO " . $this->table . " ");

        if($this->includeKeys){
            $isFirst = true;
            $sql .= "(";
            foreach ($this->fields as $field){
                if($isFirst) $isFirst = false;
                else $sql .= ", ";
                $sql .= $field->key;
            }
            $sql .= ") ";
        }

        $isFirst = true;
        $sql .= "VALUES (";
        foreach ($this->fields as $field){
            if($isFirst) $isFirst = false;
            else $sql .= ", ";
            if(!$field->isNumeric) $sql .= "'";
            if($field->isNumeric) $sql .= $field->value;
            else $sql .= ($field->value ? $this->conn->real_escape_string($field->value) : "");
            if(!$field->isNumeric) $sql .= "'";
        }
        $sql .= ")";

        return $sql;
    }
}

class QueryField {
    public function __construct(public $value, public bool $isNumeric, public string $key = ""){
    }
}
