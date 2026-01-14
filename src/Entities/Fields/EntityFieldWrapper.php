<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;
use Avetify\Fields\BaseRecordField;
use Avetify\Interface\WebModifier;

class EntityFieldWrapper extends EntityField {
    public function __construct(public BaseRecordField $recordField) {
        parent::__construct($recordField->key, $recordField->title);

        if(property_exists($this->recordField, "useNameIdentifier")) {
            $this->recordField->useNameIdentifier = true;
        }
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        $this->recordField->presentValue($item, $webModifier);
    }
}
