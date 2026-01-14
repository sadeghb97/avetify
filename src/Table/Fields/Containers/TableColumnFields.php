<?php
namespace Avetify\Table\Fields\Containers;

use Avetify\Fields\Containers\ColumnFields;
use Avetify\Table\Fields\TableFieldWrapper;

class TableColumnFields extends TableFieldWrapper {
    public function __construct(string $title, string $key, array $childs){
        parent::__construct(new ColumnFields($key, $title, $childs));
    }
}
