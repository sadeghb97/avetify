<?php

abstract class SortFactor {
    public bool $alterDirection = false;

    public function __construct(public string $title, public string $factorKey,
                                public bool $descIsDefault,
                                public bool $isNumeric = true,
                                public bool $skipEmpties = false){
    }

    public function toggleDirection(){
        $this->alterDirection = !$this->alterDirection;
    }

    public function isDescending() : bool {
        $isDescending = $this->descIsDefault;
        if($this->alterDirection) $isDescending = !$isDescending;
        return $isDescending;
    }

    public function isQualified($item) : bool {
        if(!$this->skipEmpties) return true;
        $value = $this->getValue($item);
        if(!$value) return false;
        return true;
    }

    abstract public function getValue($item) : float | string;

    public function compare($itemA, $itemB) : int {
        $isDescending = $this->isDescending();
        $qa = $this->isQualified($itemA);
        $qb = $this->isQualified($itemB);
        if($qa != $qb) return $qa ? -1 : 1;

        $multiplier = $isDescending ? -1 : 1;
        $va = $this->getValue($itemA);
        $vb = $this->getValue($itemB);

        if(!$this->isNumeric){
            return $multiplier * strcmp($va, $vb);
        }
        if($va == $vb) return 0;
        return $multiplier * ($va > $vb ? 1 : -1);
    }
}

class SimpleSortFactor extends SortFactor {
    public function getValue($item): float | string {
        return EntityUtils::getSimpleValue($item, $this->factorKey);
    }
}

class SimpleNumericSortFactor extends SimpleSortFactor {
    public function __construct(string $title, string $factorKey, bool $descIsDefault){
        parent::__construct($title, $factorKey, $descIsDefault, true);
    }
}

class SimpleTextSortFactor extends SimpleSortFactor {
    public function __construct(string $title, string $factorKey, bool $descIsDefault){
        parent::__construct($title, $factorKey, $descIsDefault, false);
    }
}

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