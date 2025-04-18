<?php

abstract class JSACTextField extends JSTextField {
    public bool $datalistReady = false;

    public function __construct(string $fieldKey, string $recordKey,
                                string $initValue, string $listId){
        parent::__construct($fieldKey, $recordKey, $initValue);
        $this->listIdentifier = $listId;
    }

    public function present(){
        $this->presentList();
    }
}

class APIACTextField extends APITextField {
    public function __construct(string $fieldKey, string $recordKey,
                                string $initValue, string $endpoint, string $listId){
        parent::__construct($fieldKey, $recordKey, $initValue, $endpoint);
        $this->listIdentifier = $listId;
    }

    public function present(){
        $this->presentList();
    }
}
