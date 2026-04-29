<?php
namespace Avetify\Fields\Containers;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Interface\Pout;
use Avetify\Interface\WebModifier;

class RowFields extends FieldsContainer {
    public function presentValue($item, ?WebModifier $webModifier = null) {
        $niceDiv = new NiceDiv($this->sepSize);
        $niceDiv->open($webModifier);

        for($i=0; count($this->childs) > $i; $i++){
            $this->childs[$i]->placeField($item);
            if(count($this->childs) > ($i + 1)) $niceDiv->separate();
        }

        $niceDiv->close();
    }
}
