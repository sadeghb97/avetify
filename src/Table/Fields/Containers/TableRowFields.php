<?php
namespace Avetify\Table\Fields\Containers;

use Avetify\Fields\Containers\RowFields;

class TableRowFields extends TableFieldsContainer {
    public function __construct(string $title, string $key, array $childs){
        parent::__construct(new RowFields($key, $title, $childs));
    }
}