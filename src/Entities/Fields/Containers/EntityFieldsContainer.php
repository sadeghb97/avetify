<?php
namespace Avetify\Entities\Fields\Containers;

use Avetify\Entities\Fields\EntityFieldWrapper;
use Avetify\Fields\BaseRecordField;

/**
 * @method self modifyRecordSetSeparatorSize(int $sepSize)
 */
class EntityFieldsContainer extends EntityFieldWrapper {
    public function __construct(BaseRecordField $recordField) {
        parent::__construct($recordField);
        $this->baseModifier?->pushStyle("margin", "auto");
    }
}