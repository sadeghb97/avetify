<?php
namespace Avetify\Table\Fields\NumberFields;

use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableSimpleField;

class PercentField extends TableSimpleField {
    public function presentValue($item, ?WebModifier $webModifier = null){
        HTMLInterface::placeSpan($this->getValue($item) . '%', $webModifier);
    }
}
