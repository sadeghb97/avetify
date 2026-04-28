<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\DB\Filters\DBFilterInterface;
use Avetify\Fields\BaseRecordField;

class FilterFactor extends BaseRecordField implements Qualifier {
    public function isQualified($item, $param): bool {
        return !!$this->getValue($item);
    }

    public function dbQualifyingFilter($param): DBFilterInterface | null {
        return null;
    }
}
