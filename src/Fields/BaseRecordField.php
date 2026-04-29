<?php
namespace Avetify\Fields;

use Avetify\Entities\EntityUtils;
use Avetify\Interface\WebModifier;

class BaseRecordField {
    public WebModifier | null $baseModifier = null;

    public function __construct(public string $key, public string $title){
        if(!$this->baseModifier) $this->baseModifier = WebModifier::createInstance();
    }

    public int $maxFieldCharacters = 0;

    public function getValue($item) : string {
        if(!$item) return "";
        if(!is_array($item) && !is_object($item)) return $item;
        if(str_contains($this->key, "~")) $finalKeys = explode("~", $this->key);
        else $finalKeys = $this->key;
        $foundValue = EntityUtils::getSimpleValue($item, $finalKeys);
        if($this->maxFieldCharacters <= 0 || $this->maxFieldCharacters >= strlen($foundValue)) return $foundValue;
        return substr($foundValue, 0, $this->maxFieldCharacters) . " ...";
    }

    public function presentValue($item, ?WebModifier $webModifier = null){
        echo $this->getValue($item);
    }

    public function removeBaseMargins(): self {
        $this->baseModifier->popStyle("margin-bottom");
        $this->baseModifier->popStyle("margin-top");
        $this->baseModifier->popStyle("margin-right");
        $this->baseModifier->popStyle("margin-left");
        return $this;
    }

    public function attachWebModifier(WebModifier $modifier) : self {
        $this->baseModifier = WebModifier::mergeModifiers($this->baseModifier, $modifier);
        return $this;
    }

    public function attachCssStyle(string $styleKey, string $styleValue) : self {
        $this->baseModifier->pushStyle($styleKey, $styleValue);
        return $this;
    }

    public function attachCssClass(string $className) : self {
        $this->baseModifier->pushClass($className);
        return $this;
    }

    public function attachHtmlModifier(string $modifierKey, string $modifierValue) : self {
        $this->baseModifier->pushModifier($modifierKey, $modifierValue);
        return $this;
    }

    public function detachCssStyle(string $styleKey) : self {
        $this->baseModifier->popStyle($styleKey);
        return $this;
    }

    public function detachCssClass(string $className) : self {
        $this->baseModifier->popClass($className);
        return $this;
    }

    public function detachHtmlModifier(string $modifierKey) : self {
        $this->baseModifier->popModifier($modifierKey);
        return $this;
    }

    public function getFinalModifier(WebModifier | null $extraModifier) : WebModifier {
        return WebModifier::mergeModifiers($this->baseModifier, $extraModifier);
    }
}
