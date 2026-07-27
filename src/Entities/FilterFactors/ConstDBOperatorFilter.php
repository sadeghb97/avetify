<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\DB\Filters\DBFilter;
use Avetify\DB\Filters\DBFilterInterface;

class ConstDBOperatorFilter extends SimpleDBOperatorFilter {
    public function __construct(string $key, string $title, string $operator, bool $isNumeric, public $filterValue) {
        parent::__construct($key, $title, $operator, $isNumeric);
    }

    public function getFilterValue() {
        return $this->filterValue;
    }
}