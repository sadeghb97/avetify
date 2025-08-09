<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\Entities\EntityUtils;

abstract class FilterFactor {
    public array $discreteFilters = [];

    public function __construct(public string $title, public string $key){}

    public function getItemRelatedValue($item) {
        return EntityUtils::getSimpleValue($item, $this->key);
    }

    public function isQualified($item, $param): bool {
        return !!$this->getItemRelatedValue($item);
    }

    public function addDiscreteFilter($filterTitle, $filterValue): FilterFactor {
        $this->discreteFilters[$filterTitle] = $filterValue;
        return $this;
    }
}
