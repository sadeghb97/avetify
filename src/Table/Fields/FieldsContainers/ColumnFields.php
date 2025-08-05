<?php
namespace Avetify\Table\Fields\FieldsContainers;

use Avetify\Components\Containers\VertDiv;
use Avetify\Interface\WebModifier;

class ColumnFields extends FieldsContainer {
    public function presentValue($item, ?WebModifier $webModifier = null) {
        $vertDiv = new VertDiv(4);
        $vertDiv->open($webModifier);

        for($i=0; count($this->childs) > $i; $i++){
            $this->childs[$i]->presentValue($item);
            if(count($this->childs) > ($i + 1)) $vertDiv->separate();
        }

        $vertDiv->close();
    }
}
