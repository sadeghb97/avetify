<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;
use Avetify\Fields\JSDynamicSelect;
use Avetify\Interface\WebModifier;

class EntitySelectField extends EntityField {
    public function __construct($key, $title, public string $dataSetKey) {
        parent::__construct($key, $title);
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $title = $this->title;
        $key = $this->key;
        $value = $this->getValue($item);

        $sModifier = WebModifier::createInstance();
        $sModifier->styler->pushStyle("margin-top", "8px");
        $sModifier->styler->pushStyle("margin-bottom", "8px");
        $selectField = new JSDynamicSelect($this->title, $key, $value, $this->dataSetKey);
        $selectField->setNameIdentifier = true;
        $selectField->place($sModifier);
    }
}
