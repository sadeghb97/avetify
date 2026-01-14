<?php
namespace Avetify\Interface;
use Avetify\Entities\EntityUtils;

/*** Implements RecordField */
trait RecordFieldTrait {
    public int $maxFieldCharacters = 0;
    public string $title;
    public string $key;

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
}
