<?php
namespace Avetify\Entities\Sorters;

interface Sorter {
    public function compare($itemA, $itemB) : int;
}
