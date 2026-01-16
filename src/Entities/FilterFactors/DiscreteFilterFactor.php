<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\Fields\BaseRecordField;
use Avetify\Interface\IdentifiedElementTrait;

abstract class DiscreteFilterFactor extends FilterFactor {
    use IdentifiedElementTrait;
    public array $discreteFilters = [];

    public function addDiscreteFilter($filterTitle, $filterValue): DiscreteFilterFactor {
        $this->discreteFilters[$filterTitle] = $filterValue;
        return $this;
    }
}
