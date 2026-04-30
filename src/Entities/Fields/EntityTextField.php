<?php
namespace Avetify\Entities\Fields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Entities\EntityField;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

class EntityTextField extends EntityField {
    public function setWritable(): EntityField {
        $this->baseModifier->pushStyle("height", "36px");
        return parent::setWritable();
    }
}
