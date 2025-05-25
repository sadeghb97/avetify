<?php
namespace Avetify\Entities\Sorters;

use Avetify\Entities\EntityUtils;

class SimpleSortFactor extends SortFactor {
    public function getValue($item): float | string {
        return EntityUtils::getSimpleValue($item, $this->factorKey);
    }
}
