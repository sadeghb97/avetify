<?php
namespace Avetify\Table\Fields\FieldsContainers;

use Avetify\Components\Containers\NiceDiv;

class RowFields extends FieldsContainer {
    public function presentValue($item) {
        $niceDiv = new NiceDiv(4);
        $niceDiv->open();

        for($i=0; count($this->childs) > $i; $i++){
            $this->childs[$i]->presentValue($item);
            if(count($this->childs) > ($i + 1)) $niceDiv->separate();
        }

        $niceDiv->close();
    }
}
