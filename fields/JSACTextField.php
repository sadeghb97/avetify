<?php

use JetBrains\PhpStorm\Pure;

class JSACTextField extends JSTextField {
    public string $enterCallbackName = "logSelectedRecord";

    /**
     * elemente asli: inpute dar bar girande dataye asli. mamulan ye inpute sade
     * @param string $fieldKey bakhshe shorue id elemente asli
     * @param string $childKey bakhshe payane id elemente asli. dar callbacke selecte item
     *          shome be an dastresi khahid dasht
     */
    public function __construct(string $fieldKey, string $childKey,
                                string $initValue, public DatalistInfo $dlInfo){
        parent::__construct($fieldKey, $childKey, $initValue);
        $this->listIdentifier = $dlInfo->datalistId;
    }

    public function present(){
        $this->presentListField();
    }

    public function callbackMoreData() : array {
        return [];
    }

    #[Pure]
    public function applyText(): string {
        $cmdJson = json_encode($this->callbackMoreData());
        $cmdSafe = htmlspecialchars($cmdJson, ENT_QUOTES, 'UTF-8');

        return 'acOnItemEntered(' . '\'' . $this->getFieldIdentifier() . '\', \'' . $this->childKey . '\''
            . ', ' . $this->dlInfo->jsRecordsVarName . ', \''
            . $this->dlInfo->recordLabelKey . '\', '
            . $cmdSafe . ', '
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
