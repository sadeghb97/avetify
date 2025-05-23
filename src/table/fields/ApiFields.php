<?php

class ApiIconField extends SBTableField {
    public function __construct(string $title, string $key, public string $icon, public string $apiEndpoint){
        parent::__construct($title, $key);
    }

    public function presentValue($item) {
        if($item instanceof EntityProfile) {
            $recordKey = $item->getItemId();
            $recordValue = $this->getValue($item);
            $medalField = new APIMedalField($recordKey, $this->key, $this->icon,
                $recordValue, $this->apiEndpoint);

            $medalField->place();
        }
    }
}
