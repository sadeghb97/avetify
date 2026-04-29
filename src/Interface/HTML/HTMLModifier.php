<?php
namespace Avetify\Interface\HTML;

class HTMLModifier {
    public array $modifiers = [];

    public function pushModifier($modifierKey, $modifierValue){
        $this->modifiers[$modifierKey] = $modifierValue;
    }

    public function popModifier($modifierKey){
        unset($this->modifiers[$modifierKey]);
    }

    public function applyModifiers(){
        foreach ($this->modifiers as $modifierKey => $modifierValue){
            HTMLInterface::addAttribute($modifierKey, $modifierValue);
        }
    }

    public function merge(HTMLModifier | null $secondModifier) : HTMLModifier {
        $outModifier = clone $this;
        if($secondModifier) {
            foreach ($secondModifier->modifiers as $secAttrKey => $secAttrValue) {
                $outModifier->modifiers[$secAttrKey] = $secAttrValue;
            }
        }
        return $outModifier;
    }
}
