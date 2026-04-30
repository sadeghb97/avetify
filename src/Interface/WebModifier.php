<?php
namespace Avetify\Interface;

use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLModifier;
use JetBrains\PhpStorm\Pure;

class WebModifier {
    public function __construct(public HTMLModifier | null $htmlModifier = null,
                                public Styler | null $styler = null){
    }

    #[Pure] public static function createInstance() : WebModifier {
        return new WebModifier(new HTMLModifier(), new Styler());
    }

    public function __clone() {
        $this->htmlModifier = $this->htmlModifier ? clone $this->htmlModifier : null;
        $this->styler = clone $this->styler ? $this->styler : null;
    }

    public function apply() : void {
        if($this->htmlModifier != null) $this->htmlModifier->applyModifiers();
        if($this->styler != null) {
            $this->styler->applyClasses();
            $this->styler->applyStyles();
        }
    }

    public function pushStyle(string $styleKey, string $styleValue) : void {
        if(!$this->styler) $this->styler = new Styler();
        $this->styler->pushStyle($styleKey, $styleValue);
    }

    public function pushClass(string $className) : void {
        if($this->styler) $this->styler->pushClass($className);
    }

    public function pushModifier(string $modifierKey, string $modifierValue) : void {
        if(!$this->htmlModifier) $this->htmlModifier = new HTMLModifier();
        $this->htmlModifier->pushModifier($modifierKey, $modifierValue);
    }

    public function popStyle(string $styleKey) : void {
        if(!$this->styler) $this->styler = new Styler();
        $this->styler->popStyle($styleKey);
    }

    public function popClass(string $className) : void {
        $this->styler?->popClass($className);
    }

    public function popModifier(string $modifierKey) : void {
        if(!$this->htmlModifier) $this->htmlModifier = new HTMLModifier();
        $this->htmlModifier->popModifier($modifierKey);
    }

    public function merge(WebModifier | null $secondModifier) : WebModifier {
        $outModifier = clone $this;
        if(!$outModifier->htmlModifier) $outModifier->htmlModifier = new HTMLModifier();
        if(!$outModifier->styler) $outModifier->styler = new Styler();

        if($secondModifier) {
            if ($secondModifier->htmlModifier) {
                $outModifier->htmlModifier = $outModifier->htmlModifier->merge($secondModifier->htmlModifier);
            }
            if ($secondModifier->styler) {
                $outModifier->styler = $outModifier->styler->merge($secondModifier->styler);
            }
        }

        return $outModifier;
    }

    public static function mergeModifiers(WebModifier | null $firstModifier, WebModifier | null $secondModifier) : WebModifier {
        $outModifier = $firstModifier ? clone $firstModifier : WebModifier::createInstance();
        return $outModifier->merge($secondModifier);
    }
}
