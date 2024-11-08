<?php

class IRDateField extends SBTableSimpleField {
    public function presentValue($item) {
        $val = $this->getValue($item);
        $timeStr = jdate("Y/m/d - H:i:s", $val, '', 'Asia/Tehran', 'en');
        echo $timeStr;
    }
}
