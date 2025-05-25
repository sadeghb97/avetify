<?php
namespace Avetify\Table\Fields\NumberFields;

use Avetify\Interface\Styler;
use Avetify\Table\Fields\TableSimpleField;

class ExactColoredField extends TableSimpleField {
    public function __construct(string $title, string $key, public array $colors){
        parent::__construct($title, $key);
    }

    public function normalCellStyles($item){
        parent::normalCellStyles($item);
        $val = $this->getValue($item);

        $chosenColor = null;
        foreach ($this->colors as $colorTargetKey => $color){
            if($val == $colorTargetKey){
                $chosenColor = $color;
                break;
            }
        }

        if($chosenColor != null) {
            Styler::addStyle("background-color", $chosenColor);
        }
    }
}
