<?php
namespace Avetify\Fields\Containers;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Interface\WebModifier;

class RowFields extends FieldsContainer {
    public function presentValue($item, ?WebModifier $webModifier = null) {;
        $niceDiv = new NiceDiv($this->sepSize);
        if(!$webModifier) $webModifier = WebModifier::createInstance();
        $webModifier->pushStyle("margin-bottom", "12px");
        $webModifier->pushStyle("margin-top", "12px");
        $webModifier->pushStyle("margin-right", "8px");
        $webModifier->pushStyle("margin-left", "8px");
        $niceDiv->open($webModifier);

        for($i=0; count($this->childs) > $i; $i++){
            $this->childs[$i]->presentValue($item);
            if(count($this->childs) > ($i + 1)) $niceDiv->separate();
        }

        $niceDiv->close();
    }
}
