<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\Fields\BaseRecordField;

abstract class FilterFactor extends BaseRecordField implements Qualifier {
    public function isQualified($item, $param): bool {
        return !!$this->getValue($item);
    }
}
