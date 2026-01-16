<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\Fields\BaseRecordField;
use Avetify\Interface\IdentifiedElementTrait;

abstract class FilterFactor extends BaseRecordField {
    use IdentifiedElementTrait;

    public function __construct(string $title, string $key, public string $namespace = ""){
        parent::__construct($key, $title);
    }

    public function isQualified($item, $param): bool {
        return !!$this->getValue($item);
    }

    public function getElementIdentifier($item = null){
        return $this->namespace ? $this->namespace . "_filter_" . $this->key : "filter_" . $this->key;
    }
}
