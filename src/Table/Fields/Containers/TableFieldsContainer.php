<?php
namespace Avetify\Table\Fields\Containers;

use Avetify\Table\Fields\TableFieldWrapper;

class TableFieldsContainer extends TableFieldWrapper {
    public function setSeparatorSize(int $sepSize): TableFieldWrapper {
        if(property_exists($this->recordField, "sepSize")) {
            $this->recordField->sepSize = $sepSize;
        }
        return $this;
    }
}
