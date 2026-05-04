<?php
namespace Avetify\Table\Fields\Containers;

use Avetify\Fields\BaseRecordField;
use Avetify\Table\Fields\TableFieldWrapper;

/**
 * @method self modifyRecordSetSeparatorSize(int $sepSize)
 */
class TableFieldsContainer extends TableFieldWrapper {
    public function __construct(BaseRecordField $recordField) {
        parent::__construct($recordField);
        $this->baseModifier?->pushStyle("margin", "auto");
    }
}
