<?php
namespace Avetify\Entities\FilterFactors;

class BooleanFilterFactor extends FilterFactor {
    public function isQualified($item, $param): bool {
        return !!$this->getter->getValue($item);
    }
}
