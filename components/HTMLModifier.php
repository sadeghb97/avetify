<?php

class HTMLModifier {
    public array $modifiers = [];

    public function pushModifier($modifierKey, $modifierValue){
        $this->modifiers[$modifierKey] = $modifierValue;
    }

    public function applyModifiers(){
        foreach ($this->modifiers as $modifierKey => $modifierValue){
            HTMLInterface::addAttribute($modifierKey, $modifierValue);
        }
    }
}

class WebModifier {
    public function __construct(public HTMLModifier | null $htmlModifier = null,
                                public Styler | null $styler = null){
    }

    public function apply(){
        if($this->htmlModifier != null) $this->htmlModifier->applyModifiers();
        if($this->styler != null) $this->styler->applyStyles();
    }
}
