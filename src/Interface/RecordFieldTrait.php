<?php
namespace Avetify\Interface;

use Avetify\Entities\EntityUtils;

trait RecordFieldTrait {
    public function getValue($item) : string {
        if(!$item) return "";
        if(!is_array($item) && !is_object($item)) return $item;
        if(str_contains($this->key, "~")) $finalKeys = explode("~", $this->key);
        else $finalKeys = $this->key;
        return EntityUtils::getSimpleValue($item, $finalKeys);
    }

    public function presentValue($item, ?WebModifier $webModifier = null){
        echo $this->getValue($item);
    }
}
