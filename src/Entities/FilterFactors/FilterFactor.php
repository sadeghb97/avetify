<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\DB\Filters\DBFilterInterface;
use Avetify\Fields\BaseRecordField;

//filter factor be surate mamul ui nadare. baraye dashtane ui bayad az filter field estefade beshe
class FilterFactor extends BaseRecordField implements Qualifier {
    public bool $useManualInterface = false;

    public function isQualified($item, $param): bool {
        return !!$this->getValue($item);
    }

    public function dbQualifyingFilter($param): DBFilterInterface | null {
        return null;
    }

    public function getFilterValue() {
        return $_REQUEST[$this->getElementIdentifier()] ?? null;
    }

    public function setNumeric() : FilterFactor {
        $this->isNumeric = true;
        return $this;
    }
}
