<?php

abstract class SetModifier {
    public abstract function getEntityRecords() : array;

    public abstract function setEntityRecords(array $records);

    public abstract function getSortKey() : string;

    /** @return SortFactor[] An array of MyClass instances */
    public abstract function finalSortFactors() : array;

    public function getSortFactor() : SortFactor | null {
        if(!isset($_GET[$this->getSortKey()])) return null;
        $sortKey = $_GET[$this->getSortKey()];
        $allSortFactors = $this->finalSortFactors();

        foreach ($allSortFactors as $sf){
            if($sf->factorKey == $sortKey) return $sf;
        }

        return null;
    }

    public function sortRecords(){
        $sortFactor = $this->getSortFactor();
        if($sortFactor == null) return;
        $records = $this->getEntityRecords();
        usort($records, [$sortFactor, 'compare']);
        $this->setEntityRecords($records);
    }

    public function renderSortLabels(){
        $allSortFactors = $this->finalSortFactors();
        echo '<div style="text-align: center; margin-top: 2px;">';
        $bg = 'Black';
        $color = 'Cyan';
        printLabel("Clear", Routing::currentPureLink(), $bg, $color);
        foreach ($allSortFactors as $sortFactor){
            printLabel($sortFactor->title, Routing::addParamToCurrentLink($this->getSortKey(),
                $sortFactor->factorKey), $bg, $color);
        }

        echo '</div>';
    }
}
