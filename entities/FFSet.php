<?php

class FFSet {
    public $set = [];
    private $map = [];

    public function __construct($set){
        $this->set = $set;
        $this->refreshMap();
    }

    public function getItemId($item){
        return $item['id'];
    }

    public function refreshMap(){
        $this->map = [];
        foreach ($this->set as $index => $item){
            $this->map[$this->getItemId($item)] = $index;
        }
    }

    public function getItem($id){
        if(isset($this->map[$id])){
            return $this->set[$this->map[$id]];
        }
        return null;
    }

    public function sort($callable){
        usort($this->set, $callable);
        $this->refreshMap();
    }

    public function featuredSort(){
        $this->sort(function ($a, $b){
            if ($a->moviesPoints == $b->moviesPoints) return 0;
            return $b->moviesPoints > $a->moviesPoints ? 1 : -1;
        });
    }

    public function printSet(){
        printPreArray($this->set);
    }

    public function toAssociativeSet(){
        $out = [];
        foreach ($this->set as $item){
            $out[] = (array) $item;
        }
        return $out;
    }

    function makeGoodString($string){
        $out = str_replace('"',"", $string);
        $out = str_replace("'","", $out);
        $out = str_replace('&#39;',"", $out);
        $out = str_replace('&#34;',"", $out);
        return $out;
    }

    function getCloneSet() : array {
        return array_merge([], $this->set);
    }

    public function oneDimSet($key) : array {
        $out = [];
        foreach ($this->set as $item){
            if($key == 'avatar') $out[] = $item->getAvatarSrc();
            else if($key == "name") $out[] = $this->makeGoodString($item->name);
            else if($key == "id") $out[] = $this->getItemId($item);
        }
        return $out;
    }

    public function idsStrToNamesStr(string $idsStr, string $idsSeparator, string $namesSeparator) : string {
        if(!$idsStr) return "";

        $ids = explode($idsSeparator, $idsStr);
        $out = "";

        foreach ($ids as $id){
            if($out) $out .= $namesSeparator;
            $out .= $this->getItem($id)->name;
        }

        return $out;
    }
}
