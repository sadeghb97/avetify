<?php
namespace Avetify\Table\Fields;

use Avetify\Entities\Sorters\SortFactor;

class TableSortField extends SortFactor {
    public function __construct(public TableField $field){
        parent::__construct($this->field->title, $this->field->key, !$this->field->isAscending,
            $this->field->isNumeric, $this->field->skipEmpties);
        $this->isDefaultSort = $this->field->isDefaultSort;
        $this->tieBreaks = $this->field->tieBreaks;
    }

    public function getValue($item): float|string {
        return $this->field->getValue($item);
    }

    public function isQualified($item): bool {
        return $this->field->isQualified($item);
    }
}
