<?php
namespace Avetify\Fields\Containers;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Interface\Pout;
use Avetify\Interface\WebModifier;

class RowFields extends FieldsContainer {
    public function presentValue($item, ?WebModifier $webModifier = null) {
        $finalModifier = $this->getFinalModifier($webModifier);
        $niceDiv = new NiceDiv($this->sepSize);
        $niceDiv->open($finalModifier);

        for($i=0; count($this->childs) > $i; $i++){
            $this->childs[$i]->presentValue($item);
            if(count($this->childs) > ($i + 1)) $niceDiv->separate();
        }

        $niceDiv->close();
    }
}
