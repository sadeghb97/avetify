<?php

abstract class JSTextField extends JSInputField {
    public string $listIdentifier = "";
    public bool $disableSubmitOnEnter = true;

    public function __construct(public string $fieldKey, public string $recordKey,
                                public string $initValue){
    }

    public function getFieldIdentifier() : string {
        if($this->recordKey) return $this->fieldKey . "_" . $this->recordKey;
        return $this->fieldKey;
    }

    public function present(){
        $div = new NiceDiv(0);
        $div->baseOpen();
        HTMLInterface::closeTag();

        echo '<input ';
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
        HTMLInterface::addAttribute("type", "text");
        $this->boundEnterEvent();
        HTMLInterface::closeSingleTag();

        $div->close();
    }

    abstract public function applyText() : string;

    public function boundEnterEvent(){
        HTMLInterface::addAttribute("onkeydown", "if (event.key === 'Enter') {" .
            ($this->disableSubmitOnEnter ? "event.preventDefault();" : "") .
            $this->applyText() .
            "}");
    }

    public function presentList(){
        echo '<input ';
        HTMLInterface::addAttribute("list", $this->listIdentifier);
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
        $this->boundEnterEvent();
        HTMLInterface::closeSingleTag();
    }
}

class APITextField extends JSTextField {
    public function __construct(string $fieldKey, string $recordKey, string $initValue,
                                public string $apiEndpoint){
        parent::__construct($fieldKey, $recordKey, $initValue);
    }

    public function applyText() : string {
        return 'apiTextEnterAction(\'' . $this->getFieldIdentifier() . '\', \'' . $this->recordKey .
            '\', \'' . $this->fieldKey . '\', \'' .
            $this->apiEndpoint . '\', ' . $this->applyTextCallback() . ');';
    }

    public function applyTextCallback() : string {
        return "(data) => {" .
            "console.log('DATA', data)" .
            "}";
    }
}
