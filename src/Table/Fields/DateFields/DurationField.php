<?php
namespace Avetify\Table\Fields\DateFields;

use Avetify\Table\Fields\TableSimpleField;
use Avetify\Utils\TimeUtils\TimeUtils;

class DurationField extends TableSimpleField {
    public function __construct(string $title, string $key, public bool $isGlobal = true){
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        $val = $this->getValue($item);
        $timeStr = $this->isGlobal ?
            TimeUtils::getFormattedDurationTime((int) $val) : TimeUtils::getIRFormattedDurationTime((int) $val);
        echo $timeStr;
    }
}
