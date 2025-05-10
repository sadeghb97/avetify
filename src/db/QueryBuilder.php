<?php

class QueryBuilder {
    /** @var QueryField[]  */
    public array $fields = [];

    public function __construct(public DBConnection $conn, public string $table,
                                public bool $ignoreMode = true){
    }

    public function addField($value, bool $isNumeric, string $key = ""){
        $this->fields[] = new QueryField($value, $isNumeric, $key);
    }

    public function clear(){
        $this->fields = [];
    }

    public function createInsert($includeKeys = false) : string {
        $sql = "INSERT " .
            ($this->ignoreMode ? "IGNORE " : "") .
            ("INTO " . $this->table . " ");

        if($includeKeys){
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
            if($field->isNumeric) $sql .= ($field->value ? $field->value : 0);
            else $sql .= ($field->value ? $this->conn->real_escape_string($field->value) : "");
            if(!$field->isNumeric) $sql .= "'";
        }
        $sql .= ")";

        return $sql;
    }

    public function createUpdate(QueryField $primaryField) : string {
        $sql = "UPDATE " . ($this->table) . " SET ";

        $isFirst = true;
        foreach ($this->fields as $field){
            if($isFirst) $isFirst = false;
            else $sql .= ", ";
            $sql .= $field->key;
            $sql .= "=";
            if(!$field->isNumeric) $sql .= "'";
            if($field->isNumeric) $sql .= ($field->value ? $field->value : 0);
            else $sql .= ($field->value ? $this->conn->real_escape_string($field->value) : "");
            if(!$field->isNumeric) $sql .= "'";
            $sql .= " ";
        }
        $sql .= $this->createPrimaryWhere($primaryField);
        return $sql;
    }

    public function createDelete(QueryField $primaryField) : string {
        $sql = "DELETE FROM " . ($this->table) . " ";
        $sql .= $this->createPrimaryWhere($primaryField);
        return $sql;
    }

    public function createPrimaryWhere(QueryField $primaryField) : string {
        $sql = "WHERE ";
        $sql .= ($primaryField->key . "=");
        if(!$primaryField->isNumeric) $sql .= "'";
        if($primaryField->isNumeric) $sql .= ($primaryField->value ? $primaryField->value : 0);
        else $sql .= ($primaryField->value ? $this->conn->real_escape_string($primaryField->value) : "");
        if(!$primaryField->isNumeric) $sql .= "'";
        $sql .= " ";
        return $sql;
    }
}

class QueryField {
    public function __construct(public $value, public bool $isNumeric, public string $key = ""){
    }
}
