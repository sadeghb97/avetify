<?php
namespace Avetify\Table\Fields\TextFields;

use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableSimpleField;

class TitleCaseField extends TableSimpleField {
    public function presentValue($item, ?WebModifier $webModifier = null) {
        $val = $this->getValue($item);
        $val = str_replace("_", " ", $val);
        echo ucwords($val);
    }
}