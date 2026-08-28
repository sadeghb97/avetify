<?php
namespace Avetify\DB\Filters;

class RawDBFilter implements DBFilterInterface {
    public function __construct(public string $rawQuery){
    }

    public function toRawQuery() : string {
        return $this->rawQuery;
    }
}
