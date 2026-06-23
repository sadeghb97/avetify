<?php
namespace Avetify\Fields;

use Avetify\DB\DBConnection;
use Avetify\Entities\AvtEntityItem;
use Avetify\Entities\EntityUtils;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\WebModifier;

class BaseRecordField implements IdentifiedElement {
    use IdentifiedElementTrait;
    const DYNAMIC_IDENTIFIER = "*$*";
    public WebModifier | null $baseModifier = null;

    public bool $isNumeric = false;
    public bool $nullOnEmpty = false;

    public array $dbValueMappers = [];

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

    public function getElementIdentifier($item = null) {
        $id = $this->key;
        if($item instanceof AvtEntityItem){
            $itemId = $item->getItemId();
            if($itemId) {
                $id .= "_";
                $id .= $itemId;
            }
        }
        return $id;
    }

    public function adjustDBValue(DBConnection $conn, string $value) : string | null {
        if(isset($this->dbValueMappers[$value])) return $this->dbValueMappers[$value];
        if($this->nullOnEmpty && !$value) return null;
        if($this->isNumeric && !$value) return "0";

        if(!$this->isNumeric) return $conn->real_escape_string($value);
        return $value;
    }

    public function placeField($item, ?WebModifier $webModifier = null) : void {
        $finalModifier = WebModifier::mergeModifiers($this->baseModifier, $webModifier);
        $this->presentValue($item, $finalModifier);
    }

    public function presentValue($item, ?WebModifier $webModifier = null){
        HTMLInterface::placeSpan($this->getValue($item), $webModifier);
    }

    public function removeBaseMargins(): static {
        $this->baseModifier->popStyle("margin-bottom");
        $this->baseModifier->popStyle("margin-top");
        $this->baseModifier->popStyle("margin-right");
        $this->baseModifier->popStyle("margin-left");
        return $this;
    }

    public function attachWebModifier(WebModifier $modifier) : static {
        $this->baseModifier = WebModifier::mergeModifiers($this->baseModifier, $modifier);
        return $this;
    }

    public function attachCssStyle(string $styleKey, string $styleValue) : static {
        $this->baseModifier->pushStyle($styleKey, $styleValue);
        return $this;
    }

    public function attachCssWidth(string $styleValue) : static {
        $this->baseModifier->pushStyle("width", $styleValue);
        return $this;
    }

    public function attachCssHeight(string $styleValue) : static {
        $this->baseModifier->pushStyle("height", $styleValue);
        return $this;
    }

    public function attachCssClass(string $className) : static {
        $this->baseModifier->pushClass($className);
        return $this;
    }

    public function attachHtmlModifier(string $modifierKey, string $modifierValue) : static {
        $this->baseModifier->pushModifier($modifierKey, $modifierValue);
        return $this;
    }

    public function detachCssStyle(string $styleKey) : static {
        $this->baseModifier->popStyle($styleKey);
        return $this;
    }

    public function detachCssClass(string $className) : static {
        $this->baseModifier->popClass($className);
        return $this;
    }

    public function detachHtmlModifier(string $modifierKey) : static {
        $this->baseModifier->popModifier($modifierKey);
        return $this;
    }

    public function getFinalModifier(WebModifier | null $extraModifier) : WebModifier {
        return WebModifier::mergeModifiers($this->baseModifier, $extraModifier);
    }
}
