<?php
namespace Avetify\Table\Fields\DateFields;

use Avetify\Table\Fields\TableSimpleField;

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
