<?php
namespace Avetify\Table\Fields\NumberFields;

use Avetify\Interface\Styler;
use Avetify\Table\Fields\TableSimpleField;

class ColoredField extends TableSimpleField {
    public function __construct(string $title, string $key, public array $colors){
        parent::__construct($title, $key);
    }

    public function normalCellStyles($item){
        parent::normalCellStyles($item);

        if($this->isQualified($item)) {
            $val = $this->getValue($item);

            $chosenColor = null;
            foreach ($this->colors as $colorBound => $color) {
                $chosenColor = $color;
                if ($val < $colorBound) break;
            }

            Styler::addStyle("background-color", $chosenColor);
        }
    }
}
