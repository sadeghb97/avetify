<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\Entities\EntityUtils;
use Avetify\Entities\ValueGetter;

abstract class FilterFactor {
    public function __construct(public string $title, public string $key, public ValueGetter | null $getter = null){
        if($this->getter == null) $this->getter = EntityUtils::defaultValueGetter($this->key);
    }

    public abstract function isQualified($item, $param) : bool;
}
