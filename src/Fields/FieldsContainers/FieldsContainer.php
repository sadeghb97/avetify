<?php
namespace Avetify\Fields\FieldsContainers;

use Avetify\Fields\BaseRecordField;

class FieldsContainer extends BaseRecordField {
    /** @var BaseRecordField[] $childs */
    public array $childs = [];
    public int $sepSize = 4;

    public function __construct(string $title, string $key, array $childs){
        parent::__construct($key, $title);
        $this->childs = $childs;
    }

    public function setSeparatorSize(int $sepSize): FieldsContainer {
        $this->sepSize = $sepSize;
        return $this;
    }
}
