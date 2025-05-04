<?php

class APIScoreField extends APIMedalField {
    public function __construct(public string $mainKey, public string $altKey,
                                public int $initValue, public string $apiEndpoint
    ){
        parent::__construct($mainKey, $altKey, "", $initValue, $apiEndpoint);
    }

    public function place(?WebModifier $webModifier = null){
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("number-label");
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        HTMLInterface::addAttribute("onclick", $this->clickAction());
        HTMLInterface::closeTag();

        echo '<sapn ';
        Styler::startAttribute();
        Styler::addStyle("font-weight", "bold");
        Styler::closeAttribute();
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
        HTMLInterface::closeTag();
        echo $this->medalInitValue;
        echo '</span>';

        HTMLInterface::closeDiv();
    }
}
