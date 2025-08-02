<?php
namespace Avetify\Fields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Fields\JSTextFields\JSInputField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class SimpleMedalField extends APIMedalField {
    public bool $skipEmpties = false;
    public function __construct(public string $recordKey, public string $medalKey,
                                public string $icon, public float $medalInitValue,
    ){
        parent::__construct($this->recordKey, $this->medalKey, $this->icon, $this->medalInitValue, "");
    }

    public function setSkipEmpties(){
        $this->skipEmpties = true;
    }

    public function place(?WebModifier $webModifier = null){
        if($this->medalInitValue > 0) {
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
            HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
            HTMLInterface::closeTag();
            echo $this->medalInitValue;
            echo '</span>';

            $div->close();
        }
    }
}
