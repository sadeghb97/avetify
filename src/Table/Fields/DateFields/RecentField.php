<?php
namespace Avetify\Table\Fields\DateFields;

use Avetify\Table\Fields\TableSimpleField;
use function Avetify\Utils\getFormattedRecentTime;
use function Avetify\Utils\getIRFormattedRecentTime;

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
