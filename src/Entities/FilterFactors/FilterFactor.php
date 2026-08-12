<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\DB\Filters\DBFilterInterface;
use Avetify\Fields\BaseRecordField;
use Avetify\Interface\Cookies;

//filter factor be surate mamul ui nadare. baraye dashtane ui bayad az filter field estefade beshe
class FilterFactor extends BaseRecordField implements Qualifier {
    public bool $useManualInterface = false;

    public static function getStorageIdentifier(string $filterKey) : string {
        return "filters_" . $filterKey;
    }

    public function isQualified($item, $param): bool {
        return !!$this->getValue($item);
    }

    public function dbQualifyingFilter($paramValue): DBFilterInterface | null {
        return null;
    }

    public function getFilterValue() {
        $filterKey = $this->getElementIdentifier();
        $storageKey = self::getStorageIdentifier($filterKey);
        return $_REQUEST[$filterKey] ?? Cookies::getCookieValue($storageKey);
    }
}
