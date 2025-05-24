<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;

class EntitySelectField extends EntityField {
    public function __construct($key, $title, public string $dataSetKey) {
        parent::__construct($key, $title);
        $this->type = "select";
    }
}
