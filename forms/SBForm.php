<?php
class SBForm {
    /** @var Placeable[] */
    public array $triggers;

    /** @var FormHiddenProperty[] */
    public array $hiddenData;

    public string $currentTrigger = "";

    public function __construct(public string $formIdentifier, public string $formMethod = "POST"){
    }

    public function addTrigger(Placeable $trigger){
        if($trigger instanceof FormButton) $trigger->formTriggerElementId = $this->getTriggerHiddenId();
        $this->triggers[] = $trigger;
    }

    public function addHiddenElement(FormHiddenProperty $hiddenProperty){
        $this->hiddenData[$hiddenProperty->hiddenPropertyId] = $hiddenProperty;
    }

    public function openForm() {
        echo '<form ';
        HTMLInterface::addAttribute("id", $this->formIdentifier);
        HTMLInterface::addAttribute("name", $this->formIdentifier);
        HTMLInterface::addAttribute("method", $this->formMethod);
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

    public function placeTriggers(){
        $div = new NiceDiv(8);
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
}

class FormHiddenProperty {
    public function __construct(public string $hiddenPropertyId,
                                public string $value,
                                public bool $useId = true,
                                public $useName = true
    ){
    }
}
