<?php
namespace Avetify\Table\Fields\TextFields;

use Avetify\Table\Fields\TableSimpleField;

class TitleCaseField extends TableSimpleField {
    public function presentValue($item) {
        $val = $this->getValue($item);
        $val = str_replace("_", " ", $val);
        echo ucwords($val);
    }
}