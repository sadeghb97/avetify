<?php
namespace Avetify\Table\Fields\FieldsContainers;

use Avetify\Components\Containers\VertDiv;

class ColumnFields extends FieldsContainer {
    public function presentValue($item) {
        $vertDiv = new VertDiv(4);
        $vertDiv->open();

        for($i=0; count($this->childs) > $i; $i++){
            $this->childs[$i]->presentValue($item);
            if(count($this->childs) > ($i + 1)) $vertDiv->separate();
        }

        $vertDiv->close();
    }
}
