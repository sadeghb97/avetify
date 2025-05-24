<?php
namespace Avetify\Table\Fields;

class TitleCaseField extends TableSimpleField {
    public function presentValue($item) {
        $val = $this->getValue($item);
        $val = str_replace("_", " ", $val);
        echo ucwords($val);
    }
}