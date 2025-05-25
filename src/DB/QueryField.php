<?php
namespace Avetify\DB;

class QueryField {
    public function __construct(public $value, public bool $isNumeric, public string $key = ""){
    }
}
