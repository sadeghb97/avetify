<?php
namespace Avetify\Entities\Sorters;

abstract class SortFactor implements Sorter {
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





