<?php
namespace Avetify\Fields\Containers;

use Avetify\Components\Containers\VertDiv;
use Avetify\Interface\WebModifier;

class ColumnFields extends FieldsContainer {
    public function presentValue($item, ?WebModifier $webModifier = null) {
        $vertDiv = new VertDiv(4);
        if(!$webModifier) $webModifier = WebModifier::createInstance();
        $webModifier->pushStyle("margin-bottom", "12px");
        $webModifier->pushStyle("margin-top", "12px");
        $webModifier->pushStyle("margin-right", "8px");
        $webModifier->pushStyle("margin-left", "8px");
        $vertDiv->open($webModifier);

        for($i=0; count($this->childs) > $i; $i++){
            $this->childs[$i]->presentValue($item);
            if(count($this->childs) > ($i + 1)) $vertDiv->separate();
        }

        $vertDiv->close();
    }
}
