<?php
namespace Avetify\Entities\Sorters;

class SimpleNumericSortFactor extends SimpleSortFactor {
    public function __construct(string $title, string $factorKey, bool $descIsDefault){
        parent::__construct($title, $factorKey, $descIsDefault, true);
    }
}
