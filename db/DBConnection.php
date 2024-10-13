<?php

abstract class DBConnection extends mysqli {
    public string $lastQuery = "";

    public abstract function getHost() : string;
    public abstract function getUser() : string;
    public abstract function getPassword() : string;
    public abstract function getDBName() : string;

    public function __construct(){
        fparent::__construct($this->getHost(), $this->getUser(), $this->getPassword(), $this->getDBName());
        if (mysqli_connect_errno()){
            echo "Failed to connect to MySQL";
        }
    }

    public function query($sql, $queryName = null) : mysqli_result | bool {
        $result = parent::query($sql);
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

    public function fetchTable($tableName){
        return $this->fetchSet("SELECT * FROM $tableName");
    }

    public function getQueendom($queendomId) : Queendom {
        $qd = $this->fetchRow("SELECT * FROM queendom WHERE id=$queendomId");
        $qdMap = $this->fetchMap("SELECT * FROM qdrel WHERE qd_id=$queendomId", "lady_id");
        return new Queendom($qd, $qdMap);
    }
}