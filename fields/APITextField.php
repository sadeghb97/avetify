<?php

class APITextField {
    public function __construct(public string $recordKey, public string $fieldKey,
                                public string $initValue, public string $apiEndpoint){
    }

    public static function initJs(){
        ThemesManager::importJS(Routing::getAventadorRoot() . "fields/api_medals.js");
    }

    public function getFieldIdentifier() : string {
        return $this->fieldKey . "_" . $this->recordKey;
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

    public function boundEnterEvent(){
        HTMLInterface::addAttribute("onkeydown", "if (event.key === 'Enter') {" .
            $this->applyText() .
            "}");
    }
}
