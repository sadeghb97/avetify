<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\DB\Filters\DBFilter;
use Avetify\DB\Filters\DBFilterInterface;

class SimpleDBOperatorFilter extends FilterFactor {
    public function __construct(string $key, string $title, public string $operator, bool $isNumeric) {
        parent::__construct($key, $title);
        $this->isNumeric = $isNumeric;
    }

    public function dbQualifyingFilter($paramValue): DBFilterInterface|null {
        return new DBFilter($this->key, $this->operator, $paramValue, $this->isNumeric);
    }
}
