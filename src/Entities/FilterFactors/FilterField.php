<?php
namespace Avetify\Entities\FilterFactors;

use Avetify\DB\Filters\DBFilterInterface;
use Avetify\Fields\BaseRecordField;
use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;

class FilterField extends FilterFactor implements IdentifiedElement {
    use IdentifiedElementTrait;

    public function __construct(public BaseRecordField $recordField){
        parent::__construct($this->recordField->key, $this->recordField->title);
    }

    public function isQualified($item, $param): bool {
        if(method_exists($this->recordField, "isQualified")){
            return $this->recordField->isQualified($item, $param);
        }

        return parent::isQualified($item, $param);
    }

    public function dbQualifyingFilter($param): DBFilterInterface | null {
        if(method_exists($this->recordField, "dbQualifyingFilter")){
            return $this->recordField->dbQualifyingFilter($param);
        }

        return parent::dbQualifyingFilter($param);
    }

    public function getElementIdentifier($item = null) {
        if(method_exists($this->recordField, "getElementIdentifier")){
            return $this->recordField->getElementIdentifier();
        }
        return null;
    }
}
