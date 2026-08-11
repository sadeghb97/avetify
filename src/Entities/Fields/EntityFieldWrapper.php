<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;
use Avetify\Fields\BaseRecordField;
use Avetify\Fields\FieldWrapperTrait;

class EntityFieldWrapper extends EntityField {
    use FieldWrapperTrait;
    public function __construct(BaseRecordField $recordField) {
        $this->recordField = $recordField;
        parent::__construct($recordField->key, $recordField->title);

        if(property_exists($this->recordField, "useNameIdentifier")) {
            $this->recordField->useNameIdentifier = true;
        }

        if(property_exists($this->recordField, "legacyGeneralNaming")) {
            $this->recordField->legacyGeneralNaming = true;
        }

        if(property_exists($this->recordField, "isLabelEnabled")) {
            $this->recordField->isLabelEnabled = true;
        }
    }
}
