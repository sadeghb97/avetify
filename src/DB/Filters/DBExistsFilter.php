<?php
namespace Avetify\DB\Filters;

class DBExistsFilter implements DBFilterInterface {
    public function __construct(public string $key, public string $value, public bool $isNumeric = false){}

    public function toRawQuery() : string {
        $vc = (!$this->isNumeric ? "'" : "") . $this->value . (!$this->isNumeric ? "'" : "");
        return $vc . " IN (" . $this->key . ")";
    }
}
