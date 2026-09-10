<?php
namespace Avetify\Entities\Fields\DateFields;

use Avetify\Entities\EntityField;
use Avetify\Utils\TimeUtils\TimeUtils;

class UnixTimeField extends EntityField {
    public function formattedValue($item): string {
        $rawValue = $this->getValue($item);
        if(!$rawValue) return "Never";
        return TimeUtils::formattedDateTime($rawValue);
    }
}
