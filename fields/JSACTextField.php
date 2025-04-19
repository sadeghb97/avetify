<?php

class JSACTextField extends JSTextField {
    public string $enterCallbackName = "logSelectedRecord";

    public function __construct(string $fieldKey, string $childKey,
                                string $initValue, public DatalistInfo $dlInfo){
        parent::__construct($fieldKey, $childKey, $initValue);
        $this->listIdentifier = $dlInfo->datalistId;
    }

    public function present(){
        $this->presentListField();
    }

    public function applyText(): string {
        return 'acOnItemEntered(' . '\'' . $this->getFieldIdentifier() . '\', \'' . $this->childKey . '\''
            . ', ' . $this->dlInfo->jsRecordsVarName . ', \'' . $this->dlInfo->recordLabelKey . '\', '
            . $this->enterCallbackName . ');';
    }
}

class APIACTextField extends APITextField {
    public function __construct(string $fieldKey, string $childKey,
                                string $initValue, string $endpoint, string $listId){
        parent::__construct($fieldKey, $childKey, $initValue, $endpoint);
        $this->listIdentifier = $listId;
    }

    public function present(){
        $this->presentListField();
    }
}
