<?php

abstract class SetModifier {
    public array $currentRecords = [];

    public function __construct(public string $setKey){}

    public abstract function getEntityRecords() : array;

    public function getItemId($item) : string {
        return EntityUtils::getSimpleValue($item, "id");
    }

    public function getItemName($item) : string {
        return EntityUtils::getSimpleValue($item, "name");
    }

    /** @return SortFactor[] An array of MyClass instances */
    public function finalSortFactors() : array {
        return [];
    }

    /** @return FilterFactor[] An array of MyClass instances */
    public function finalFilterFactors() : array {
        return [];
    }

    public function getSortKey() : string {
        return $this->setKey . "_sort";
    }

    public function getFilterKey($factorKey) : string {
        return $this->setKey . "_" . $factorKey;
    }

    public function getDefaultFactor() : SortFactor | null {
        return null;
    }

    public function getSortFactor() : SortFactor | null {
        if(!isset($_GET[$this->getSortKey()])) return $this->getDefaultFactor();
        $sortKey = $_GET[$this->getSortKey()];
        $allSortFactors = $this->finalSortFactors();

        foreach ($allSortFactors as $sf){
            if($sf->factorKey == $sortKey) return $sf;
        }

        return $this->getDefaultFactor();
    }

    private function sortRecords(){
        $sortFactor = $this->getSortFactor();
        if($sortFactor == null) return;
        usort($this->currentRecords, [$sortFactor, 'compare']);
    }

    public function simpleSort(string $key, bool $isAsc = true){
        EntityUtils::simpleSort($this->currentRecords, $key, $isAsc);
    }

    private function filterRecords(){
        $this->currentRecords = [];
        foreach ($this->getEntityRecords() as $record){
            $isQualified = true;

            foreach ($this->finalFilterFactors() as $filterFactor){
                $filterKey = $this->getFilterKey($filterFactor->key);
                if(isset($_GET[$filterKey]) && !$filterFactor->isQualified($record, $_GET[$filterKey])){
                    $isQualified = false;
                    break;
                }
            }

            if($isQualified) $this->currentRecords[] = $record;
        }
    }

    public function adjustRecords(){
        $this->filterRecords();
        $this->sortRecords();
    }

    public function renderSortLabels(){
        $allSortFactors = $this->finalSortFactors();
        echo '<div style="text-align: center; margin-top: 2px;">';
        $bg = 'Black';
        $color = 'Cyan';
        printLabel("Clear", Routing::removeParamFromCurrentLink($this->getSortKey()), $bg, $color);
        foreach ($allSortFactors as $sortFactor){
            printLabel($sortFactor->title, Routing::addParamToCurrentLink($this->getSortKey(),
                $sortFactor->factorKey), $bg, $color);
        }

        echo '</div>';
    }

    public function getCurrentRecordsIndexes() : array {
        $map = [];
        foreach ($this->currentRecords as $index => $record){
            $id = $this->getItemId($record);
            $map[$id] = $index;
        }
        return $map;
    }
}
