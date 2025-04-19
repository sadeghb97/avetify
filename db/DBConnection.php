<?php

abstract class DBConnection extends mysqli {
    public string $lastQuery = "";

    public abstract function getHost() : string;
    public abstract function getUser() : string;
    public abstract function getPassword() : string;
    public abstract function getDBName() : string;

    public function __construct(){
        $message = "Database is not available";
        $dbException = new Exception($message);
        try {
            parent::__construct($this->getHost(), $this->getUser(), $this->getPassword(), $this->getDBName());
            if (mysqli_connect_errno()) {
                throw $dbException;
            }
        }
        catch (Exception $ex){
            throw $dbException;
        }
    }

    /** @param DBFilter[] $filters
     * @return string
     */
    public function getFilteringQuery(array $filters) : string {
        if(count($filters) < 1) return "";

        $first = true;
        $out = "WHERE ";

        foreach ($filters as $filter){
            if($first) $first = false;
            else $out .= "AND ";

            $out .= ($filter->key . " ");
            $out .= ($filter->operator . " ");

            if(!$filter->isNumeric) $out .= "'";
            $out .= $filter->value;
            if(!$filter->isNumeric) $out .= "'";
            $out .= " ";
        }

        return $out;
    }

    public function query($sql, $queryName = null) : mysqli_result | bool {
        try { $result = parent::query($sql);}
        catch (Exception $ex){
            echo $sql . br();
        }
        $this->lastQuery = $sql;
        return $result;
    }

    public function fetchRow($query){
        $result = $this->query($query);
        if(!$result) return null;
        return mysqli_fetch_array($result, MYSQLI_ASSOC);
    }

    public function fetchSet($query){
        $result = $this->query($query);
        if(!$result) return [];

        $out = [];
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $out[] = $row;
        }
        return $out;
    }

    public function fetchMap($query, $mapKey){
        $result = $this->query($query);
        if(!$result) return [];

        $out = [];
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $out[$row[$mapKey]] = $row;
        }
        return $out;
    }

    public function fetchProperty($table, $idKey, $idValue, $property){
        $row = $this->fetchRow("SELECT $property FROM $table WHERE $idKey=$idValue");
        if(!$row) return null;
        return $row[$property];
    }

    public function fetchTable($tableName){
        return $this->fetchSet("SELECT * FROM $tableName");
    }
}