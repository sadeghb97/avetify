<?php
namespace Avetify\Table\Fields\NumberFields;

use Avetify\Table\Fields\TableSimpleField;

class PercentField extends TableSimpleField {
    public function presentValue($item){
        echo $this->getValue($item) . '%';
    }
}
