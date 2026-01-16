<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\Fields\BaseRecordField;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Table\Fields\DateFields\RecentField;

class FilterField extends FilterFactor {
    public function __construct(public BaseRecordField $recordField){
        parent::__construct($this->recordField->key, $this->recordField->title);
    }

    public function isQualified($item, $param): bool {
        if(method_exists($this->recordField, "isQualified")){
            return $this->recordField->isQualified($item, $param);
        }

        return parent::isQualified($item, $param);
    }
}
