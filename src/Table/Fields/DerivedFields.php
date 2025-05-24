<?php
namespace Avetify\Table\Fields;

use Avetify\Components\NiceDiv;
use Avetify\Components\VertDiv;

class FieldsContainer extends TableField {
    /** @var TableField[] $childs */
    public array $childs = [];

    public function __construct(string $title, string $key, array $childs){
        parent::__construct($title, $key);
        $this->childs = $childs;
    }
}

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
