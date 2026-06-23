<?php
namespace Avetify\DB;

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
            $sql .= $this->generateDBValueStatement($field);
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
            $sql .= $this->generateDBValueStatement($field);
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
        $sql .= $this->generateDBValueStatement($primaryField);
        $sql .= " ";
        return $sql;
    }

    public function generateDBValueStatement(QueryField $field) : string {
        $nullValue = $field->value === null;
        $needQuote = !$field->isNumeric && !$nullValue;
        $emptyValue = $nullValue ? "NULL" : ($field->isNumeric ? 0 : "");

        $out = "";
        if($needQuote) $out .= "'";
        if($field->isNumeric) $out .= ($field->value ?: $emptyValue);
        else $out .= ($field->value ? $this->conn->real_escape_string($field->value) : $emptyValue);
        if($needQuote) $out .= "'";

        return $out;
    }
}
