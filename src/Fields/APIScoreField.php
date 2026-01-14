<?php
namespace Avetify\Fields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class APIScoreField extends APIMedalField {
    public function __construct(public string $mainKey, public string $altKey,
                                public float $initValue, public string $apiEndpoint
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
        $this->placeElementIdAttributes();
        HTMLInterface::closeTag();
        echo $this->medalInitValue;
        echo '</span>';

        HTMLInterface::closeDiv();
    }
}
