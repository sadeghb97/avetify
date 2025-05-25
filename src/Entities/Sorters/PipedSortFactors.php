<?php
namespace Avetify\Entities\Sorters;

class PipedSortFactors extends SortFactor {
    /** @var SortFactor[] */
    public array $sortFactors = [];

    public function __construct(array $sortFactors){
        $this->sortFactors = $sortFactors;
        $mainFactor = $sortFactors[0];
        parent::__construct($mainFactor->title, $mainFactor->factorKey,
            $mainFactor->isDescending, $mainFactor->isNumeric);
    }

    public function getValue($item): float | string {
        return $this->sortFactors[0]->getValue($item);
    }

    public function compare($itemA, $itemB): int {
        foreach ($this->sortFactors as $sortFactor){
            $comp = $sortFactor->compare($itemA, $itemB);
            if($comp != 0) return $comp;
        }
        return 0;
    }
}
