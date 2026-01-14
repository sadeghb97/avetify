<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;
use Avetify\Interface\RecordField;
use Avetify\Interface\WebModifier;

class EntityFieldWrapper extends EntityField {
    public function __construct(public RecordField $recordField) {
        parent::__construct($recordField->key, $recordField->title);
        $this->recordField->useNameIdentifier = true;
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        $this->recordField->presentValue($item, $webModifier);
    }
}
