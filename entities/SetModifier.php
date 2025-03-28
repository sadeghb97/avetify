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
        $sortFactorKey = $_GET[$this->getSortKey()];
        $startWithMinus = str_starts_with($sortFactorKey, "-");
        $pureSortFactorKey = $startWithMinus ? substr($sortFactorKey, 1) : $sortFactorKey;
        $allSortFactors = $this->finalSortFactors();

        foreach ($allSortFactors as $sf){
            if($sf->factorKey == $pureSortFactorKey){
                $nextIsDescending = $startWithMinus;
                if($sf->isDescending() != $nextIsDescending) $sf->toggleDirection();
                return $sf;
            }
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
        $defaultBg = 'Black';
        $defaultColor = 'Cyan';
        $alterBg = 'Black';
        $alterColor = 'GoldenRod';
        printLabel("Clear", Routing::removeParamFromCurrentLink($this->getSortKey()), $defaultBg, $defaultColor);

        $currentSort = isset($_GET[$this->getSortKey()]) ? $_GET[$this->getSortKey()] : null;
        $startWithMinus = $currentSort ? str_starts_with($currentSort, "-") : false;
        $pureSort = null;
        if($currentSort != null){
            $pureSort = $startWithMinus ? substr($currentSort, 1) : $currentSort;
        }

        foreach ($allSortFactors as $sortFactor){
            $finalBg = $defaultBg;
            $finalColor = $defaultColor;
            $finalTitle = $sortFactor->title;
            $nextDescending = $sortFactor->descIsDefault;
            if($currentSort && $pureSort == $sortFactor->factorKey){
                $nextDescending = !$startWithMinus;
                $finalBg = $alterBg;
                $finalColor = $alterColor;
                $finalTitle .= ($startWithMinus ? " ↓" : " ↑");
            }
            $finalSortFactor = ($nextDescending ? "-" : "") . $sortFactor->factorKey;

            printLabel($finalTitle, Routing::addParamToCurrentLink($this->getSortKey(),
                $finalSortFactor), $finalBg, $finalColor);
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
