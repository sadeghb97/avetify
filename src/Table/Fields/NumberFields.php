<?php
namespace Avetify\Table\Fields;

use Avetify\Interface\Styler;
use function Avetify\Utils\formatMegaNumber;

class MegaNumberField extends TableSimpleField {
    public function presentValue($item){
        echo formatMegaNumber($this->getValue($item));
    }
}

class PercentField extends TableSimpleField {
    public function presentValue($item){
        echo $this->getValue($item) . '%';
    }
}

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
