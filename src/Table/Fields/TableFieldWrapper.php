<?php
namespace Avetify\Table\Fields;

use Avetify\Fields\BaseRecordField;
use Avetify\Fields\FieldWrapperTrait;

class TableFieldWrapper extends TableField {
    use FieldWrapperTrait;
    public function __construct(BaseRecordField $recordField) {
        $this->recordField = $recordField;
        parent::__construct($recordField->title, $recordField->key);

        if(property_exists($this->recordField, "useNameIdentifier")) {
            $this->recordField->useNameIdentifier = false;
        }
    }
}
