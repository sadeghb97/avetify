<?php
namespace Avetify\Entities;

abstract class FilterFactor {
    public function __construct(public string $title, public string $key, public ValueGetter | null $getter = null){
        if($this->getter == null) $this->getter = EntityUtils::defaultValueGetter($this->key);
    }

    public abstract function isQualified($item, $param) : bool;
}

class BooleanFilterFactor extends FilterFactor {
    public function isQualified($item, $param): bool {
        return !!$this->getter->getValue($item);
    }
}
