<?php
namespace Avetify\Table\Fields\DateFields;

use Avetify\Table\Fields\TableSimpleField;
use function Avetify\Externals\jdate;

class IRDateField extends TableSimpleField {
    public function presentValue($item) {
        $val = $this->getValue($item);
        $timeStr = jdate("Y/m/d - H:i:s", $val, '', 'Asia/Tehran', 'en');
        echo $timeStr;
    }
}
