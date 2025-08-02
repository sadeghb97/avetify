<?php
namespace Avetify\Table\Fields\MedalFields;

use Avetify\Entities\BasicProperties\EntityProfile;
use Avetify\Fields\SimpleMedalField;
use Avetify\Table\Fields\TableField;

class SimpleIconField extends TableField {
    public function __construct(string $title, string $key, public string $icon, public bool $skipEmpties){
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        if($item instanceof EntityProfile) {
            $recordKey = $item->getItemId();
            $recordValue = $this->getValue($item);
            $medalField = new SimpleMedalField($recordKey, $this->key, $this->icon, $recordValue);
            if($this->skipEmpties) $medalField->setSkipEmpties();

            $medalField->place();
        }
    }
}
