<?php
namespace Avetify\Entities\Fields\DateFields;

use Avetify\Entities\EntityField;

class CreatedAtField extends EntityField {
    public function __construct(){
        parent::__construct("created_at", "Created At");
        $this->setAutoTimeCreate();
    }
}
