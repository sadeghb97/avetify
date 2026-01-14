<?php
namespace Avetify\Entities\Fields\Containers;

use Avetify\Fields\Containers\RowFields;

class EntityRowFields extends EntityFieldsContainer {
    public function __construct(string $key, string $title, array $childs){
        parent::__construct(new RowFields($key, $title, $childs));
    }
}