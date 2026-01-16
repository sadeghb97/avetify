<?php
namespace Avetify\Entities\Sorters;

use Avetify\Entities\EntityUtils;

abstract class SortFactor implements Sorter {
    public bool $alterDirection = false;
    public bool $isDefaultSort = false;
    public array $tieBreaks = [];

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

    public function sortQualified($item) : bool {
        if(!$this->skipEmpties) return true;
        $value = $this->getValue($item);
        if(!$value) return false;
        return true;
    }

    public function setTiebreaks(array $tieBreaks) : SortFactor {
        $this->tieBreaks = $tieBreaks;
        return $this;
    }

    abstract public function getValue($item) : float | string;

    public static function baseCompare($aValue, $bValue, bool $isNumeric, bool $isDescending): int {
        if($aValue == $bValue) return 0;

        $multiplier = $isDescending ? -1 : 1;
        if(!$isNumeric) return $multiplier * strcmp($aValue, $bValue);
        return $multiplier * ($aValue > $bValue ? 1 : -1);
    }

    public function compare($itemA, $itemB) : int {
        $qa = $this->sortQualified($itemA);
        $qb = $this->sortQualified($itemB);
        if($qa != $qb) return $qa ? -1 : 1;

        $va = $this->getValue($itemA);
        $vb = $this->getValue($itemB);

        $res = self::baseCompare($va, $vb, $this->isNumeric, $this->isDescending());
        if($res != 0) return $res;

        foreach ($this->tieBreaks as $tieBreak){
            $skipEmpties = false;
            $tbDesc = false;
            $tbNumeric = false;

            if(str_starts_with($tieBreak, "+")){
                $tieBreak = substr($tieBreak, 1);
                $skipEmpties = true;
            }

            if(str_starts_with($tieBreak, "-")){
                $tieBreak = substr($tieBreak, 1);
                $tbDesc = true;
            }

            if(str_starts_with($tieBreak, "#")){
                $tieBreak = substr($tieBreak, 1);
                $tbNumeric = true;
            }

            $aValue = EntityUtils::getSimpleValue($itemA, $tieBreak);
            $bValue = EntityUtils::getSimpleValue($itemB, $tieBreak);

            if($aValue != $bValue) {
                if($skipEmpties) {
                    if (!$aValue) return 1;
                    if (!$bValue) return -1;
                }
                return self::baseCompare($aValue, $bValue, $tbNumeric, $tbDesc);
            }
        }

        return 0;
    }
}





