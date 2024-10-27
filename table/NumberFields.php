<?php

class MegaNumberField extends SBTableSimpleField {
    public function presentValue($item){
        echo formatMegaNumber($this->getValue($item));
    }
}

class PercentField extends SBTableSimpleField {
    public function presentValue($item){
        echo $this->getValue($item) . '%';
    }
}
