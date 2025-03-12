<?php

class IRDateField extends SBTableSimpleField {
    public function presentValue($item) {
        $val = $this->getValue($item);
        $timeStr = jdate("Y/m/d - H:i:s", $val, '', 'Asia/Tehran', 'en');
        echo $timeStr;
    }
}

class RecentField extends SBTableSimpleField {
    public function __construct(string $title, string $key, public bool $isGlobal = true){
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        $val = $this->getValue($item);
        $timeStr = $this->isGlobal ?
            getFormattedRecentTime(time(), (int) $val) : getIRFormattedRecentTime(time(), (int) $val);
        echo $timeStr;
    }
}
