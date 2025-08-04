<?php
namespace Avetify\Table\Fields\FieldsContainers;

use Avetify\Table\Fields\TableField;

class FieldsContainer extends TableField {
    /** @var TableField[] $childs */
    public array $childs = [];
    public int $sepSize = 4;

    public function __construct(string $title, string $key, array $childs){
        parent::__construct($title, $key);
        $this->childs = $childs;
    }

    public function setSeparatorSize(int $sepSize): FieldsContainer {
        $this->sepSize = $sepSize;
        return $this;
    }
}
