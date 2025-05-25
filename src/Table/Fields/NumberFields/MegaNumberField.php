<?php
namespace Avetify\Table\Fields\NumberFields;

use Avetify\Table\Fields\TableSimpleField;
use Avetify\Utils\NumberUtils;

class MegaNumberField extends TableSimpleField {
    public function presentValue($item){
        echo NumberUtils::formatMegaNumber($this->getValue($item));
    }
}
