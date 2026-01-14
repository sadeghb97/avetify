<?php
namespace Avetify\Table\Fields\Containers;

use Avetify\Fields\Containers\ColumnFields;

class TableColumnFields extends TableFieldsContainer {
    public function __construct(string $title, string $key, array $childs){
        parent::__construct(new ColumnFields($key, $title, $childs));
    }
}
