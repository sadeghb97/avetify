<?php
namespace Avetify\Table\Fields\NumberFields;

use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableSimpleField;

class PercentField extends TableSimpleField {
    public function presentValue($item, ?WebModifier $webModifier = null){
        echo $this->getValue($item) . '%';
    }
}
