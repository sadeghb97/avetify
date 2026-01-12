<?php
namespace Avetify\DB;

class DBFilter implements DBFilterInterface {
    public function __construct(public string $key, public string $operator,
                                public string $value, public bool $isNumeric = false){
    }

    public function toRawQuery() : string {
        return $this->key . " " . $this->operator . " " . (!$this->isNumeric ? "'" : "") . $this->value . (!$this->isNumeric ? "'" : "");
    }
}
