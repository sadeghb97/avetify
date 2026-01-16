<?php
namespace Avetify\Entities\FilterFactors;

class BooleanFilterFactor extends DiscreteFilterFactor {
    public function __construct(string $title, string $key) {
        parent::__construct($title, $key);
        $this->discreteFilters = [$title => 1];
    }
}
