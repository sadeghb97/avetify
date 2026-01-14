<?php
namespace Avetify\Table\Fields\Containers;

use Avetify\Fields\Containers\RowFields;
use Avetify\Table\Fields\TableFieldWrapper;

class TableRowFields extends TableFieldWrapper {
    public function __construct(string $title, string $key, array $childs){
        parent::__construct(new RowFields($key, $title, $childs));
    }
}