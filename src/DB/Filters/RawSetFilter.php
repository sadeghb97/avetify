<?php
namespace Avetify\DB\Filters;

class RawSetFilter implements DBFilterInterface {
    public function __construct(public string $key, public string $value, public bool $isNumeric = false){}

    public function toRawQuery() : string {
        return "FIND_IN_SET(" . (!$this->isNumeric ? "'" : "") . $this->value . (!$this->isNumeric ? "'" : "") . ", " . $this->key . ")";
    }
}
