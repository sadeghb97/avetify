<?php

abstract class SortFactor {
    public function __construct(public string $title, public string $factorKey,
                                public bool $isDescending, public bool $isNumeric = true){
    }

    public function isQualified($item) : bool {
        $value = $this->getValue($item);
        if(!$value) return false;
        return true;
    }

    abstract public function getValue($item) : float | string;

    public function compare($itemA, $itemB) : int {
        $qa = $this->isQualified($itemA);
        $qb = $this->isQualified($itemB);
        if($qa != $qb) return $qa ? -1 : 1;

        $multiplier = $this->isDescending ? -1 : 1;
        $va = $this->getValue($itemA);
        $vb = $this->getValue($itemB);

        if(!$this->isNumeric){
            return $multiplier * strcmp($va, $vb);
        }
        if($va == $vb) return 0;
        return $multiplier * ($va > $vb ? 1 : -1);
    }
}

class SBTableSortField extends SortFactor {
    public function __construct(public SBTableField $field){
        parent::__construct($this->field->title, $this->field->key, !$this->field->isAscending,
            $this->field->isNumeric);
    }

    public function getValue($item): float|string {
        return $this->field->getValue($item);
    }
}
