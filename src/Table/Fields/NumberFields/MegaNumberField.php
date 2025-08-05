<?php
namespace Avetify\Table\Fields\NumberFields;

use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableSimpleField;
use Avetify\Utils\NumberUtils;

class MegaNumberField extends TableSimpleField {
    public function presentValue($item, ?WebModifier $webModifier = null){
        echo NumberUtils::formatMegaNumber($this->getValue($item));
    }
}
