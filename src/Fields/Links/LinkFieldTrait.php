<?php
namespace Avetify\Fields\Links;

use Avetify\DB\DBConnection;
use Avetify\Fields\BaseRecordField;
use Avetify\Fields\DateFields\DateValueType;
use Avetify\Fields\StructuredRecordValueField;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

trait LinkFieldTrait {
    public bool $isBlank = false;

    public function setBlank() : static {
        $this->isBlank = true;
        return $this;
    }
}