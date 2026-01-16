<?php
namespace Avetify\Forms;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Forms\Buttons\FormButton;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class AvtForm {
    /** @var Placeable[] */
    public array $triggers = [];

    /** @var FormHiddenProperty[] */
    public array $hiddenData = [];

    public function __construct(public string $formIdentifier, public string $formMethod = "POST"){
    }

    public function addTrigger(Placeable $trigger){
        if($trigger instanceof FormButton) $trigger->formTriggerElementId = $this->getTriggerHiddenId();
        $this->triggers[] = $trigger;
    }

    public function addHiddenElement(FormHiddenProperty $hiddenProperty){
        $this->hiddenData[$hiddenProperty->hiddenPropertyId] = $hiddenProperty;
    }

    public function openForm(?WebModifier $webModifier = null) {
        echo '<form ';
        HTMLInterface::addAttribute("id", $this->formIdentifier);
        HTMLInterface::addAttribute("name", $this->formIdentifier);
        HTMLInterface::addAttribute("method", $this->formMethod);
        Styler::classStartAttribute();
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::applyModifiers($webModifier);
        HTMLInterface::closeTag();

        $this->placeHiddenData();
    }

    public function placeHiddenData(){
        FormUtils::placeHiddenField($this->getTriggerHiddenId(), "", false);

        foreach ($this->hiddenData as $hiddenProperty){
            FormUtils::placeHiddenField($hiddenProperty->hiddenPropertyId, $hiddenProperty->value,
                !$hiddenProperty->useName);
        }
    }

    public function placeTriggers($trSepSize = 8){
        $div = new NiceDiv($trSepSize);
        $div->open();

        foreach ($this->triggers as $trigger){
            $div->placeItem($trigger);
        }

        $div->close();
    }

    public function closeForm(){
        echo '</form>';
    }

    public function getTriggerHiddenId() : string {
        return $this->formIdentifier . "_" . "trigger";
    }

    public function getCurrentTrigger() : string {
        return !empty($_POST[$this->getTriggerHiddenId()]) ? $_POST[$this->getTriggerHiddenId()] : "";
    }
}
