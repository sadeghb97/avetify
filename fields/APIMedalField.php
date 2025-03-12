<?php

class APIMedalField {
    public function __construct(public string $recordKey, public string $medalKey,
                                public string $icon, public int $medalInitValue,
                                public string $apiEndpoint){
    }

    public static function initJs(){
        ThemesManager::importJS(Routing::getAventadorRoot() . "fields/api_medals.js");
    }

    public function geiFieldIdentifier() : string {
        return $this->medalKey . "_" . $this->recordKey;
    }

    public function clickAction() : string {
        return 'apiMedalClickAction(\'' . $this->geiFieldIdentifier() . '\', \'' . $this->recordKey .
            '\', \'' . $this->medalKey . '\', \'' . $this->medalInitValue .
            '\', \'' . $this->apiEndpoint . '\')';
    }

    public function present(){
        $div = new NiceDiv(0);
        $div->baseOpen();
        HTMLInterface::addAttribute("onclick", $this->clickAction());
        HTMLInterface::closeTag();

        HTMLInterface::placeImageWithHeight($this->icon, 48);
        $div->separate();

        echo '<sapn ';
        Styler::startAttribute();
        Styler::addStyle("font-weight", "bold");
        Styler::closeAttribute();
        HTMLInterface::addAttribute("id", $this->geiFieldIdentifier());
        HTMLInterface::closeTag();
        echo $this->medalInitValue;
        echo '</span>';

        $div->close();
    }
}
