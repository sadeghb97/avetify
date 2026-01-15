<?php
namespace Avetify\Entities\Fields\Containers;

use Avetify\Entities\Fields\EntityFieldWrapper;

class EntityFieldsContainer extends EntityFieldWrapper {
    public function setSeparatorSize(int $sepSize): EntityFieldsContainer {
        if(property_exists($this->recordField, "sepSize")) {
            $this->recordField->sepSize = $sepSize;
        }
        return $this;
    }
}