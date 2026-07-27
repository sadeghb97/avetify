<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;

abstract class DiscreteFilterFactor extends FilterFactor implements IdentifiedElement {
    use IdentifiedElementTrait;
    public array $discreteFilters = [];

    public function __construct(string $key, string $title, string $namespace = ""){
        parent::__construct($key, $title);
        $this->namespace = $namespace;
    }

    public function addDiscreteFilter($filterTitle, $filterValue): DiscreteFilterFactor {
        $this->discreteFilters[$filterTitle] = $filterValue;
        return $this;
    }

    public function getElementIdentifier($item = null){
        return $this->namespace ? $this->namespace . "_filter_" . $this->key : "filter_" . $this->key;
    }
}
