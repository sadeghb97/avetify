<?php
namespace Avetify\Entities\Fields\DateFields;

use Avetify\Entities\EntityField;

class UpdatedAtField extends EntityField {
    public function __construct(){
        parent::__construct("updated_at", "Updated");
        $this->setAutoTimeUpdate();
    }
}
