<?php

class APIACTextField extends APITextField {
    public bool $datalistReady = false;

    public function __construct(string $recordKey, string $fieldKey,
                                string $initValue, string $apiEndpoint){
        parent::__construct($recordKey, $fieldKey, $initValue, $apiEndpoint);
    }

    public function getListIdentifier(): string {
        return "tags_datalist";
    }

    public function present(){
        echo '<input ';
        HTMLInterface::addAttribute("list", $this->getListIdentifier());
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
        $this->boundEnterEvent();
        HTMLInterface::closeSingleTag();
    }
}
