<?php
namespace Avetify\Entities\Fields\Containers;

use Avetify\Entities\Fields\EntityFieldWrapper;
use Avetify\Fields\Containers\ColumnFields;

class EntityColumnFields extends EntityFieldWrapper {
    public function __construct(string $key, string $title, array $childs){
        parent::__construct(new ColumnFields($key, $title, $childs));
    }
}
