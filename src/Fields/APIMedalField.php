<?php
namespace Avetify\Fields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Fields\JSTextFields\JSInputField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class APIMedalField extends JSInputField implements Placeable, IdentifiedElement {
    use IdentifiedElementTrait;
    public function __construct(public string $recordKey, public string $medalKey,
                                public string $icon, public float $medalInitValue,
                                public string $apiEndpoint){
    }

    public function getElementIdentifier($item = null) {
        return $this->medalKey . "_" . $this->recordKey;
    }

    public function clickAction() : string {
        if($this->apiEndpoint) {
            return 'apiMedalClickAction(\'' . $this->getElementIdentifier() . '\', \'' . $this->recordKey .
                '\', \'' . $this->medalKey . '\', \'' . $this->medalInitValue .
                '\', \'' . $this->apiEndpoint . '\')';
        }
        else return "";
    }

    public function place(?WebModifier $webModifier = null){
        $div = new NiceDiv(0);
        $div->baseOpen($webModifier);
        HTMLInterface::addAttribute("onclick", $this->clickAction());
        HTMLInterface::closeTag();

        HTMLInterface::placeImageWithHeight($this->icon, 48);
        $div->separate();

        echo '<sapn ';
        Styler::startAttribute();
        Styler::addStyle("font-weight", "bold");
        Styler::closeAttribute();
        $this->placeElementIdAttributes();
        HTMLInterface::closeTag();
        echo $this->medalInitValue;
        echo '</span>';

        $div->close();
    }
}
