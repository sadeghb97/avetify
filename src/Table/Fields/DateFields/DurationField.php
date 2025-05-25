<?php
namespace Avetify\Table\Fields\DateFields;

use Avetify\Table\Fields\TableSimpleField;
use function Avetify\Utils\getFormattedDurationTime;
use function Avetify\Utils\getIRFormattedDurationTime;

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
