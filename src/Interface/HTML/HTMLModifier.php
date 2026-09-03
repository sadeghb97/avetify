<?php
namespace Avetify\Interface\HTML;

class HTMLModifier {
    public array $modifiers = [];

    public function pushModifier($modifierKey, $modifierValue){
        $this->modifiers[$modifierKey] = $modifierValue;
    }

    public function pushNormalizedModifier($modifierKey, $modifierValue) : void {
        $normalizedModifierValue = $modifierValue !== null ? HTMLInterface::normalizedAttributeValue($modifierValue) : null;
        $this->pushModifier($modifierKey, $normalizedModifierValue);
    }

    public function popModifier($modifierKey){
        unset($this->modifiers[$modifierKey]);
    }

    public function existModifier($modifierKey) : bool {
        return isset($this->modifiers[$modifierKey]);
    }

    public function applyModifiers(){
        foreach ($this->modifiers as $modifierKey => $modifierValue){
            HTMLInterface::addAttribute($modifierKey, $modifierValue);
        }
    }

    public function merge(HTMLModifier | null $secondModifier) : static {
        $outModifier = clone $this;
        if($secondModifier) {
            foreach ($secondModifier->modifiers as $secAttrKey => $secAttrValue) {
                $outModifier->modifiers[$secAttrKey] = $secAttrValue;
            }
        }
        return $outModifier;
    }

    public function copiableModifier() : void {
        $this->modifiers["onclick"] = "avtCopyElementText(this);";
    }
}
