<?php
namespace Avetify\DB;

interface DBFilterInterface{
    public function toRawQuery() : string;
}
