<?php
namespace Avetify\Table\Fields\DateFields;

use Avetify\Table\Fields\TableSimpleField;
use Avetify\Utils\TimeUtils\TimeUtils;

class RecentField extends TableSimpleField {
    public function __construct(string $title, string $key, public bool $isGlobal = true){
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        $val = $this->getValue($item);
        $duration = time() - ((int) $val);
        echo TimeUtils::summaryFormatDuration($duration);
    }
}
