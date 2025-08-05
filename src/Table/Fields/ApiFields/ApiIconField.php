<?php
namespace Avetify\Table\Fields\ApiFields;

use Avetify\Entities\BasicProperties\EntityProfile;
use Avetify\Fields\APIMedalField;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableField;

class ApiIconField extends TableField {
    public function __construct(string $title, string $key, public string $icon, public string $apiEndpoint){
        parent::__construct($title, $key);
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        if($item instanceof EntityProfile) {
            $recordKey = $item->getItemId();
            $recordValue = $this->getValue($item);
            $medalField = new APIMedalField($recordKey, $this->key, $this->icon,
                $recordValue, $this->apiEndpoint);

            $medalField->place($webModifier);
        }
    }
}
