<?php
namespace Avetify\Table\Fields\DateFields;

use Avetify\Externals\JDF;
use Avetify\Table\Fields\TableSimpleField;

class IRDateField extends TableSimpleField {
    public function presentValue($item) {
        $val = $this->getValue($item);
        $timeStr = JDF::jdate("Y/m/d - H:i:s", $val, '', 'Asia/Tehran', 'en');
        echo $timeStr;
    }
}
