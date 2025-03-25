<?php

class SBTableSortField extends SortFactor {
    public function __construct(public SBTableField $field){
        parent::__construct($this->field->title, $this->field->key, !$this->field->isAscending,
            $this->field->isNumeric, $this->field->skipEmpties);
    }

    public function getValue($item): float|string {
        return $this->field->getValue($item);
    }

    public function isQualified($item): bool {
        return $this->field->isQualified($item);
    }
}
