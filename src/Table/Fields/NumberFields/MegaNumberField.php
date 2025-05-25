<?php
namespace Avetify\Table\Fields\NumberFields;

use Avetify\Table\Fields\TableSimpleField;
use function Avetify\Utils\formatMegaNumber;

class MegaNumberField extends TableSimpleField {
    public function presentValue($item){
        echo formatMegaNumber($this->getValue($item));
    }
}
