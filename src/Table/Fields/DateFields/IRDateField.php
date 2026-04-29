<?php
namespace Avetify\Table\Fields\DateFields;

use Avetify\Externals\JDF;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableSimpleField;

class IRDateField extends TableSimpleField {
    public function presentValue($item, ?WebModifier $webModifier = null) {
        $val = $this->getValue($item);
        $timeStr = JDF::jdate("Y/m/d - H:i:s", $val, '', 'Asia/Tehran', 'en');
        HTMLInterface::placeSpan($timeStr, $webModifier);
    }
}
