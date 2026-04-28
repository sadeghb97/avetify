<?php
namespace Avetify\DB\Filters;

interface DBFilterInterface {
    public function toRawQuery() : string;
}
