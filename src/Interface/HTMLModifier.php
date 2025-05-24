<?php
namespace Avetify\Interface;

use JetBrains\PhpStorm\Pure;

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

    #[Pure] public static function createInstance() : WebModifier {
        return new WebModifier(new HTMLModifier(), new Styler());
    }

    public function __clone() {
        $this->htmlModifier = clone $this->htmlModifier;
        $this->styler = clone $this->styler;
    }

    public function apply(){
        if($this->htmlModifier != null) $this->htmlModifier->applyModifiers();
        if($this->styler != null) $this->styler->applyStyles();
    }

    public function pushStyle(string $styleKey, string $styleValue){
        if($this->styler) $this->styler->pushStyle($styleKey, $styleValue);
    }

    public function pushModifier(string $modifierKey, string $modifierValue){
        if($this->htmlModifier) $this->htmlModifier->pushModifier($modifierKey, $modifierValue);
    }

    public function pushClass(string $className){
        if($this->styler) $this->styler->pushClass($className);
    }
}
