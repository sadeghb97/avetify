<?php
namespace Avetify\Table\Fields;

use Avetify\Fields\BaseRecordField;
use Avetify\Interface\WebModifier;

class TableFieldWrapper extends TableField {
    public function __construct(public BaseRecordField $recordField) {
        parent::__construct($recordField->title, $recordField->key);

        if(property_exists($this->recordField, "useNameIdentifier")) {
            $this->recordField->useNameIdentifier = false;
        }
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        $this->recordField->presentValue($item, $webModifier);
    }
}
