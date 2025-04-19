<?php

abstract class JSACTextField extends JSTextField {
    public function __construct(string $fieldKey, string $recordKey,
                                string $initValue, public DatalistInfo $dlInfo){
        parent::__construct($fieldKey, $recordKey, $initValue);
        $this->listIdentifier = $dlInfo->datalistId;
    }

    public function present(){
        $this->presentList();
    }

    public function onEnterCallbackName() : string {
        return "logRecord";
    }

    public function applyText(): string {
        return 'acOnItemEntered(' . '\'' . $this->fieldKey . '\''
            . ', ' . $this->dlInfo->jsRecordsVarName . ', \'' . $this->dlInfo->recordLabelKey . '\', '
            . $this->onEnterCallbackName() . ');';
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
