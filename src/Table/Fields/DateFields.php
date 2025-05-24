<?php
namespace Avetify\Table\Fields;

use function Avetify\Externals\jdate;
use function Avetify\Utils\getFormattedDurationTime;
use function Avetify\Utils\getFormattedRecentTime;
use function Avetify\Utils\getIRFormattedDurationTime;
use function Avetify\Utils\getIRFormattedRecentTime;

class IRDateField extends TableSimpleField {
    public function presentValue($item) {
        $val = $this->getValue($item);
        $timeStr = jdate("Y/m/d - H:i:s", $val, '', 'Asia/Tehran', 'en');
        echo $timeStr;
    }
}

class RecentField extends TableSimpleField {
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

class DurationField extends TableSimpleField {
    public function __construct(string $title, string $key, public bool $isGlobal = true){
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        $val = $this->getValue($item);
        $timeStr = $this->isGlobal ?
            getFormattedDurationTime((int) $val) : getIRFormattedDurationTime((int) $val);
        echo $timeStr;
    }
}

class TimeDurationField extends TableSimpleField {
    public function __construct(string $title, string $key, public bool $isGlobal = true){
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        $val = $this->getValue($item);
        $hours = (int)($val / 3600);
        $rem = $val % 3600;
        $minutes = (int)($rem / 60);
        $seconds = $rem % 60;

        if($hours > 0) {
            echo $hours . ':';
        }
        if($minutes > 0) {
            if ($minutes < 10) echo '0';
            echo $minutes . ':';
        }
        if($seconds < 10) echo '0';
        echo $seconds;
    }
}
