<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;
use Avetify\Fields\BaseRecordField;
use Avetify\Fields\FieldWrapperTrait;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

class EntityFieldWrapper extends EntityField {
    use FieldWrapperTrait;
    public function __construct(BaseRecordField $recordField) {
        $this->recordField = $recordField;
        parent::__construct($recordField->key, $recordField->title);

        if(property_exists($this->recordField, "useNameIdentifier")) {
            $this->recordField->useNameIdentifier = true;
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
