<?php
namespace Avetify\Table\Fields;

use Avetify\Fields\BaseRecordField;
use Avetify\Fields\FieldWrapperTrait;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

class TableFieldWrapper extends TableField {
    use FieldWrapperTrait;
    public function __construct(BaseRecordField $recordField) {
        $this->recordField = $recordField;
        parent::__construct($recordField->title, $recordField->key);

        if(property_exists($this->recordField, "useNameIdentifier")) {
            $this->recordField->useNameIdentifier = false;
        }
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        echo '<div ';
        $webModifier?->apply();
        HTMLInterface::closeTag();
        $this->recordField->placeField($item);
        HTMLInterface::closeDiv();
    }
}
