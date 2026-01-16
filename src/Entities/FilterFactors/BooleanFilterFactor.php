<?php
namespace Avetify\Entities\FilterFactors;

class BooleanFilterFactor extends DiscreteFilterFactor {
    public function __construct(string $key, string $title) {
        parent::__construct($key, $title);
        $this->discreteFilters = [$title => 1];
    }
}
