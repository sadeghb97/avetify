<?php
namespace Avetify\Entities\FilterFactors;

interface Qualifier {
    public function isQualified($item, $param): bool;
}
