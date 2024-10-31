<?php

abstract class SetModifier {
    public array $currentRecords = [];

    public function __construct(public string $setKey){}

    public abstract function getEntityRecords() : array;

    /** @return SortFactor[] An array of MyClass instances */
    public abstract function finalSortFactors() : array;

    public function getSortKey() : string {
        return $this->setKey . "_sort";
    }

    public function getSortFactor() : SortFactor | null {
        if(!isset($_GET[$this->getSortKey()])) return null;
        $sortKey = $_GET[$this->getSortKey()];
        $allSortFactors = $this->finalSortFactors();

        foreach ($allSortFactors as $sf){
            if($sf->factorKey == $sortKey) return $sf;
        }

        return null;
    }

    private function sortRecords(){
        $sortFactor = $this->getSortFactor();
        if($sortFactor == null) return;
        usort($this->currentRecords, [$sortFactor, 'compare']);
    }

    private function filterRecords(){
        $this->currentRecords = $this->getEntityRecords();
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
        printLabel("Clear", Routing::currentPureLink(), $bg, $color);
        foreach ($allSortFactors as $sortFactor){
            printLabel($sortFactor->title, Routing::addParamToCurrentLink($this->getSortKey(),
                $sortFactor->factorKey), $bg, $color);
        }

        echo '</div>';
    }
}
