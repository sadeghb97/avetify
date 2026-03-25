<?php
namespace Avetify\Table\Fields;

use Avetify\Entities\FilterFactors\Qualifier;
use Avetify\Fields\BaseRecordField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Table\Fields\EditableFields\EditableField;

class ConstField {
    public function __construct(public string $key, public string $value, public bool $isNumeric){}
}